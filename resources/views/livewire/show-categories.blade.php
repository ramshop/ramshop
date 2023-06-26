<div class="mb-4">
    <ul class="flex -mb-px text-center flex-nowrap scroll-smooth scrollbar-hidden overflow-x-scroll">
        <li class="flex-auto text-center mx-2">
            <button wire:click="$emit('categoryClicked')"
                class="w-full inline-block pb-3 pt-1 border-b-2 rounded-t-lg whitespace-nowrap border-transparent focus:border-blue-600 focus:tg-link-color"
            >
                All
            </button>
        </li>
        @foreach ($categories as $category)
        <li class="flex-auto text-center mx-2">
            <button wire:click="$emit('categoryClicked', {{ $category->id }})"
                class="w-full inline-block pb-3 pt-1 border-b-2 rounded-t-lg whitespace-nowrap border-transparent focus:border-blue-600 focus:tg-link-color"
            >
                {{ $category->name }}
            </button>
        </li>
        @endforeach
    </ul>
</div>
