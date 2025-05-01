/**
 * Livewire Sortable Component for Layer Panel
 * Adds drag-and-drop functionality to reorder layers
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if Sortable library exists
    if (typeof Sortable === 'undefined') {
        // Load Sortable.js from CDN if not available
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
        script.onload = initSortable;
        document.head.appendChild(script);
    } else {
        initSortable();
    }

    function initSortable() {
        let el = document.querySelector('[wire\\:sortable]');
        if (!el) {
            console.warn('No sortable element found');
            return;
        }

        // Extract the sortable method name
        const sortableMethodName = el.getAttribute('wire:sortable');

        // Initialize Sortable
        new Sortable(el, {
            animation: 150,
            ghostClass: 'bg-gray-100',
            handle: '[wire\\:sortable\\.handle]',
            onEnd: function(evt) {
                // Get all items
                const items = Array.from(el.querySelectorAll('[wire\\:sortable\\.item]')).map(item => {
                    return item.getAttribute('wire:sortable.item');
                });

                // Call the Livewire method with the new order
                if (window.Livewire) {
                    window.Livewire.find(el.closest('[wire\\:id]').getAttribute('wire:id'))
                        .call(sortableMethodName, items);
                }
            }
        });

        console.log('Layer sortable initialized');
    }
}); 