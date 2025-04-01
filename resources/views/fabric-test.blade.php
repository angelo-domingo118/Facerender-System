<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Fabric.js Test Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Shape Controls</h3>
                    <div class="mt-3 flex space-x-4">
                        <button id="add-rectangle" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Rectangle</button>
                        <button id="add-circle" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">Circle</button>
                        <button id="add-triangle" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">Triangle</button>
                        <button id="add-text" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition">Text</button>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Image Upload</h3>
                    <div class="mt-3 flex space-x-4 items-center">
                        <label for="image-upload" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition cursor-pointer">
                            Select Image
                            <input type="file" id="image-upload" accept="image/*" class="hidden" />
                        </label>
                        <span id="image-name" class="text-sm text-gray-600">No image selected</span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                    <div class="mt-3 flex space-x-4">
                        <button id="delete-selected" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">Delete Selected</button>
                        <button id="clear-canvas" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">Clear Canvas</button>
                    </div>
                </div>
                
                <div class="border-2 border-gray-300 rounded-lg">
                    <canvas id="fabric-canvas" width="800" height="600"></canvas>
                </div>
                
                <div class="mt-4 text-sm text-gray-500">
                    <p>Tips:</p>
                    <ul class="list-disc pl-5 mt-2">
                        <li>Click and drag objects to move them</li>
                        <li>Use the corner handles to resize objects</li>
                        <li>Click and drag the circular handle to rotate objects</li>
                        <li>Click on an object to select it</li>
                        <li>Upload an image to add it to the canvas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/fabric-test.js'])
</x-app-layout> 