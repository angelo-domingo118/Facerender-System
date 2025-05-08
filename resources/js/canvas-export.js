/**
 * Canvas Export Functionality
 * Implements print and download for the Fabric.js canvas
 */

// Add a global flag to control console logging
const DEBUG_LOGS = false;

document.addEventListener('DOMContentLoaded', function() {
    if (DEBUG_LOGS) {
        console.log('Canvas export module loaded');
    } else {
        // Keep a minimal console log for important events
        console.log('Canvas export module loaded');
    }
    
    // Wait until the canvas is ready
    window.addEventListener('canvas:initialized', function(event) {
        const canvas = event.detail.canvas;
        if (DEBUG_LOGS) {
            console.log('Canvas export module detected initialized canvas:', canvas);
        }
        
        // Set up print button
        const printButton = document.getElementById('print-canvas-btn');
        if (printButton) {
            if (DEBUG_LOGS) {
                console.log('Print button found, attaching event listener');
            }
            printButton.addEventListener('click', function() {
                printCanvas(canvas);
            });
        } else {
            console.warn('Print button not found with ID: print-canvas-btn');
            // Try with the tooltip selector as fallback
            const fallbackPrintButton = document.querySelector('button[x-tooltip="\'Print (Ctrl+P)\'"]');
            if (fallbackPrintButton) {
                console.log('Print button found using fallback selector, attaching event listener');
                fallbackPrintButton.addEventListener('click', function() {
                    printCanvas(canvas);
                });
            }
        }
        
        // Set up download button
        const downloadButton = document.getElementById('download-canvas-btn');
        if (downloadButton) {
            console.log('Download button found, attaching event listener');
            downloadButton.addEventListener('click', function() {
                showExportModal(canvas);
            });
        } else {
            console.warn('Download button not found with ID: download-canvas-btn');
            // Try with the tooltip selector as fallback
            const fallbackDownloadButton = document.querySelector('button[x-tooltip="\'Download\'"]');
            if (fallbackDownloadButton) {
                console.log('Download button found using fallback selector, attaching event listener');
                fallbackDownloadButton.addEventListener('click', function() {
                    showExportModal(canvas);
                });
            }
        }
        
        // Set up export modal controls
        setupExportModalControls(canvas);
    });
});

/**
 * Set up the export modal controls
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
function setupExportModalControls(canvas) {
    // Get modal elements
    const modal = document.getElementById('export-options-modal');
    const closeBtn = document.getElementById('close-export-modal');
    const cancelBtn = document.getElementById('cancel-export');
    const confirmBtn = document.getElementById('confirm-export');
    const fileFormatInputs = document.querySelectorAll('input[name="file-format"]');
    const transparencyOption = document.getElementById('transparency-option');
    const qualityOption = document.getElementById('quality-option');
    const qualitySlider = document.getElementById('quality-slider');
    const qualityValue = document.getElementById('quality-value');
    
    if (!modal) {
        console.error('Export modal not found in the DOM');
        return;
    }
    
    // Close modal functions
    const closeModal = () => {
        modal.classList.add('hidden');
    };
    
    // Close modal event listeners
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    
    // Toggle options based on file format
    fileFormatInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'png') {
                transparencyOption.classList.remove('hidden');
                qualityOption.classList.add('hidden');
            } else {
                transparencyOption.classList.add('hidden');
                qualityOption.classList.remove('hidden');
            }
        });
    });
    
    // Update quality value
    if (qualitySlider && qualityValue) {
        qualitySlider.addEventListener('input', function() {
            qualityValue.textContent = this.value;
        });
    }
    
    // Handle export confirmation
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            // Get selected options
            const fileFormat = document.querySelector('input[name="file-format"]:checked').value;
            const keepTransparency = document.querySelector('input[name="transparency"]:checked').value === 'keep';
            const showGrid = document.querySelector('input[name="grid"]:checked').value === 'show';
            const quality = parseInt(document.getElementById('quality-slider').value) / 100;
            const exportSize = parseFloat(document.getElementById('export-size').value);
            
            // Perform export with options
            exportCanvas(canvas, {
                format: fileFormat,
                quality: quality,
                multiplier: exportSize,
                keepTransparency: keepTransparency,
                showGrid: showGrid
            });
            
            // Close modal
            closeModal();
        });
    }
}

/**
 * Show the export options modal
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
function showExportModal(canvas) {
    const modal = document.getElementById('export-options-modal');
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        console.error('Export modal not found, falling back to direct download');
        downloadCanvas(canvas);
    }
}

/**
 * Export the canvas with the specified options
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 * @param {Object} options - Export options
 */
