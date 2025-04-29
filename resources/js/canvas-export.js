/**
 * Canvas Export Functionality
 * Implements print and download for the Fabric.js canvas
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Canvas export module loaded');
    
    // Wait until the canvas is ready
    window.addEventListener('canvas:initialized', function(event) {
        const canvas = event.detail.canvas;
        console.log('Canvas export module detected initialized canvas:', canvas);
        
        // Set up print button
        const printButton = document.getElementById('print-canvas-btn');
        if (printButton) {
            console.log('Print button found, attaching event listener');
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
 * Print the canvas content
 * @param {fabric.Canvas} canvas - The Fabric.js canvas instance
 */
function printCanvas(canvas) {
    try {
        console.log('Print canvas function called');
        
        // Check if canvas exists
        if (!canvas) {
            console.error('Cannot print - canvas is not defined');
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
        
        // Temporarily remove grid lines for printing
        if (gridLines.length > 0) {
            console.log(`Temporarily hiding ${gridLines.length} grid lines for printing`);
            gridLines.forEach(line => canvas.remove(line));
        }
        
        // Force canvas re-rendering
        canvas.renderAll();
        
        // Create a data URL of the canvas
        const dataUrl = canvas.toDataURL({
            format: 'png',
            quality: 1.0,
            multiplier: 2 // Higher quality for printing
        });
        
        // Create a new window
        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Please allow pop-ups to print the canvas');
            return;
        }
        
        // Get composite title if available
        let title = 'Canvas Print';
        const titleElement = document.querySelector('.composite-title, h2[title]');
        if (titleElement) {
            title = titleElement.textContent || title;
        }
        
        // Write HTML content to the new window
        printWindow.document.write(`
            <html>
                <head>
                    <title>${title}</title>
                    <style>
                        @media print {
                            body {
                                margin: 0;
                                padding: 0;
                            }
                            img {
                                max-width: 100%;
                                max-height: 100%;
                            }
                        }
                        body {
                            margin: 0;
                            padding: 20px;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                            font-family: Arial, sans-serif;
                        }
                        .controls {
                            margin-bottom: 20px;
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
                        h1 {
                            margin-bottom: 20px;
                            color: #333;
                            font-size: 24px;
                        }
                        img {
                            max-width: 100%;
                            max-height: 80vh;
                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                        }
                    </style>
                </head>
                <body>
                    <div class="controls">
                        <h1>${title}</h1>
                        <button onclick="window.print()">Print</button>
                        <button onclick="window.close()">Close</button>
                    </div>
                    <img src="${dataUrl}" alt="Canvas Image">
                    <script>
                        // Auto print when loaded
                        window.onload = function() {
                            // Auto print option - uncommenting this will auto-print
                            // setTimeout(function() {
                            //     window.print();
                            // }, 500);
                        };
                    </script>
                </body>
            </html>
        `);
        
        printWindow.document.close();
        console.log('Print window created successfully');
        
        // Restore grid lines after print window is created
        if (gridLines.length > 0) {
            console.log('Restoring grid lines after printing');
            gridLines.forEach(line => canvas.add(line));
            // Send grid lines to back
            gridLines.forEach(line => canvas.sendObjectToBack(line));
            canvas.renderAll();
        }
        
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