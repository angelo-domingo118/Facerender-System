/**
 * Livewire Sortable Component for Layer Panel
 * Adds drag-and-drop functionality to reorder layers
 */

import Sortable from 'sortablejs';

document.addEventListener('livewire:initialized', () => {
    let sortables = [];

    function initSortable() {
        document.querySelectorAll('[wire\\:sortable]').forEach(el => {
            const component = Livewire.find(el.closest('[wire\\:id]')?.getAttribute('wire:id'));
            
            if (!component) return;
            
            const methodName = el.getAttribute('wire:sortable');
            
            // Destroy existing sortable instance if it exists
            if (el._sortable) {
                el._sortable.destroy();
            }
            
            // Initialize SortableJS
            el._sortable = Sortable.create(el, {
                draggable: '[wire\\:sortable\\.item]',
                handle: '[wire\\:sortable\\.handle]',
                animation: 150,
                onEnd: function(evt) {
                    // Get all item IDs in order
                    const items = Array.from(el.querySelectorAll('[wire\\:sortable\\.item]'));
                    const ids = items.map(item => item.getAttribute('wire:sortable.item'));
                    
                    // Call the method with the ordered IDs
                    component.call(methodName, ids);
                }
            });
            
            sortables.push(el._sortable);
        });
    }
    
    // Initialize sortable on page load
    initSortable();
    
    // Re-initialize on Livewire updates
    document.addEventListener('livewire:update', initSortable);
}); 