function exportCanvas(canvas, options) {
    try {
        console.log('Export canvas with options:', options);
        
        if (!canvas) {
            console.error('Cannot export - canvas is not defined');
            return;
        }
        
        // Save the current state of the canvas
        const originalObjects = [...canvas.getObjects()];
        const gridLines = originalObjects.filter(obj => 
            obj.type === 'line' && 
            obj.stroke === '#e5e7eb' && 
            !obj.selectable && 
            !obj.evented
        );
        
        // If we need to hide the grid, temporarily remove grid lines
        if (!options.showGrid && gridLines.length > 0) {
            console.log(`Temporarily hiding ${gridLines.length} grid lines for export`);
            gridLines.forEach(line => canvas.remove(line));
        }
        
        // Set background color for PNG transparency removal if needed
        const originalBackgroundColor = canvas.backgroundColor;
        if (options.format === 'png') {
            if (options.keepTransparency) {
                // Set background to transparent for PNG export
                canvas.backgroundColor = null; // or '' could also work
                console.log('Setting canvas background to transparent for PNG export');
            } else {
                // Set background to white if transparency is not kept
                canvas.backgroundColor = '#ffffff';
                console.log('Setting canvas background to white for PNG export');
            }
        } else {
            // For JPEG, always use a white background
            canvas.backgroundColor = '#ffffff';
            console.log('Setting canvas background to white for JPEG export');
        }
        
        // Force canvas re-rendering with the potential new background color
        canvas.renderAll();
        
        // Get composite title if available for the filename
        let filenameBase = 'canvas-export';
        const titleElement = document.querySelector('.composite-title, h2[title]');
        if (titleElement) {
            const rawTitle = titleElement.textContent || '';
            // Clean up the title for a filename
            filenameBase = rawTitle.trim()
                .replace(/[^a-z0-9]/gi, '-') // Replace non-alphanumeric with dash
                .replace(/-+/g, '-')        // Replace multiple dashes with single
                .toLowerCase();
        }
        
        // Generate a filename with date
        const now = new Date();
        const timestamp = now.toISOString().replace(/[:.]/g, '-').substring(0, 19);
        const filename = `${filenameBase}-${timestamp}.${options.format}`;
        
        // Create a data URL of the canvas
        const dataUrl = canvas.toDataURL({
            format: options.format,
            quality: options.quality,
            multiplier: options.multiplier
        });
        
        // Create a temporary link element and trigger the download
        const link = document.createElement('a');
        link.download = filename;
        link.href = dataUrl;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        console.log('Canvas exported successfully as:', filename);
        
        // Restore canvas state
        if (!options.showGrid && gridLines.length > 0) {
            console.log('Restoring grid lines after export');
            gridLines.forEach(line => canvas.add(line));
            // Send grid lines to back
            gridLines.forEach(line => canvas.sendObjectToBack(line));
        }
        
        // Restore original background color
        canvas.backgroundColor = originalBackgroundColor;
        console.log('Restored original canvas background color:', originalBackgroundColor);
        
        // Re-render the canvas
        canvas.renderAll();
        
    } catch (error) {
        console.error('Error during canvas export:', error);
        alert('Failed to export the canvas. Please try again.');
    }
}

