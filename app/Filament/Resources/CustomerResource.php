<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use SergiX44\Nutgram\Telegram\Properties\ChatType;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationGroup = "Shop";
    protected static ?string $recordTitleAttribute = "name";
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = "heroicon-o-user-group";

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make("id")->disabled(),
                        TextInput::make("name")
                            ->label(__("customer.name"))
                            ->maxLength(255)
                            ->required(),
                        TextInput::make("phone")
                            ->label(__("customer.phone"))
                            ->maxLength(12)
                            ->tel(),
                        TextInput::make("username")
                            ->label(__("customer.username"))
                            ->minLength(5),
                    ])
                    ->columns(2)
                    ->columnSpan([
                        "lg" => fn(?Customer $record) => $record === null
                            ? 3
                            : 2,
                    ]),
                Card::make()
                    ->schema([
                        Placeholder::make("created_at")
                            ->label(__("customer.created_at"))
                            ->content(
                                fn(
                                    Customer $record
                                ): ?string => $record->created_at?->diffForHumans()
                            ),

                        Placeholder::make("updated_at")
                            ->label(__("customer.updated_at"))
                            ->content(
                                fn(
                                    Customer $record
                                ): ?string => $record->updated_at?->diffForHumans()
                            ),
                        Placeholder::make("started_at")
                            ->label(__("customer.started_at"))
                            ->content(
                                fn(
                                    Customer $record
                                ): ?string => $record->started_at?->diffForHumans()
                            ),
                        Placeholder::make("blocked_at")
                            ->label(__("customer.blocked_at"))
                            ->content(
                                fn(
                                    Customer $record
                                ): ?string => $record->blocked_at?->diffForHumans()
                            ),
                    ])
                    ->columnSpan(1)
                    ->hidden(fn(?Customer $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id")->sortable(),
                TextColumn::make("name")
                    ->label(__("customer.name"))
                    ->weight("medium")
                    ->copyable()
                    ->searchable(),
                TextColumn::make("phone")
                    ->label(__("customer.phone"))
                    ->searchable()
                    ->copyable(),
                TextColumn::make("username")
                    ->label("Telegram")
                    ->icon("heroicon-o-link")
                    ->url(fn($record) => $record->getTelegramUrl(), true)
                    ->default("Unknown")
                    ->searchable(),
                TextColumn::make("language_code")->label(
                    __("customer.language_code")
                ),
                TextColumn::make("type"),
                TextColumn::make("created_at")
                    ->label(__("customer.created_at"))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort("created_at", "desc")
            ->filters([SelectFilter::make("type")->options(ChatType::class)])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getRelations(): array
    {
        return [OrdersRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListCustomers::route("/"),
            "edit" => Pages\EditCustomer::route("/{record}/edit"),
        ];
    }

    public static function getLabel(): ?string
    {
        return __("customer.label");
    }
}
