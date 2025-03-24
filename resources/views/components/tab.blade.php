<div 
    x-data="{
        name: '{{ $name }}',
        label: '{{ $label ?? $name }}',
        icon: '{{ $icon ?? '' }}',
        init() {
            // Add tab to parent tabHeadings
            const parent = this.$el.closest('[role=tablist]').__x.$data;
            parent.tabHeadings.push(this.name);
            this.$nextTick(() => {
                // Register label reference for tab
                if (this.$refs.labelContainer) {
                    parent.$refs[`label-${this.name}`] = this.$refs.labelContainer;
                }
                // Register icon reference for tab if exists
                if (this.$refs.iconContainer && this.$refs.iconContainer.innerHTML.trim() !== '') {
                    parent.$refs[`icon-${this.name}`] = this.$refs.iconContainer;
                }
            });
        }
    }"
    :id="`tabpanel-${name}`"
    role="tabpanel"
    :aria-labelledby="`tab-${name}`"
    :hidden="name !== $parent.activeTab"
    tabindex="0"
    class="focus:outline-none"
>
    <div x-ref="labelContainer" class="hidden">{{ $label ?? $name }}</div>
    
    @if ($icon)
    <div x-ref="iconContainer" class="hidden">
        <x-icon :name="$icon" class="w-5 h-5" />
    </div>
    @endif
    
    <div x-show="name === $parent.activeTab" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        {{ $slot }}
    </div>
</div> 