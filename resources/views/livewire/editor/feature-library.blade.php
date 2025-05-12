<div class="h-full flex flex-col bg-slate-800/60 relative" 
    x-data="{ 
        loading: false,
        init() {
            Livewire.on('scrollToTop', () => {
                const featureGrid = document.querySelector('.feature-grid-container');
                if (featureGrid) {
                    featureGrid.scrollTop = 0;
                }
            });
        }
    }"
>
    <!-- Feature Category Selection -->
    <div class="p-3 border-b border-slate-600 bg-slate-700/50">
        <div x-data="{ open: false, selected: @entangle('selectedCategory').live }" class="relative">
            <div class="mb-1">
                <label class="text-sm font-medium text-gray-300 block">Feature Category</label>
            </div>
            
            <div class="flex space-x-2">
                <!-- Custom Dropdown Trigger -->
                <button 
                    @click="open = !open" 
                    type="button"
                    class="flex-1 bg-slate-600 border border-slate-500 rounded-lg px-3 py-2 text-left text-sm flex items-center justify-between hover:border-[#3498DB] transition-colors duration-150 feature-category-dropdown"
                >
                    <!-- Show selected category or placeholder -->
                    <span class="flex items-center">
                        <!-- Category Icon - conditionally show based on selection -->
                        <span x-show="selected === 'face'" class="mr-2">
                            <svg viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-400" fill="currentColor">
                              <!-- License: MIT. Made by radix-ui: https://github.com/radix-ui/icons -->
                              <path fill-rule="evenodd" clip-rule="evenodd" d="M7.49991 0.876892C3.84222 0.876892 0.877075 3.84204 0.877075 7.49972C0.877075 11.1574 3.84222 14.1226 7.49991 14.1226C11.1576 14.1226 14.1227 11.1574 14.1227 7.49972C14.1227 3.84204 11.1576 0.876892 7.49991 0.876892ZM1.82708 7.49972C1.82708 4.36671 4.36689 1.82689 7.49991 1.82689C10.6329 1.82689 13.1727 4.36671 13.1727 7.49972C13.1727 10.6327 10.6329 13.1726 7.49991 13.1726C4.36689 13.1726 1.82708 10.6327 1.82708 7.49972ZM5.03747 9.21395C4.87949 8.98746 4.56782 8.93193 4.34133 9.08991C4.11484 9.24789 4.05931 9.55956 4.21729 9.78605C4.93926 10.8211 6.14033 11.5 7.50004 11.5C8.85974 11.5 10.0608 10.8211 10.7828 9.78605C10.9408 9.55956 10.8852 9.24789 10.6587 9.08991C10.4323 8.93193 10.1206 8.98746 9.9626 9.21395C9.41963 9.99238 8.51907 10.5 7.50004 10.5C6.481 10.5 5.58044 9.99238 5.03747 9.21395ZM5.37503 6.84998C5.85828 6.84998 6.25003 6.45815 6.25003 5.97498C6.25003 5.4918 5.85828 5.09998 5.37503 5.09998C4.89179 5.09998 4.50003 5.4918 4.50003 5.97498C4.50003 6.45815 4.89179 6.84998 5.37503 6.84998ZM10.5 5.97498C10.5 6.45815 10.1083 6.84998 9.62503 6.84998C9.14179 6.84998 8.75003 6.45815 8.75003 5.97498C8.75003 5.4918 9.14179 5.09998 9.62503 5.09998C10.1083 5.09998 10.5 5.4918 10.5 5.97498Z" />
                            </svg>
                        </span>
                        <span x-show="selected === 'hair'" class="mr-2">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-400" fill="currentColor">
                                <!-- License: Apache. Made by Richard9394: https://github.com/Richard9394/MingCute -->
                                <path d="M12,3 C16.9706,3 21,7.02944 21,12 L21,18 C21,19.6569 19.6569,21 18,21 L16.618,21 C15.8985,21 15.2376,20.6138 14.8828,19.9945 C13.9472,20.6335 12.916,21 12,21 C11.084,21 10.0528,20.6335 9.11722,19.9944 C8.76242,20.6138 8.10152,21 7.38197,21 L6,21 C4.34315,21 3,19.6569 3,18 L3,12 C3,7.02944 7.02944,3 12,3 Z M12,5 C8.13401,5 5,8.13401 5,12 L5,18 C5,18.5523 5.44772,19 6,19 L7.38197,19 L7.59096,18.582 C6.66115,17.4286 6,15.8799 6,14 C6,13.2091 6.13344,12.4256 6.37336,11.6879 C6.51091,11.265 6.9112,10.9837 7.35569,10.9977 C8.86025,11.0449 10.3154,10.3183 11.1824,9.08727 C11.3697,8.82129 11.6747,8.66304 12,8.66304 C12.3253,8.66304 12.6303,8.8213 12.8176,9.08728 C13.6845,10.3184 15.1397,11.0449 16.6443,10.9977 C17.0888,10.9838 17.4891,11.2651 17.6267,11.688 C17.8666,12.4256 18,13.2091 18,14 C18,15.8799 17.3389,17.4286 16.409,18.582 L16.618,19 L18,19 C18.5523,19 19,18.5523 19,18 L19,12 C19,8.13401 15.866,5 12,5 Z M12,11.1903 C10.9684,12.18 9.60981,12.8339 8.10191,12.9724 C8.03491,13.3131 8,13.6579 8,14 C8,15.6183 8.65705,16.8719 9.50644,17.7272 C10.3868,18.6137 11.3951,19 12,19 C12.6049,19 13.6132,18.6137 14.4936,17.7272 C15.3429,16.8719 16,15.6183 16,14 C16,13.6579 15.9651,13.3131 15.8981,12.9725 C14.3902,12.8339 13.0315,12.1801 12,11.1903 Z"/>
                            </svg>
                        </span>
                        <span x-show="selected === 'eyes'" class="mr-2">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-400" fill="currentColor">
                                <!-- License: Apache. Made by Iconscout: https://github.com/Iconscout/unicons -->
                                <path d="M21.92,11.6C19.9,6.91,16.1,4,12,4S4.1,6.91,2.08,11.6a1,1,0,0,0,0,.8C4.1,17.09,7.9,20,12,20s7.9-2.91,9.92-7.6A1,1,0,0,0,21.92,11.6ZM12,18c-3.17,0-6.17-2.29-7.9-6C5.83,8.29,8.83,6,12,6s6.17,2.29,7.9,6C18.17,15.71,15.17,18,12,18ZM12,8a4,4,0,1,0,4,4A4,4,0,0,0,12,8Zm0,6a2,2,0,1,1,2-2A2,2,0,0,1,12,14Z"/>
                            </svg>
                        </span>
                        <span x-show="selected === 'eyebrows'" class="mr-2">
                            <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="size-5 text-gray-400" fill="currentColor">
                                <!-- License: Apache. Made by Richard9394: https://github.com/Richard9394/MingCute -->
                                <title>eyebrow_line</title>
                                <path d="M7.65839,10.4478 C5.97582,10.8292 4.59689,11.4276 3.71006,12.1548 C3.22513,12.5525 3.04805,12.9509 3.00912,13.2717 C2.96903,13.602 3.06346,13.9463 3.28848,14.2505 C3.7336,14.8522 4.6879,15.2711 5.84877,14.7925 C8.54702,13.6799 13.8165,11.5325 19.4843,11.9603 C19.0818,11.6736 18.6237,11.4145 18.1143,11.1845 C14.9375,9.75047 11.015,9.68694 7.65839,10.4478 Z M18.9372,9.36167 C20.6395,10.1301 22.108,11.2831 22.8998,12.9158 C23.0681,13.2628 23.0219,13.6757 22.781,13.9769 C22.5401,14.278 22.1474,14.4138 21.772,14.3258 C15.8145,12.93 9.78907,15.3311 6.61118,16.6414 C4.618,17.4633 2.68113,16.7923 1.68066,15.44 C1.18289,14.7672 0.915929,13.9186 1.02369,13.0307 C1.1326,12.1333 1.6126,11.2884 2.44183,10.6083 C3.65763,9.6113 5.36699,8.91645 7.21626,8.49728 C10.9972,7.64025 15.3643,7.74879 18.9372,9.36167 Z"/>
                            </svg>
                        </span>
                        <span x-show="selected === 'nose'" class="mr-2">
                            <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="size-5 text-gray-400" fill="currentColor">
                                <!-- License: Apache. Made by Richard9394: https://github.com/Richard9394/MingCute -->
                                <title>nose_line</title>
                                <path d="M9.5,4 C9.5,3.44772 9.05228,3 8.5,3 C7.94772,3 7.5,3.44772 7.5,4 C7.5,5.82724 7.04443,7.67507 6.10557,9.55279 C5.65437,10.4552 5.01801,11.1536 4.31564,12.1216 C3.66521,13.0179 3,14.1126 3,15.5 C3,17.433 4.567,19 6.5,19 C7.07231,19 8.01211,18.6241 8.55279,18.8944 C8.95146,19.0938 9.20854,19.5539 9.50991,19.8735 C10.0291,20.4242 10.7741,21 12,21 C13.2259,21 13.9709,20.4242 14.4901,19.8735 C14.7847,19.561 15.0522,19.0919 15.4472,18.8944 C15.9879,18.6241 16.9277,19 17.5,19 C19.433,19 21,17.433 21,15.5 C21,14.1126 20.3348,13.0179 19.6844,12.1216 C18.982,11.1537 18.3456,10.4552 17.8944,9.55279 C16.9556,7.67507 16.5,5.82724 16.5,4 C16.5,3.44772 16.0523,3 15.5,3 C14.9477,3 14.5,3.44772 14.5,4 C14.5,6.17276 15.0444,8.32493 16.1056,10.4472 C16.6543,11.5448 17.518,12.5416 18.0656,13.2962 C18.6652,14.1224 19,14.7777 19,15.5 C19,16.3284 18.3284,17 17.5,17 C16.4837,17 15.5332,16.6153 14.5528,17.1056 C13.9114,17.4262 13.5129,17.9945 13.0349,18.5015 C12.7291,18.8258 12.4741,19 12,19 C11.5259,19 11.2709,18.8258 10.9651,18.5015 C10.4898,17.9974 10.0842,17.4241 9.44721,17.1056 C8.46675,16.6153 7.51632,17 6.5,17 C5.67157,17 5,16.3284 5,15.5 C5,14.7777 5.33479,14.1224 5.93436,13.2962 C6.48197,12.5415 7.34564,11.5448 7.89443,10.4472 C8.95557,8.32493 9.5,6.17276 9.5,4 Z"/>
                            </svg>
                        </span>
                        <span x-show="selected === 'mouth'" class="mr-2">
                            <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-400" fill="none" stroke="currentColor">
                                <!-- License: Apache. Made by bytedance: https://github.com/bytedance/IconPark -->
                                <path d="M4 24C4 24 10 15 14 15C18 15 22 17 24 17C26 17 30 15 34 15C38 15 44 24 44 24C44 24 34 34 24 34C14 34 4 24 4 24Z" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 24H44" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span x-show="selected === 'ears'" class="mr-2">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" class="size-5 text-gray-400" fill="currentColor">
                                <!-- License: Apache. Made by Iconscout: https://github.com/Iconscout/unicons -->
                                <title>Ear</title>
                                <path d="M12,8a1.00067,1.00067,0,0,1,1,1,1,1,0,0,0,2,0,2.9995,2.9995,0,1,0-5.01758,2.2207c.01.0091.16113.16992.19336.21485a.9875.9875,0,0,1,0,1.11914.99952.99952,0,1,0,1.64844,1.13086,2.98332,2.98332,0,0,0-.00488-3.38867,7.12392,7.12392,0,0,0-.49122-.55665,1.05523,1.05523,0,0,1-.1582-.18164A1.00072,1.00072,0,0,1,12,8Zm0-6a7.0006,7.0006,0,0,0-6.76172,8.81152A.99989.99989,0,0,0,7.16992,10.294,5.00018,5.00018,0,1,1,17,9a5.11412,5.11412,0,0,1-.70508,2.56738L12.73145,19A2.00462,2.00462,0,0,1,11,20a2.027,2.027,0,0,1-2-2,1.99224,1.99224,0,0,1,.26855-.999,1.00065,1.00065,0,0,0-1.73242-1.002,3.9988,3.9988,0,1,0,6.96289,3.9336L18.0625,12.5A7.00044,7.00044,0,0,0,12,2Z"/>
                            </svg>
                        </span>
                        <span x-show="selected === 'accessories'" class="mr-2">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 471.408 471.408" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 471.408 471.408" class="size-5 text-gray-400" fill="currentColor">
                              <!-- License: CC0. Made by SVG Repo: https://www.svgrepo.com/svg/28922/ring -->
                              <g>
                                <path d="m276.243,105.709l31.3-54.219c2.479-4.295 3.02-9.44 1.487-14.157-1.532-4.716-4.994-8.562-9.524-10.579l-56.68-25.24c-4.531-2.019-9.707-2.019-14.238,0l-56.68,25.24c-4.53,2.017-7.992,5.862-9.524,10.579-1.532,4.717-0.992,9.862 1.487,14.157l31.3,54.218c-82.628,18.533-144.559,92.47-144.559,180.609 0,102.06 83.032,185.091 185.092,185.091s185.091-83.031 185.091-185.091c-5.68434e-14-88.137-61.927-162.073-144.552-180.608zm-72.343-54.887l31.808-14.165 31.808,14.165-29.118,50.438c-0.897-0.013-4.485-0.013-5.38,0l-29.118-50.438zm31.804,385.586c-82.761,0-150.092-67.331-150.092-150.091 0-82.761 67.331-150.091 150.092-150.091s150.091,67.331 150.091,150.091c0,82.76-67.33,150.091-150.091,150.091z"/>
                                <path d="m235.704,148.699c-75.883,0-137.617,61.735-137.617,137.618 0,75.882 61.734,137.617 137.617,137.617s137.617-61.735 137.617-137.617c0.001-75.883-61.734-137.618-137.617-137.618zm0,240.235c-56.583,0-102.617-46.034-102.617-102.617s46.034-102.618 102.617-102.618 102.617,46.034 102.617,102.618-46.034,102.617-102.617,102.617z"/>
                              </g>
                            </svg>
                        </span>
                        
                        <!-- Replace the complex nested ternary with a simpler implementation -->
                        <span class="text-gray-200 font-medium" x-show="!selected">Select a category</span>
                        <span class="text-gray-200 font-medium" x-show="selected === 'face'">Face Shape</span>
                        <span class="text-gray-200 font-medium" x-show="selected === 'hair'">Hair</span>
                        <span class="text-gray-200 font-medium" x-show="selected === 'eyes'">Eyes</span>
                        <span class="text-gray-200 font-medium" x-show="selected === 'eyebrows'">Eyebrows</span>
                        <span class="text-gray-200 font-medium" x-show="selected === 'nose'">Nose</span>
                        <span class="text-gray-200 font-medium" x-show="selected === 'mouth'">Mouth</span>
                        <span class="text-gray-200 font-medium" x-show="selected === 'ears'">Ears</span>
                        <span class="text-gray-200 font-medium" x-show="selected === 'accessories'">Accessories</span>
                    </span>
                    
                    <!-- Dropdown indicator -->
                    <svg class="h-5 w-5 text-gray-400" x-bind:class="{ 'transform rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <!-- Next Category Button -->
                <button 
                    @click="$wire.nextCategory()" 
                    type="button" 
                    class="px-2.5 py-2 bg-slate-600 hover:bg-slate-500 border border-slate-500 hover:border-[#3498DB] rounded-lg text-xs text-gray-200 flex items-center transition-colors duration-150 shadow-sm"
                    x-show="selected"
                    x-data="{
                        getNextCategory() {
                            const sequence = ['face', 'hair', 'eyes', 'eyebrows', 'nose', 'mouth', 'ears', 'accessories'];
                            const currentIndex = sequence.indexOf(selected);
                            return currentIndex >= 0 && currentIndex < sequence.length - 1 
                                ? sequence[currentIndex + 1] 
                                : sequence[0];
                        },
                        getNextCategoryName() {
                            const nextCat = this.getNextCategory();
                            const names = {
                                'face': 'Face',
                                'hair': 'Hair',
                                'eyes': 'Eyes',
                                'eyebrows': 'Eyebrows',
                                'nose': 'Nose',
                                'mouth': 'Mouth',
                                'ears': 'Ears',
                                'accessories': 'Accessories'
                            };
                            return names[nextCat] || 'Next';
                        }
                    }"
                >
                    <span class="whitespace-nowrap mr-1">Next: <span class="text-[#3498DB]" x-text="getNextCategoryName()"></span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-[#3498DB]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
            
            <!-- Dropdown Menu -->
            <div 
                x-show="open" 
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute mt-1 w-full z-10 bg-slate-700 shadow-lg rounded-lg py-1 border border-slate-600 category-dropdown-menu"
                style="display: none;"
            >
                <!-- Category Options -->
                <button 
                    @click="selected = 'face'; open = false;" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-slate-600 text-gray-200"
                    :class="{ 'bg-slate-600 text-[#3498DB]': selected === 'face' }"
                    wire:click="$set('selectedCategory', 'face')"
                >
                    <span class="mr-2">
                        <svg viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-400" :class="{ 'text-[#3498DB]': selected === 'face' }" fill="currentColor">
                            <!-- License: MIT. Made by radix-ui: https://github.com/radix-ui/icons -->
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.49991 0.876892C3.84222 0.876892 0.877075 3.84204 0.877075 7.49972C0.877075 11.1574 3.84222 14.1226 7.49991 14.1226C11.1576 14.1226 14.1227 11.1574 14.1227 7.49972C14.1227 3.84204 11.1576 0.876892 7.49991 0.876892ZM1.82708 7.49972C1.82708 4.36671 4.36689 1.82689 7.49991 1.82689C10.6329 1.82689 13.1727 4.36671 13.1727 7.49972C13.1727 10.6327 10.6329 13.1726 7.49991 13.1726C4.36689 13.1726 1.82708 10.6327 1.82708 7.49972ZM5.03747 9.21395C4.87949 8.98746 4.56782 8.93193 4.34133 9.08991C4.11484 9.24789 4.05931 9.55956 4.21729 9.78605C4.93926 10.8211 6.14033 11.5 7.50004 11.5C8.85974 11.5 10.0608 10.8211 10.7828 9.78605C10.9408 9.55956 10.8852 9.24789 10.6587 9.08991C10.4323 8.93193 10.1206 8.98746 9.9626 9.21395C9.41963 9.99238 8.51907 10.5 7.50004 10.5C6.481 10.5 5.58044 9.99238 5.03747 9.21395ZM5.37503 6.84998C5.85828 6.84998 6.25003 6.45815 6.25003 5.97498C6.25003 5.4918 5.85828 5.09998 5.37503 5.09998C4.89179 5.09998 4.50003 5.4918 4.50003 5.97498C4.50003 6.45815 4.89179 6.84998 5.37503 6.84998ZM10.5 5.97498C10.5 6.45815 10.1083 6.84998 9.62503 6.84998C9.14179 6.84998 8.75003 6.45815 8.75003 5.97498C8.75003 5.4918 9.14179 5.09998 9.62503 5.09998C10.1083 5.09998 10.5 5.4918 10.5 5.97498Z" />
                        </svg>
                    </span> Face Shape
                </button>
                <button 
                    @click="selected = 'hair'; open = false;" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-slate-600 text-gray-200"
                    :class="{ 'bg-slate-600 text-[#3498DB]': selected === 'hair' }"
                    wire:click="$set('selectedCategory', 'hair')"
                >
                    <span class="mr-2">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-400" :class="{ 'text-[#3498DB]': selected === 'hair' }" fill="currentColor">
                            <!-- License: Apache. Made by Richard9394: https://github.com/Richard9394/MingCute -->
                            <path d="M12,3 C16.9706,3 21,7.02944 21,12 L21,18 C21,19.6569 19.6569,21 18,21 L16.618,21 C15.8985,21 15.2376,20.6138 14.8828,19.9945 C13.9472,20.6335 12.916,21 12,21 C11.084,21 10.0528,20.6335 9.11722,19.9944 C8.76242,20.6138 8.10152,21 7.38197,21 L6,21 C4.34315,21 3,19.6569 3,18 L3,12 C3,7.02944 7.02944,3 12,3 Z M12,5 C8.13401,5 5,8.13401 5,12 L5,18 C5,18.5523 5.44772,19 6,19 L7.38197,19 L7.59096,18.582 C6.66115,17.4286 6,15.8799 6,14 C6,13.2091 6.13344,12.4256 6.37336,11.6879 C6.51091,11.265 6.9112,10.9837 7.35569,10.9977 C8.86025,11.0449 10.3154,10.3183 11.1824,9.08727 C11.3697,8.82129 11.6747,8.66304 12,8.66304 C12.3253,8.66304 12.6303,8.8213 12.8176,9.08728 C13.6845,10.3184 15.1397,11.0449 16.6443,10.9977 C17.0888,10.9838 17.4891,11.2651 17.6267,11.688 C17.8666,12.4256 18,13.2091 18,14 C18,15.8799 17.3389,17.4286 16.409,18.582 L16.618,19 L18,19 C18.5523,19 19,18.5523 19,18 L19,12 C19,8.13401 15.866,5 12,5 Z M12,11.1903 C10.9684,12.18 9.60981,12.8339 8.10191,12.9724 C8.03491,13.3131 8,13.6579 8,14 C8,15.6183 8.65705,16.8719 9.50644,17.7272 C10.3868,18.6137 11.3951,19 12,19 C12.6049,19 13.6132,18.6137 14.4936,17.7272 C15.3429,16.8719 16,15.6183 16,14 C16,13.6579 15.9651,13.3131 15.8981,12.9725 C14.3902,12.8339 13.0315,12.1801 12,11.1903 Z"/>
                        </svg>
                    </span> Hair
                </button>
                <button 
                    @click="selected = 'eyes'; open = false;" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-slate-600 text-gray-200"
                    :class="{ 'bg-slate-600 text-[#3498DB]': selected === 'eyes' }"
                    wire:click="$set('selectedCategory', 'eyes')"
                >
                    <span class="mr-2">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-400" :class="{ 'text-[#3498DB]': selected === 'eyes' }" fill="currentColor">
                            <!-- License: Apache. Made by Iconscout: https://github.com/Iconscout/unicons -->
                            <path d="M21.92,11.6C19.9,6.91,16.1,4,12,4S4.1,6.91,2.08,11.6a1,1,0,0,0,0,.8C4.1,17.09,7.9,20,12,20s7.9-2.91,9.92-7.6A1,1,0,0,0,21.92,11.6ZM12,18c-3.17,0-6.17-2.29-7.9-6C5.83,8.29,8.83,6,12,6s6.17,2.29,7.9,6C18.17,15.71,15.17,18,12,18ZM12,8a4,4,0,1,0,4,4A4,4,0,0,0,12,8Zm0,6a2,2,0,1,1,2-2A2,2,0,0,1,12,14Z"/>
                        </svg>
                    </span> Eyes
                </button>
                <button 
                    @click="selected = 'eyebrows'; open = false;" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-slate-600 text-gray-200"
                    :class="{ 'bg-slate-600 text-[#3498DB]': selected === 'eyebrows' }"
                    wire:click="$set('selectedCategory', 'eyebrows')"
                >
                    <span class="mr-2">
                        <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="size-5 text-gray-400" :class="{ 'text-[#3498DB]': selected === 'eyebrows' }" fill="currentColor">
                            <!-- License: Apache. Made by Richard9394: https://github.com/Richard9394/MingCute -->
                            <title>eyebrow_line</title>
                            <path d="M7.65839,10.4478 C5.97582,10.8292 4.59689,11.4276 3.71006,12.1548 C3.22513,12.5525 3.04805,12.9509 3.00912,13.2717 C2.96903,13.602 3.06346,13.9463 3.28848,14.2505 C3.7336,14.8522 4.6879,15.2711 5.84877,14.7925 C8.54702,13.6799 13.8165,11.5325 19.4843,11.9603 C19.0818,11.6736 18.6237,11.4145 18.1143,11.1845 C14.9375,9.75047 11.015,9.68694 7.65839,10.4478 Z M18.9372,9.36167 C20.6395,10.1301 22.108,11.2831 22.8998,12.9158 C23.0681,13.2628 23.0219,13.6757 22.781,13.9769 C22.5401,14.278 22.1474,14.4138 21.772,14.3258 C15.8145,12.93 9.78907,15.3311 6.61118,16.6414 C4.618,17.4633 2.68113,16.7923 1.68066,15.44 C1.18289,14.7672 0.915929,13.9186 1.02369,13.0307 C1.1326,12.1333 1.6126,11.2884 2.44183,10.6083 C3.65763,9.6113 5.36699,8.91645 7.21626,8.49728 C10.9972,7.64025 15.3643,7.74879 18.9372,9.36167 Z"/>
                        </svg>
                    </span> Eyebrows
                </button>
                <button 
                    @click="selected = 'nose'; open = false;" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-slate-600 text-gray-200"
                    :class="{ 'bg-slate-600 text-[#3498DB]': selected === 'nose' }"
                    wire:click="$set('selectedCategory', 'nose')"
                >
                    <span class="mr-2">
                        <svg viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="size-5 text-gray-400" :class="{ 'text-[#3498DB]': selected === 'nose' }" fill="currentColor">
                            <!-- License: Apache. Made by Richard9394: https://github.com/Richard9394/MingCute -->
                            <title>nose_line</title>
                            <path d="M9.5,4 C9.5,3.44772 9.05228,3 8.5,3 C7.94772,3 7.5,3.44772 7.5,4 C7.5,5.82724 7.04443,7.67507 6.10557,9.55279 C5.65437,10.4552 5.01801,11.1536 4.31564,12.1216 C3.66521,13.0179 3,14.1126 3,15.5 C3,17.433 4.567,19 6.5,19 C7.07231,19 8.01211,18.6241 8.55279,18.8944 C8.95146,19.0938 9.20854,19.5539 9.50991,19.8735 C10.0291,20.4242 10.7741,21 12,21 C13.2259,21 13.9709,20.4242 14.4901,19.8735 C14.7847,19.561 15.0522,19.0919 15.4472,18.8944 C15.9879,18.6241 16.9277,19 17.5,19 C19.433,19 21,17.433 21,15.5 C21,14.1126 20.3348,13.0179 19.6844,12.1216 C18.982,11.1537 18.3456,10.4552 17.8944,9.55279 C16.9556,7.67507 16.5,5.82724 16.5,4 C16.5,3.44772 16.0523,3 15.5,3 C14.9477,3 14.5,3.44772 14.5,4 C14.5,6.17276 15.0444,8.32493 16.1056,10.4472 C16.6543,11.5448 17.518,12.5416 18.0656,13.2962 C18.6652,14.1224 19,14.7777 19,15.5 C19,16.3284 18.3284,17 17.5,17 C16.4837,17 15.5332,16.6153 14.5528,17.1056 C13.9114,17.4262 13.5129,17.9945 13.0349,18.5015 C12.7291,18.8258 12.4741,19 12,19 C11.5259,19 11.2709,18.8258 10.9651,18.5015 C10.4898,17.9974 10.0842,17.4241 9.44721,17.1056 C8.46675,16.6153 7.51632,17 6.5,17 C5.67157,17 5,16.3284 5,15.5 C5,14.7777 5.33479,14.1224 5.93436,13.2962 C6.48197,12.5415 7.34564,11.5448 7.89443,10.4472 C8.95557,8.32493 9.5,6.17276 9.5,4 Z"/>
                        </svg>
                    </span> Nose
                </button>
                <button 
                    @click="selected = 'mouth'; open = false;" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-slate-600 text-gray-200"
                    :class="{ 'bg-slate-600 text-[#3498DB]': selected === 'mouth' }"
                    wire:click="$set('selectedCategory', 'mouth')"
                >
                    <span class="mr-2">
                        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" class="size-5 text-gray-400" fill="none" stroke="currentColor">
                            <!-- License: Apache. Made by bytedance: https://github.com/bytedance/IconPark -->
                            <path d="M4 24C4 24 10 15 14 15C18 15 22 17 24 17C26 17 30 15 34 15C38 15 44 24 44 24C44 24 34 34 24 34C14 34 4 24 4 24Z" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 24H44" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span> Mouth
                </button>
                <button 
                    @click="selected = 'ears'; open = false;" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-slate-600 text-gray-200"
                    :class="{ 'bg-slate-600 text-[#3498DB]': selected === 'ears' }"
                    wire:click="$set('selectedCategory', 'ears')"
                >
                    <span class="mr-2">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" class="size-5 text-gray-400" :class="{ 'text-[#3498DB]': selected === 'ears' }" fill="currentColor">
                            <!-- License: Apache. Made by Iconscout: https://github.com/Iconscout/unicons -->
                            <title>Ear</title>
                            <path d="M12,8a1.00067,1.00067,0,0,1,1,1,1,1,0,0,0,2,0,2.9995,2.9995,0,1,0-5.01758,2.2207c.01.0091.16113.16992.19336.21485a.9875.9875,0,0,1,0,1.11914.99952.99952,0,1,0,1.64844,1.13086,2.98332,2.98332,0,0,0-.00488-3.38867,7.12392,7.12392,0,0,0-.49122-.55665,1.05523,1.05523,0,0,1-.1582-.18164A1.00072,1.00072,0,0,1,12,8Zm0-6a7.0006,7.0006,0,0,0-6.76172,8.81152A.99989.99989,0,0,0,7.16992,10.294,5.00018,5.00018,0,1,1,17,9a5.11412,5.11412,0,0,1-.70508,2.56738L12.73145,19A2.00462,2.00462,0,0,1,11,20a2.027,2.027,0,0,1-2-2,1.99224,1.99224,0,0,1,.26855-.999,1.00065,1.00065,0,0,0-1.73242-1.002,3.9988,3.9988,0,1,0,6.96289,3.9336L18.0625,12.5A7.00044,7.00044,0,0,0,12,2Z"/>
                        </svg>
                    </span> Ears
                </button>
                <button 
                    @click="selected = 'accessories'; open = false;" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-slate-600 text-gray-200"
                    :class="{ 'bg-slate-600 text-[#3498DB]': selected === 'accessories' }"
                    wire:click="$set('selectedCategory', 'accessories')"
                >
                    <span class="mr-2">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 471.408 471.408" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 471.408 471.408" class="size-5 text-gray-400" :class="{ 'text-[#3498DB]': selected === 'accessories' }" fill="currentColor">
                            <!-- License: CC0. Made by SVG Repo: https://www.svgrepo.com/svg/28922/ring -->
                            <g>
                              <path d="m276.243,105.709l31.3-54.219c2.479-4.295 3.02-9.44 1.487-14.157-1.532-4.716-4.994-8.562-9.524-10.579l-56.68-25.24c-4.531-2.019-9.707-2.019-14.238,0l-56.68,25.24c-4.53,2.017-7.992,5.862-9.524,10.579-1.532,4.717-0.992,9.862 1.487,14.157l31.3,54.218c-82.628,18.533-144.559,92.47-144.559,180.609 0,102.06 83.032,185.091 185.092,185.091s185.091-83.031 185.091-185.091c-5.68434e-14-88.137-61.927-162.073-144.552-180.608zm-72.343-54.887l31.808-14.165 31.808,14.165-29.118,50.438c-0.897-0.013-4.485-0.013-5.38,0l-29.118-50.438zm31.804,385.586c-82.761,0-150.092-67.331-150.092-150.091 0-82.761 67.331-150.091 150.092-150.091s150.091,67.331 150.091,150.091c0,82.76-67.33,150.091-150.091,150.091z"/>
                              <path d="m235.704,148.699c-75.883,0-137.617,61.735-137.617,137.618 0,75.882 61.734,137.617 137.617,137.617s137.617-61.735 137.617-137.617c0.001-75.883-61.734-137.618-137.617-137.618zm0,240.235c-56.583,0-102.617-46.034-102.617-102.617s46.034-102.618 102.617-102.618 102.617,46.034 102.617,102.618-46.034,102.617-102.617,102.617z"/>
                            </g>
                        </svg>
                    </span> Accessories
                </button>
            </div>
        </div>
        
        <!-- Search input -->
        <div class="mt-3 relative">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="{{ !$selectedCategory ? 'Select category first' : 'Search features...' }}" 
                    wire:model.live="search"
                    class="pl-10 pr-10 py-2 bg-slate-600 border border-slate-500 rounded-lg w-full text-sm text-gray-200 placeholder-gray-400 focus:ring-[#3498DB] focus:border-[#3498DB] transition-colors duration-150 disabled:bg-slate-700 disabled:text-slate-400 disabled:cursor-not-allowed"
                    @disabled(!$selectedCategory)
                />
                
                <!-- Clear search button - only show when there's search text and input is enabled -->
                @if($search && $selectedCategory)
                    <button 
                        wire:click="clearSearch" 
                        type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
            
            <!-- Show active filters -->
            @if($search || $selectedSubcategory)
                <div class="mt-2 flex items-center text-xs text-gray-400 space-x-2">
                    <span class="font-medium">Active filters:</span>
                    @if($search)
                        <span class="bg-sky-700 text-sky-200 px-2 py-1 rounded-full flex items-center">
                            "{{ $search }}"
                            <button wire:click="clearSearch" class="ml-1 text-sky-300 hover:text-sky-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($selectedSubcategory)
                        @php
                            $subCategoryName = $subcategories->firstWhere('id', $selectedSubcategory)?->name ?? 'Unknown';
                        @endphp
                        <span class="bg-sky-700 text-sky-200 px-2 py-1 rounded-full flex items-center">
                            {{ $subCategoryName }}
                            <button wire:click="selectSubcategory(null)" class="ml-1 text-sky-300 hover:text-sky-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    <!-- Feature Grid with Livewire loading state -->
    <div class="flex-1 overflow-y-auto p-3 feature-grid-container relative scrollbar-thin scrollbar-thumb-slate-400 scrollbar-track-slate-800">
        <!-- Pure Livewire Loading Indicator -->
        <div wire:loading wire:target="selectedCategory" class="absolute inset-0 bg-slate-900/70 z-50 flex items-center justify-center">
            <div class="bg-slate-700 p-4 rounded-lg shadow-lg flex flex-col items-center">
                <div class="flex items-center justify-center space-x-2 animate-pulse">
                    <div class="w-3 h-3 bg-sky-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-sky-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-sky-500 rounded-full"></div>
                </div>
                <span class="mt-2 text-sm text-slate-200 font-medium">Loading features...</span>
            </div>
        </div>
        
        <!-- Search Loading Indicator -->
        <div wire:loading wire:target="search" class="absolute inset-0 bg-slate-900/70 z-50 flex items-center justify-center">
            <div class="bg-slate-700 p-4 rounded-lg shadow-lg flex flex-col items-center">
                <div class="flex items-center justify-center space-x-2 animate-pulse">
                    <div class="w-3 h-3 bg-sky-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-sky-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-sky-500 rounded-full"></div>
                </div>
                <span class="mt-2 text-sm text-slate-200 font-medium">
                    Searching for "{{ $search }}"...
                </span>
            </div>
        </div>

        @if(!$selectedCategory)
            <div class="flex flex-col items-center justify-center h-full text-center px-4">
                <div class="bg-[#2C3E50]/10 rounded-full p-4 w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-200 mb-2">Select a feature category</h3>
                <p class="text-gray-400 mb-6">Please select a feature category from the dropdown above to view available facial features.</p>
                
                <button 
                    @click="document.querySelector('.feature-category-dropdown').click()"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#2C3E50] hover:bg-[#1e2c38] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2C3E50] transition-colors duration-150"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    Choose Category
                </button>
            </div>
        @else
            @if($selectedCategory)
                <div class="mb-3">
                    <h3 class="text-sm font-medium text-gray-300">Categories</h3>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($subcategories as $category)
                            <button 
                                wire:click="selectSubcategory({{ $category->id }})"
                                class="px-2 py-1 text-xs rounded-full {{ $selectedSubcategory == $category->id ? 'bg-indigo-600 text-white' : 'bg-slate-600 text-slate-200 hover:bg-slate-500' }}"
                            >
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3">
                @forelse($features as $feature)
                    <div 
                        wire:key="feature-{{ $feature->id }}"
                        wire:click="selectFeature({{ $feature->id }})" 
                        class="relative bg-slate-700 border border-slate-600 rounded-md overflow-hidden hover:border-[#2C3E50] transition-colors duration-200 cursor-pointer group"
                    >
                        <!-- Checkmark overlay for active features -->
                        @if(in_array($feature->id, $activeFeatureIds))
                            <div class="absolute top-1 right-1 z-[5] bg-[#2C3E50] text-white rounded-full p-0.5 shadow">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @endif

                        <div class="aspect-square bg-slate-600 relative overflow-hidden">
                            <!-- Feature image with lazy loading and fade-in effect -->
                            <div class="absolute inset-0 w-full h-full feature-image-container">
                                <img 
                                    src="{{ asset('storage/' . $feature->image_path) }}" 
                                    alt="{{ $feature->name }}" 
                                    class="absolute inset-0 w-full h-full object-contain p-2 feature-library-image"
                                    loading="lazy"
                                    onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNmMWYxZjEiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjI4IiBmaWxsPSIjYWFhYWFhIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZTwvdGV4dD48L3N2Zz4='; this.onerror=null;"
                                >
                            </div>
                            
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-[#2C3E50]/75 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center gap-2">
                                <!-- Primary action button (Select) -->
                                <button 
                                    wire:click="selectFeature({{ $feature->id }})"
                                    class="bg-[#3498DB] text-white px-4 py-1.5 rounded-md text-sm font-medium shadow-sm hover:bg-[#2980B9] transition-colors duration-150 w-28 flex items-center justify-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Select
                                </button>
                                
                                <!-- View full image icon button -->
                                <button 
                                    type="button"
                                    @click.stop="$dispatch('open-modal', {
                                        id: 'view-full-image-{{ $feature->id }}', 
                                        image: '{{ asset('storage/' . $feature->image_path) }}', 
                                        name: '{{ $feature->name }}',
                                        featureId: {{ $feature->id }}
                                    })"
                                    class="text-slate-100 bg-slate-600/60 hover:bg-slate-500/75 transition-colors duration-150 rounded-md flex items-center justify-center text-xs px-3 py-1.5 w-28"
                                    title="View full image"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Preview
                                </button>
                            </div>
                        </div>
                        <div class="p-2">
                            <p class="text-xs font-medium truncate text-slate-200">{{ $feature->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ $feature->category->name }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-8 text-center">
                        @if($search)
                            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                                <div class="text-gray-400 mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-base font-medium text-gray-700 mb-1">No features found</h3>
                                <p class="text-sm text-gray-500 mb-4">No results found for "<span class="font-medium">{{ $search }}</span>"</p>
                                <button 
                                    wire:click="clearSearch"
                                    class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Clear search
                                </button>
                            </div>
                        @else
                            <p class="text-gray-300">No features found. Try adjusting your search or category.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        @endif
    </div>

    <!-- Full Image Modal -->
    <div 
        x-data="{ 
            open: false, 
            imageUrl: '', 
            featureName: '',
            featureId: null,
            init() {
                window.addEventListener('open-modal', (event) => {
                    if (event.detail.id.startsWith('view-full-image-')) {
                        this.imageUrl = event.detail.image;
                        this.featureName = event.detail.name;
                        this.featureId = event.detail.featureId;
                        this.open = true;
                    }
                });
            }
        }" 
        x-show="open" 
        x-cloak
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50" 
        aria-modal="true"
    >
        <div class="fixed inset-0 bg-black/60 transition-opacity" x-show="open" @click="open = false"></div>
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-slate-700 rounded-lg overflow-hidden shadow-xl max-w-2xl max-h-full transform w-full"
                @click.away="open = false"
            >
                <!-- Modal header -->
                <div class="px-4 py-3 bg-slate-800 border-b border-slate-600 flex items-center justify-between">
                    <h3 class="text-sm font-medium text-slate-200" x-text="featureName"></h3>
                    <div class="flex items-center">
                        <!-- Fullscreen button -->
                        <button 
                            @click="
                                const imgElem = document.querySelector('.modal-image');
                                if (imgElem) {
                                    if (imgElem.requestFullscreen) {
                                        imgElem.requestFullscreen();
                                    } else if (imgElem.webkitRequestFullscreen) { /* Safari */
                                        imgElem.webkitRequestFullscreen();
                                    } else if (imgElem.msRequestFullscreen) { /* IE11 */
                                        imgElem.msRequestFullscreen();
                                    }
                                }
                            " 
                            class="text-slate-400 hover:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-full p-1 mr-2"
                            title="View fullscreen"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                            </svg>
                        </button>
                        <button @click="open = false" class="text-slate-400 hover:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-full p-1">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal body -->
                <div class="p-4 bg-slate-700">
                    <div class="bg-slate-800 rounded-md p-2 shadow-sm">
                        <div class="relative max-w-full max-h-[60vh] mx-auto bg-slate-600 modal-image-container">
                            <img 
                                :src="imageUrl" 
                                :alt="featureName" 
                                class="max-w-full max-h-[60vh] object-contain mx-auto modal-image"
                                loading="eager"
                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjIwMCIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNmMWYxZjEiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjI4IiBmaWxsPSIjYWFhYWFhIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5JbWFnZTwvdGV4dD48L3N2Zz4='; this.onerror=null;"
                            >
                            </img>
                        </div>
                    </div>
                </div>
                
                <!-- Modal footer -->
                <div class="px-4 py-3 bg-slate-800 border-t border-slate-600 flex justify-end">
                    <button 
                        @click="open = false" 
                        class="px-3 py-1.5 border border-slate-500 text-slate-300 text-sm rounded-md hover:bg-slate-600 transition-colors duration-150 mr-2"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="$wire.selectFeature(featureId); open = false"
                        class="px-3 py-1.5 bg-[#3498DB] border border-[#3498DB] text-white text-sm rounded-md hover:bg-[#2980B9] transition-colors duration-150 flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Select Feature
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Style adjustments -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Add smooth transitions for feature items to improve perceived performance */
        .feature-library-image {
            transition: opacity 0.2s ease-in-out;
        }
        
        /* Animated loading dots */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .animate-pulse {
            animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        /* Staggered animation for the loading dots */
        .animate-pulse div:nth-child(1) {
            animation-delay: 0s;
        }
        
        .animate-pulse div:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .animate-pulse div:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* Custom scrollbar styling for feature grid */
        .feature-grid-container::-webkit-scrollbar {
            width: 8px;
        }
        .feature-grid-container::-webkit-scrollbar-track {
            background-color: #1e293b; /* slate-800 */
        }
        .feature-grid-container::-webkit-scrollbar-thumb {
            background-color: #94a3b8; /* slate-400 */
            border-radius: 4px;
        }
        /* For Firefox */
        .feature-grid-container {
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #1e293b; /* thumb track */
        }

        /* Custom scrollbar styling for category dropdown menu */
        .category-dropdown-menu::-webkit-scrollbar {
            width: 8px;
        }
        .category-dropdown-menu::-webkit-scrollbar-track {
            background-color: #1e293b; /* slate-800 */
        }
        .category-dropdown-menu::-webkit-scrollbar-thumb {
            background-color: #94a3b8; /* slate-400 */
            border-radius: 4px;
        }
        /* For Firefox */
        .category-dropdown-menu {
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #1e293b; /* thumb track */
        }
    </style>
</div>
