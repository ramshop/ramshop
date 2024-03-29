<?php

namespace App\Telegram\Conversations;

use App\Enums\OrderStatus;
use App\Jobs\AdminUpdatedOrder;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class OrderManageConversation extends InlineMenu
{
    protected Order $order;
    protected OrderStatus $status;

    public function start(Nutgram $bot)
    {
        try {
            $this->order = $bot->getUserData(Order::class, $bot->userId());
            $this->status = $this->order->status;
        } catch (\Throwable $e) {
            $bot->sendMessage("Server Error. Please try again.");

            Log::critical("{class} Error in line {line}: {message}", [
                "class" => self::class,
                "line" => $e->getLine(),
                "message" => $e->getMessage(),
            ]);
            $this->end();
            return null;
        }

        $text = message("order-detail", ["order" => $this->order]);

        $this->clearButtons()
            ->menuText($text, ["parse_mode" => ParseMode::HTML])
            ->addButtonRow(
                InlineKeyboardButton::make(
                    __("order.update_shipping_amount"),
                    callback_data: "order@updateShippingAmount"
                ),
                InlineKeyboardButton::make(
                    __("order.update_status"),
                    callback_data: "order@updateStatus"
                )
            )
            ->addButtonRow(
                InlineKeyboardButton::make(
                    __("order.cancel"),
                    callback_data: "order@askCancelOrder"
                ),
                InlineKeyboardButton::make(
                    __("order.chat_with_user"),
                    "tg://user?id=" . $this->order->customer->id
                )
            )
            ->addButtonRow(
                InlineKeyboardButton::make(
                    __("order.close"),
                    callback_data: "order@end"
                )
            )
            ->showMenu();
    }

    protected function updateShippingAmount(Nutgram $bot)
    {
        $this->clearButtons()
            ->menuText(__("order.update_shipping_amount_send"))
            ->orNext("setShippingAmount");

        $this->showMenu();
    }

    protected function setShippingAmount(Nutgram $bot)
    {
        if ($bot->message()?->text === null) {
            $bot->sendMessage(__("order.invalid_value"));
            $this->updateShippingAmount($bot);
        }

        $this->order->shipping_amount = intval($bot->message()->text);
        $this->order->total_amount = $this->order->items_sum_amount + $this->order->shipping_amount;
        $this->order->save();

        AdminUpdatedOrder::dispatch($this->order, null, __("order.shipping_amount"));

        $this->start($bot);
    }

    protected function updateStatus(Nutgram $bot)
    {
        $text = __("order.update_status_send", [
            "old" => $this->status->getTrans(),
            "new" => $this->order->status->getTrans(),
        ]);
        $this->clearButtons()
            ->menuText($text, ["parse_mode" => ParseMode::HTML])
            ->addButtonRow(
                InlineKeyboardButton::make(
                    OrderStatus::PROCESSING->getTrans(),
                    callback_data: "processing@setOrderStatus"
                )
            )
            ->addButtonRow(
                InlineKeyboardButton::make(
                    OrderStatus::SHIPPED->getTrans(),
                    callback_data: "shipped@setOrderStatus"
                )
            )
            ->addButtonRow(
                InlineKeyboardButton::make(
                    OrderStatus::COMPLETED->getTrans(),
                    callback_data: "completed@setOrderStatus"
                )
            )
            ->addButtonRow(
                InlineKeyboardButton::make(
                    __("order.back"),
                    callback_data: "order@start"
                )
            )
            ->showMenu();
    }

    protected function setOrderStatus(Nutgram $bot)
    {
        $this->order->status = OrderStatus::from($bot->callbackQuery()->data);
        $this->status = $this->order->getOriginal("status");
        $this->order->save();

        AdminUpdatedOrder::dispatch($this->order, null, __("order.status"));

        $this->updateStatus($bot);
    }

    protected function askCancelOrder(Nutgram $bot)
    {
        if ($this->order->status == OrderStatus::CANCELLED) {
            $bot->sendMessage("Order can't be canceled.");
            $this->end();
            return null;
        }

        $this->clearButtons()
            ->menuText(__("order.cancelled_ask"), [
                "parse_mode" => ParseMode::HTML,
            ])
            ->orNext("cancelOrder")
            ->showMenu(true);
    }

    protected function cancelOrder(Nutgram $bot)
    {
        $this->order->status = OrderStatus::CANCELLED;
        $this->order->save();

        AdminUpdatedOrder::dispatch($this->order, $bot->messageId());

        $bot->sendMessage("Order cancelled");
        $this->end();
    }
}