/**
 * Print the canvas content, including composite details
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
async function printCanvas(canvas) {
    try {
        console.log('Print canvas function called');
        
        // Check if canvas exists
        if (!canvas) {
            console.error('Cannot print - canvas is not defined');
            return;
        }
        
        // Find the parent Livewire component (CompositeEditor)
        const selector = `[wire\\:id][wire\\:initial-data*="App\\\\Livewire\\\\Editor\\\\CompositeEditor"]`;
        const editorComponentElement = document.querySelector(selector);
        let compositeDetails = {};
        let detailsFound = false;
        
        // First try with CompositeEditor component
        if (editorComponentElement && typeof Livewire !== 'undefined') {
            const editorComponentId = editorComponentElement.getAttribute('wire:id');
            console.log('Found Livewire component element with ID:', editorComponentId);
            const component = Livewire.find(editorComponentId);
            if (component) {
                console.log('Fetching composite details from Livewire...');
                try {
                    compositeDetails = await component.call('getCompositeDetailsForPrint');
                    console.log('Fetched details:', compositeDetails);
                    detailsFound = Object.keys(compositeDetails).length > 0;
                } catch (error) {
                    console.error('Error fetching composite details from Livewire:', error);
                }
            }
        }
        
        // If that didn't work, try with MainToolbar component
        if (!detailsFound && typeof Livewire !== 'undefined') {
            console.log('Trying to find MainToolbar component...');
            const toolbarSelector = `[wire\\:id][wire\\:initial-data*="App\\\\Livewire\\\\Editor\\\\MainToolbar"]`;
            const toolbarElement = document.querySelector(toolbarSelector);
            
            if (toolbarElement) {
                const toolbarComponentId = toolbarElement.getAttribute('wire:id');
                console.log('Found MainToolbar component with ID:', toolbarComponentId);
                const toolbarComponent = Livewire.find(toolbarComponentId);
                
                if (toolbarComponent) {
                    console.log('Attempting to get details from MainToolbar...');
                    try {
                        const toolbarDetails = await toolbarComponent.call('getCompositeDetailsForPrint');
                        console.log('Got details from MainToolbar:', toolbarDetails);
                        
                        if (toolbarDetails && Object.keys(toolbarDetails).length > 0) {
                            compositeDetails = toolbarDetails;
                            detailsFound = true;
                        }
                    } catch (error) {
                        console.error('Error getting details from MainToolbar:', error);
                    }
                }
            } else {
                console.warn('MainToolbar component not found');
            }
        }
        
        // If we still don't have details, try to get basic information from the DOM
        if (!detailsFound) {
            console.warn('Could not find any Livewire components with data. Using DOM fallback.');
            console.log('Selector used:', selector);
            console.log('Available Livewire components:', typeof Livewire !== 'undefined' ? Object.keys(Livewire.components || {}) : 'Livewire not defined');
            
            // Try to get basic information from the DOM as fallback
            const titleElement = document.querySelector('h2[title]');
            if (titleElement) {
                compositeDetails = {
                    title: titleElement.textContent || 'Composite Print'
                };
                console.log('Using title from DOM:', compositeDetails.title);
            } else {
                compositeDetails = {
                    title: document.title || 'Composite Print'
                };
                console.log('Using document title as fallback:', compositeDetails.title);
            }
        }
        
        // --- Temporarily modify canvas for printing --- 
        const originalObjects = [...canvas.getObjects()];
        const gridLines = originalObjects.filter(obj => 
            obj.type === 'line' && 
            obj.stroke === '#e5e7eb' && 
            !obj.selectable && 
            !obj.evented
        );
        const originalBackgroundColor = canvas.backgroundColor;
        
        // Temporarily remove grid lines for printing
        if (gridLines.length > 0) {
            console.log(`Temporarily hiding ${gridLines.length} grid lines for printing`);
            gridLines.forEach(line => canvas.remove(line));
        }
        // Set background to white for printing (even if original was transparent)
        canvas.backgroundColor = '#ffffff';
        
        // Force canvas re-rendering
        canvas.renderAll();
        
        // Create a data URL of the modified canvas
        const dataUrl = canvas.toDataURL({
            format: 'png',
            quality: 1.0,
            multiplier: 2 // Higher quality for printing
        });
        
        // --- Restore canvas state --- 
        // Restore grid lines 
        if (gridLines.length > 0) {
            console.log('Restoring grid lines after generating data URL');
            gridLines.forEach(line => canvas.add(line));
            // Send grid lines to back
            gridLines.forEach(line => canvas.sendObjectToBack(line));
        }
        // Restore original background color
        canvas.backgroundColor = originalBackgroundColor;
        // Re-render the canvas to its original state
        canvas.renderAll();
        
        // --- Create Print Window --- 
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Please allow pop-ups to print the canvas');
            return;
        }
        
        const title = compositeDetails.title || 'Composite Print';
        
        // Helper function to safely render details
        const renderDetail = (label, value) => {
            return value ? `<div class="detail-item"><span class="label">${label}:</span> <span class="value">${value}</span></div>` : '';
        };
        
        // If we couldn't find the component using the selector, try using Livewire's getAllsByName method
        if (Object.keys(compositeDetails).length === 0 && typeof Livewire !== 'undefined' && typeof Livewire.all === 'function') {
            console.log('Attempting to find CompositeEditor using Livewire.all()');
            const components = Livewire.all();
            console.log('Available components:', components);
            
            // Try to find the CompositeEditor component
            const editorComponent = components.find(c => {
                // The name might be stored in different formats depending on Livewire version
                return (c.name && c.name.includes('composite-editor')) || 
                       (c.fingerprint && c.fingerprint.name && c.fingerprint.name.includes('composite-editor'));
            });
            
            if (editorComponent) {
                console.log('Found editor component via Livewire.all():', editorComponent);
                try {
                    compositeDetails = await editorComponent.call('getCompositeDetailsForPrint');
                    console.log('Successfully fetched details via Livewire.all():', compositeDetails);
                } catch (error) {
                    console.error('Error calling getCompositeDetailsForPrint via Livewire.all():', error);
                }
            }
        }
        
        // Write HTML content to the new window
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print - ${title}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            color: #333;
                        }
                        .print-container {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                        }
                        .header {
                            width: 100%;
                            text-align: center;
                            margin-bottom: 20px;
                            border-bottom: 1px solid #ccc;
                            padding-bottom: 10px;
                        }
                        .header h1 {
                            margin: 0;
                            font-size: 24px;
                        }
                        .content {
                            display: flex;
                            flex-direction: column;
                            width: 100%;
                            margin-bottom: 20px;
                        }
                        @media (min-width: 768px) {
                            .content {
                                flex-direction: row;
                                gap: 30px;
                            }
                        }
                        .image-container {
                            flex: 1;
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        @media (min-width: 768px) {
                            .image-container {
                                margin-bottom: 0;
                            }
                        }
                        .image-container img {
                            max-width: 100%;
                            max-height: 60vh; /* Adjust as needed */
                            border: 1px solid #eee;
                        }
                        .details-container {
                            flex: 1;
                        }
                        @media (min-width: 768px) {
                            .details-container {
                                border-left: 1px solid #eee;
                                padding-left: 20px;
                            }
                        }
                        .details-container h2 {
                            margin-top: 0;
                            font-size: 18px;
                            border-bottom: 1px solid #eee;
                            padding-bottom: 5px;
                            margin-bottom: 15px;
                        }
                        .detail-item {
                            margin-bottom: 8px;
                            font-size: 14px;
                        }
                        .detail-item .label {
                            font-weight: bold;
                            min-width: 120px; /* Adjust as needed */
                            display: inline-block;
                        }
                        .detail-item .value {
                            color: #555;
                        }
                        .description, .notes {
                            margin-top: 15px;
                            font-size: 14px;
                            line-height: 1.5;
                        }
                        .description p, .notes p {
                            margin-top: 5px;
                            white-space: pre-wrap; /* Preserve line breaks */
                        }
                        .controls {
                            margin-top: 20px;
                            text-align: center;
                        }
                        .controls button {
                            padding: 8px 16px;
                            background: #3490dc;
                            color: white;
                            border: none;
                            border-radius: 4px;
                            cursor: pointer;
                            margin: 0 5px;
                        }
                        .no-details-message {
                            text-align: center;
                            color: #888;
                            font-style: italic;
                            margin: 10px 0;
                        }
                        @media print {
                            body { margin: 1cm; }
                            .controls { display: none; }
                            .content { page-break-inside: avoid; }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-container">
                        <div class="header">
                            <h1>${title}</h1>
                            ${renderDetail('Created', compositeDetails.created_at)}
                        </div>
                        
                        <div class="content">
                            <div class="image-container">
                                <img src="${dataUrl}" alt="Canvas Image">
                            </div>
                            <div class="details-container">
                                <h2>Composite Information</h2>
                                ${compositeDetails.witness_name || compositeDetails.case_title || compositeDetails.description ? 
                                    `
                                    ${renderDetail('Witness', compositeDetails.witness_name)}
                                    ${renderDetail('Case', compositeDetails.case_title)}
                                    ${compositeDetails.description ? 
                                        `<div class="description"><strong>Description:</strong><p>${compositeDetails.description}</p></div>` : ''}
                                    ` : 
                                    `<div class="no-details-message">No composite information available</div>`
                                }
                                
                                <h2 style="margin-top: 20px;">Suspect Description</h2>
                                ${compositeDetails.suspect_gender || compositeDetails.suspect_ethnicity || 
                                  compositeDetails.suspect_age_range || compositeDetails.suspect_height || 
                                  compositeDetails.suspect_body_build || compositeDetails.suspect_additional_notes ? 
                                    `
                                    ${renderDetail('Gender', compositeDetails.suspect_gender)}
                                    ${renderDetail('Ethnicity', compositeDetails.suspect_ethnicity)}
                                    ${renderDetail('Age Range', compositeDetails.suspect_age_range)}
                                    ${renderDetail('Height', compositeDetails.suspect_height)}
                                    ${renderDetail('Body Build', compositeDetails.suspect_body_build)}
                                    ${compositeDetails.suspect_additional_notes ? 
                                        `<div class="notes"><strong>Additional Notes:</strong><p>${compositeDetails.suspect_additional_notes}</p></div>` : ''}
                                    ` : 
                                    `<div class="no-details-message">No suspect description available</div>`
                                }
                            </div>
                        </div>
                        
                        <div class="controls">
                            <button onclick="window.print()">Print</button>
                            <button onclick="window.close()">Close</button>
                        </div>
                    </div>
                    <script>
                        // Optional: Auto print when loaded
                        window.onload = function() {
                            console.log('Print window loaded');
                        };
                    </script>
                </body>
            </html>
        `);
        
        printWindow.document.close();
        console.log('Print window created successfully with composite details');
        
    } catch (error) {
        console.error('Error during print canvas operation:', error);
        alert('Failed to print the canvas. Please try again.');
    }
}

/**
 * Download the canvas content as an image (legacy method, kept for fallback)
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
function downloadCanvas(canvas) {
    try {
        console.log('Legacy download canvas function called');
        
        // Check if canvas exists
        if (!canvas) {
            console.error('Cannot download - canvas is not defined');
            return;
        }
        
        exportCanvas(canvas, {
            format: 'png',
            quality: 1.0,
            multiplier: 2,
            keepTransparency: true,
            showGrid: false
        });
        
    } catch (error) {
        console.error('Error during download canvas operation:', error);
        alert('Failed to download the canvas. Please try again.');
    }
} 