@props([
    'id',
    'label' => '',
    'model',
    'options' => [],
    'placeholder' => 'Select an option',
    'required' => false
])

<div x-data="dropdownComponent({
    entangledModel: @entangle($model),
    options: @js($options),
    placeholder: @js($placeholder),
    required: {{ $required ? 'true' : 'false' }}
})" class="relative">
    
    <label for="{{ $id }}" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>

    <div class="relative mt-1">
        <input 
            type="text"
            :id="'search-' + $id"
            x-model="search"
            @click="toggleList()"
            @keydown.escape="closeList()"
            @click.away="closeList()"
            :placeholder="placeholder"
            class="form-control w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300
                     focus:border-gray-500 dark:focus:border-gray-600
                     focus:ring-gray-500 dark:focus:ring-gray-600
                     rounded-md shadow-sm" autocomplete="off"/>

        <!-- Dropdown icon -->
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-300" 
             fill="none" stroke="currentColor" 
             viewBox="0 0 24 24" stroke-width="4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </div>

    <!-- Hidden select to bind selectedId to Livewire -->
    <select x-model="selectedId" id="{{ $id }}" name="{{ $model }}" class="hidden">
        <template x-for="option in options" :key="option.id">
            <option :value="option.id" x-text="option.number"></option>
        </template>
    </select>

    <!-- Dropdown list -->
    <ul x-show="showList && filteredOptions.length > 0" 
        class="absolute z-10 bg-white border w-full mt-1 rounded shadow max-h-60 overflow-y-auto">
        <template x-for="option in filteredOptions" :key="option.id">
            <li @click="select(option)" 
                class="px-3 py-2 hover:bg-gray-100 cursor-pointer" 
                x-text="option.number">
            </li>
        </template>
    </ul>

    <!-- No results message -->
    <div x-show="showList && filteredOptions.length === 0" 
         class="mt-1 text-gray-500 px-3 py-2">
        No results found
    </div>

    <!-- Livewire input error -->
    <x-input-error :for="$model" class="mt-2" />

    <div x-show="showValidationError" class="text-red-500 text-sm mt-2">
        Please select a valid option from the list.
    </div>
</div>

@once
@push('scripts')
<script>
function dropdownComponent({ entangledModel, options, placeholder, required }) {
    return {
        search: '',
        selectedId: entangledModel,
        options,
        placeholder,
        required,
        showList: false,
        showValidationError: false, // NEW flag

        get filteredOptions() {
            return this.options.filter(option =>
                option.number.toLowerCase().includes(this.search.toLowerCase())
            );
        },

        toggleList() {
            this.showList = !this.showList;
        },

        closeList() {
            this.showList = false;
        },

        select(option) {
            this.selectedId = option.id;
            this.search = option.number;
            this.showValidationError = false; // reset on valid selection
            this.showList = false;
        },

        isValidSelection() {
            const match = this.options.find(opt =>
                opt.id == this.selectedId && opt.number.toLowerCase().trim() === this.search.toLowerCase().trim()
            );
            return !!match;
        },

        init() {
            // Set initial value
            const selected = this.options.find(opt => opt.id == this.selectedId);
            if (selected) {
                this.search = selected.number;
            }

            // Debounce search syncing to avoid flicker
            let debounceTimer;

            this.$watch('search', (value) => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const match = this.options.find(opt =>
                        opt.number.toLowerCase().trim() === value.toLowerCase().trim()
                    );
                    this.selectedId = match ? match.id : null;

                    // Show validation error only if user typed something invalid
                    this.showValidationError = value.length > 0 && !match;
                }, 150);
            });

            this.$watch('selectedId', (id) => {
                const selected = this.options.find(opt => opt.id == id);
                if (selected) {
                    this.search = selected.number;
                }
            });

            // Handle Livewire form submit edge case
            const form = this.$root.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    const match = this.options.find(opt =>
                        opt.number.toLowerCase().trim() === this.search.toLowerCase().trim()
                    );
                    if (match) {
                        this.selectedId = match.id;
                        this.showValidationError = false; // suppress warning on submit
                    }
                });
            }
        }
    };
}
</script>
@endpush
@endonce
