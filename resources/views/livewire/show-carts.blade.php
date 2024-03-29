<div class="p-2 bg-white dark:bg-[#04293A] text-slate-900 dark:text-white">
    <div class="flex justify-between items-center">
        <h3 class="text-base font-semibold uppercase">{{ __('frontend.your_cart') }}</h3>
        <div x-data>
            <a class="text-blue-400 inline-flex items-center mr-3" href="{{ route('frontend.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                {{ __('frontend.edit') }}
            </a>
            <button class="text-red-600 inline-flex items-center" @click="Telegram.WebApp.HapticFeedback.impactOccurred('soft'); $wire.clear()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                {{ __('frontend.clear') }}
            </button>
        </div>
    </div>
    <div class="my-3">
        @foreach ($cart as $item)
        <div class="flex justify-between my-2">
            @if($item->product['image'] != null && Illuminate\Support\Facades\Storage::disk("products")->exists($item->product['image']))
                <img class="aspect-[4/3] object-contain w-1/5 flex-none" src="{{ Illuminate\Support\Facades\Storage::disk('products')->url($item->product['image']) }}" alt="{{ $item->product['name'] }}">
            @else
                <img class="aspect-[4/3] object-contain w-1/5 flex-none" src="{{ '/storage/default.jpg' }}" alt="{{ $item->product['name'] }}">
            @endif
            <div class="flex flex-col pl-1 grow">
                <h3 class="font-bold">{{ $item->product['name'] }} - {{ $item->product['code'] }} <span class="text-[#E45826] ml-2">x{{ $item->quantity }}</span></h3>
                <p class="text-[#00092C] dark:text-gray-300 text-xs line-clamp-1 overflow-hidden text-ellipsis">{!! $item->product['description'] !!}</p>
            </div>
            <div class="font-oswald">{{ format_currency($item->amount) }}</div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script type="application/javascript">
    document.addEventListener("DOMContentLoaded", () => {
        // Collect user information
        Telegram.WebApp.initData && Livewire.emit('tg:initData', Telegram.WebApp.initData);
        // Show backbutton
        Telegram.WebApp.BackButton.isVisible = true;
        Telegram.WebApp.onEvent('backButtonClicked', () => {
            Telegram.WebApp.HapticFeedback.impactOccurred('medium');
            window.location.href = "{{ route('frontend.index') }}";
        });
        // Show mainbutton
        Telegram.WebApp.MainButton.setParams({
            text: "{{ Str::upper(__('frontend.order')) . ' ' . money($subtotal, convert: true) }}",
            text_color: "#EEEEEE",
            color: "#F0A500",
            is_active: true,
            is_visible: true,
        }).onClick(() => {
            window.location.href = "/webapp/order-placed";
        });
    });
</script>
@endpush
