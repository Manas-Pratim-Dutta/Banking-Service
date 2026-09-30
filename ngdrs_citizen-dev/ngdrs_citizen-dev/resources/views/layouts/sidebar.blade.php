<section class="sidebar">
    <aside id="sidebar" class="w-64 bg-white h-full transition-all duration-300 relative">

        <nav class="">
            <button id="sidebar-toggle"
                class="absolute -right-3 top-9 bg-white border border-gray-200 rounded-full p-1 cursor-pointer hover:bg-gray-50 z-50">
                <svg id="toggle-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor"
                    class="w-4 h-4 text-gray-500 transition-transform duration-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            <div class="px-6" id="profile">
                <div
                    class="profile-container bg-zinc-100 p-3 rounded-md hover:cursor-pointer relative transition-all duration-300">
                    <div class="flex items-center justify-between overflow-hidden">
                        <div class="sidebar-text whitespace-nowrap opacity-100 transition-opacity duration-200">
                            <p class="text-sm text-zinc-900 font-medium">Nitin Singh</p>
                            <p class="text-xs text-zinc-400">User</p>
                        </div>
                        <div
                            class="sidebar-initials hidden absolute inset-0 flex items-center justify-center w-full h-full">
                            <div
                                class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-bold">
                                NS
                            </div>
                        </div>
                        <div class="my-auto sidebar-text opacity-100 transition-opacity duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4 text-zinc-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>

                    <div id="profile-action"
                        class="absolute right-0 mt-3 min-w-full w-auto bg-white rounded-md shadow hidden py-2 z-50">
                        <form method="POST" action="{{ route("logout") }}">
                            @csrf
                            <button type="submit" class="w-full text-left py-2 hover:bg-zinc-100 px-6">
                                <div class="flex items-center gap-4 ">
                                    <div class="flex justify-between items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="text-red-600" width="18"
                                            height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                    </div>
                                    <span class="text-sm text-zinc-500 sidebar-text whitespace-nowrap">
                                        Logout</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="py-4">

                <!-- <div id="" data-menu-toggle="submenu-container" class="menu-container">
                    <div
                        class="py-3 px-6 flex align-center cursor-pointer group  in-[.active]:bg-primary/15 in-[.active]:border-r-4 in-[.active]:border-primary transition-all duration-500 linear">
                        <div class="flex align-center gap-3">
                            <div class="my-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="text-grey in-[.active]:text-primary transition-all duration-500 linear"
                                    width="22" height="22">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                            </div>
                            <div class="my-auto sidebar-text">
                                <button
                                    class="w-full flex items-center text-grey text-left rounded font-semibold text-sm tracking-wide group-hover:text-primary in-[.active]:text-primary">
                                    Home
                                </button>

                            </div>
                        </div>
                        <div class="my-auto ml-auto sidebar-text">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="text-zinc-200 in-[.active]:text-primary in-[.active]:rotate-90 transition-all duration-500 linear"
                                width="12" height="22">
                                <path fill-rule="evenodd"
                                    d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <div id="submenu-container" data-submenu
                        class="overflow-hidden max-h-0 opacity-0 transition-all duration-500 linear">
                        <div class="py-2">
                            <div id="submenuitem ">
                                <div class="flex align-center group">
                                    <span
                                        class="my-auto h-0.5 w-1.75 bg-primary transition-all duration-200 group-hover:w-3.25"></span>


                                    <a href="{{ route('all-applications') }}"
                                        class="block px-3 py-2 text-sm2 text-grey group-hover:text-primary">
                                        All Applications
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div> -->
                <!-- <div id="" data-menu-toggle="submenu-container-2" class="menu-container">
                    <div
                        class="py-3 px-6 flex align-center cursor-pointer group  in-[.active]:bg-primary/15 in-[.active]:border-r-4 in-[.active]:border-primary transition-all duration-500 linear">
                        <div class="flex align-center gap-3">
                            <div class="my-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="text-grey in-[.active]:text-primary transition-all duration-500 linear"
                                    width="22" height="22">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div class="my-auto sidebar-text">
                                <button
                                    class="w-full flex items-center text-grey text-left rounded font-semibold text-sm tracking-wide group-hover:text-primary in-[.active]:text-primary">
                                    User Management
                                </button>

                            </div>
                        </div>
                        <div class="my-auto ml-auto sidebar-text">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="text-zinc-200 in-[.active]:text-primary in-[.active]:rotate-90 transition-all duration-500 linear"
                                width="12" height="22">
                                <path fill-rule="evenodd"
                                    d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <div id="submenu-container-2" data-submenu
                        class="overflow-hidden max-h-0 opacity-0 transition-all duration-500 linear">
                        <div class="py-2">
                            <div id="submenuitem ">
                                <div class="flex align-center group">
                                    <span
                                        class="my-auto h-0.5 w-1.75 bg-primary transition-all duration-200 group-hover:w-3.25"></span>

                                    <a href="#" class="block px-3 py-2 text-sm2 text-grey group-hover:text-primary">
                                        Reset Password
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div id="" data-menu-toggle="submenu-container-3" class="menu-container">
                    <div
                        class="py-3 px-6 flex align-center cursor-pointer group  in-[.active]:bg-primary/15 in-[.active]:border-r-4 in-[.active]:border-primary transition-all duration-500 linear">
                        <div class="flex align-center gap-3">
                            <div class="my-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="text-grey in-[.active]:text-primary transition-all duration-500 linear"
                                    width="22" height="22">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z" />
                                </svg>
                            </div>
                            <div class="my-auto sidebar-text">
                                <button
                                    class="w-full flex items-center text-grey text-left rounded font-semibold text-sm tracking-wide group-hover:text-primary in-[.active]:text-primary">
                                    Valuation
                                </button>

                            </div>
                        </div>
                        <div class="my-auto ml-auto sidebar-text">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="text-zinc-200 in-[.active]:text-primary in-[.active]:rotate-90 transition-all duration-500 linear"
                                width="12" height="22">
                                <path fill-rule="evenodd"
                                    d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Submenu -->
                    <div id="submenu-container-3" data-submenu
                        class="overflow-hidden max-h-0 opacity-0 transition-all duration-500 linear">
                        <div class="py-2">
                            <div id="submenuitem ">
                                <div class="flex align-center group">
                                    <span
                                        class="my-auto h-0.5 w-1.75 bg-primary transition-all duration-200 group-hover:w-3.25"></span>
                                    <a href="#" class="block px-3 py-2 text-sm2 text-grey group-hover:text-primary">
                                        Property Valuation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div id="" data-menu-toggle="submenu-container-4" class="menu-container">
                    <div
                        class="py-3 px-6 flex align-center cursor-pointer group  in-[.active]:bg-primary/15 in-[.active]:border-r-4 in-[.active]:border-primary transition-all duration-500 linear">
                        <div class="flex align-center gap-3">
                            <div class="my-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="text-grey in-[.active]:text-primary transition-all duration-500 linear"
                                    width="22" height="22" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>

                            </div>
                            <div class="my-auto sidebar-text">
                                <button
                                    class="w-full flex items-center text-grey text-left rounded font-semibold text-sm tracking-wide group-hover:text-primary in-[.active]:text-primary">
                                    Deed Services
                                </button>

                            </div>
                        </div>
                        <div class="my-auto ml-auto sidebar-text">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="text-zinc-200 in-[.active]:text-primary in-[.active]:rotate-90 transition-all duration-500 linear"
                                width="12" height="22">
                                <path fill-rule="evenodd"
                                    d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <div id="submenu-container-4" data-submenu
                        class="overflow-hidden max-h-0 opacity-0 transition-all duration-500 linear">
                        <div class="py-2">
                            <div id="submenuitem ">
                                <div class="flex align-center group">
                                    <span
                                        class="my-auto h-0.5 w-1.75 bg-primary transition-all duration-200 group-hover:w-3.25"></span>
                                    <a href="#" class="block px-3 py-2 text-sm2 text-grey group-hover:text-primary">
                                        Deed Search
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div id="" data-menu-toggle="submenu-container-5" class="menu-container">
                    <div
                        class="py-3 px-6 flex align-center cursor-pointer group  in-[.active]:bg-primary/15 in-[.active]:border-r-4 in-[.active]:border-primary transition-all duration-500 linear">
                        <div class="flex align-center gap-3">
                            <div class="my-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="text-grey in-[.active]:text-primary transition-all duration-500 linear"
                                    width="22" height="22">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                </svg>

                            </div>
                            <div class="my-auto sidebar-text">
                                <button
                                    class="w-full flex items-center text-grey text-left rounded font-semibold text-sm tracking-wide group-hover:text-primary in-[.active]:text-primary">
                                    Payment Services
                                </button>

                            </div>
                        </div>
                        <div class="my-auto ml-auto sidebar-text">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="text-zinc-200 in-[.active]:text-primary in-[.active]:rotate-90 transition-all duration-500 linear"
                                width="12" height="22">
                                <path fill-rule="evenodd"
                                    d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <div id="submenu-container-5" data-submenu
                        class="overflow-hidden max-h-0 opacity-0 transition-all duration-500 linear">
                        <div class="py-2">
                            <div id="submenuitem ">
                                <div class="flex align-center group">
                                    <span
                                        class="my-auto h-0.5 w-1.75 bg-primary transition-all duration-200 group-hover:w-3.25"></span>
                                    <a href="#" class="block px-3 py-2 text-sm2 text-grey group-hover:text-primary">
                                        Gras Payment Status
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </nav>
    </aside>
</section>