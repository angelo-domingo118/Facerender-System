<div 
    x-data="{ 
        activeTab: $persist('{{ isset($active) ? $active : collect($attributes->getIterator())->first() }}').as('active-tab'),
        tabHeadings: [],
        tabContents: [],
        focusables: [],
        tabs: document.querySelectorAll('.tab'), 
        init: function() {
            // Set tab active tab based on hash or first tab
            let hash = window.location.hash.substr(1);
            if(hash && this.tabHeadings.includes(hash)) {
                this.activeTab = hash;
            }
            
            // Set active tab
            this.setActiveTab(this.activeTab);
        },
        setActiveTab(tab) {
            this.activeTab = tab;
            this.$nextTick(() => this.updateTabButtonFocus())
        },
        selectTab(tab) {
            this.setActiveTab(tab);
        },
        updateTabButtonFocus() {
            const button = this.$refs[`tab-button-${this.activeTab}`];
            button && button.focus();
        },
        handleKeydown(event) {
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                event.preventDefault();
                const currentIndex = this.tabHeadings.indexOf(this.activeTab);
                const newIndex = event.key === 'ArrowLeft' 
                    ? (currentIndex - 1 + this.tabHeadings.length) % this.tabHeadings.length 
                    : (currentIndex + 1) % this.tabHeadings.length;
                this.setActiveTab(this.tabHeadings[newIndex]);
            }
        }
    }"
    role="tablist"
    class="w-full"
    @keydown="handleKeydown($event)"
>
    <!-- Tab List -->
    <div class="flex border-b border-gray-200 mb-4 overflow-x-auto">
        <template x-for="(tab, index) in tabHeadings" :key="index">
            <button
                :id="`tab-${tab}`"
                :class="{ 'border-indigo-500 text-indigo-600': activeTab === tab, 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== tab }"
                class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                @click="selectTab(tab)"
                :aria-selected="activeTab === tab"
                :tabindex="activeTab === tab ? 0 : -1"
                role="tab"
                :aria-controls="`tabpanel-${tab}`"
                :x-ref="`tab-button-${tab}`"
            >
                <div class="flex items-center space-x-2">
                    <template x-if="$refs[`icon-${tab}`]">
                        <span x-html="$refs[`icon-${tab}`].innerHTML"></span>
                    </template>
                    <span x-text="$refs[`label-${tab}`] ? $refs[`label-${tab}`].innerText : tab"></span>
                </div>
            </button>
        </template>
    </div>

    <!-- Tab Panels -->
    <div class="w-full">
        {{ $slot }}
    </div>
</div> 