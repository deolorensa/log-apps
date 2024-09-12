<x-app-layout>
    <div class=" mx-auto p-4 sm:p-6 lg:p-8">
        <form class="max-w-2xl mx-auto" method="POST" action="{{ route('chirps.store') }}">
            @csrf
            <textarea
                name="message"
                placeholder="{{ __('Create Log Activity ?') }}"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            >{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-2" />
            <x-primary-button class="mt-4">{{ __('Create') }}</x-primary-button>
        </form>
        <div class="mt-6 grid grid-cols-8 gap-3">
            <div class="bg-white shadow-sm rounded-lg p-4 col-span-2">
                <h2 class="text-xl text-center mb-2 uppercase text-red-500 font-semibold">Ditolak</h2>
                <hr/>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-4 col-span-2">
                <h2 class="text-xl text-center mb-2 uppercase text-gray-500 font-semibold">Penidng</h2>
                <hr/>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-4 col-span-2">
                <h2 class="text-xl text-center mb-2 uppercase text-blue-500 font-semibold">Disetujui Manajer</h2>
                <hr/>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-4 col-span-2">
                <h2 class="text-xl text-center mb-2 uppercase text-green-500 font-semibold">Disetujui Direktur</h2>
                <hr/>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-8 gap-3">
            <div class="col-span-2 overflow-y-auto h-screen">
                <div class="flex flex-col gap-3">
                    @foreach ($rejects as $chirp)
                        <div class="p-6 flex bg-white shadow-sm rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-gray-800">{{ $chirp->user->name }}</span>
                                        <small class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->format('j M Y, g:i a') }}</small>
                                        @unless ($chirp->created_at->eq($chirp->updated_at))
                                            <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                                        @endunless
                                    </div>
                                    @if ($chirp->user->is(auth()->user()) || ($chirp->user->direktur == Auth::user()->id ) || ($chirp->user->manajer == Auth::user()->id))
                                        <x-dropdown>
                                            <x-slot name="trigger">
                                                <button>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    </svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                @if ($chirp->user->is(auth()->user()))
                                                <x-dropdown-link :href="route('chirps.edit', $chirp)">
                                                    {{ __('Edit') }}
                                                </x-dropdown-link>
                                                <form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
                                                    @csrf
                                                    @method('delete')
                                                    <x-dropdown-link :href="route('chirps.destroy', $chirp)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                        {{ __('Delete') }}
                                                    </x-dropdown-link>
                                                </form>
                                                @endif
                                                @if ($chirp->user->manajer == Auth::user()->id)
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="1" name="persetujuan_manajer">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="2" name="penolakan_manajer">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Reject</button>
                                                </form>
                                                @endif
                                                @if($chirp->user->direktur == Auth::user()->id)
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="1" name="persetujuan_direktur">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="2" name="penolakan_direktur">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Reject</button>
                                                </form>
                                                @endif
                                            </x-slot>
                                        </x-dropdown>
                                    @endif
                                </div>
                                <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>
                                <div class="flex justify-between items-center mt-4">
                                    <ol class="flex justify-end items-center w-full p-3 space-x-2 text-[11px] font-medium text-center text-gray-500 bg-white">
                                        <li class="flex items-center text-blue-600">
                                            Terkirim
                                            <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                            </svg>
                                        </li>
                                        @if(!$chirp->persetujuan_manajer == 0)
                                            @if($chirp->persetujuan_manajer == 1)
                                            <li class="flex items-center text-blue-600">
                                                Manajer:<span class="hidden sm:inline-flex">Disetujui</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @elseif ($chirp->persetujuan_manajer == 2)
                                            <li class="flex items-center text-red-600">
                                                Manajer:<span class="hidden sm:inline-flex">Ditolak</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @else
                                            <li class="flex items-center">
                                                Manajer:<span class="hidden sm:inline-flex">Pending</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @endif
                                        @endif

                                        @if ($chirp->persetujuan_direktur == 1)
                                        <li class="flex items-center text-blue-600">
                                            Direktur:<span class="hidden sm:inline-flex">Disetujui</span>
                                        </li>
                                        @elseif ($chirp->persetujuan_direktur == 2)
                                        <li class="flex items-center text-red-600">
                                            Direktur:<span class="hidden sm:inline-flex">Ditolak</span>
                                        </li>
                                        @else
                                        <li class="flex items-center">
                                            Direktur:<span class="hidden sm:inline-flex">Pending</span>
                                        </li>
                                        @endif
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-span-2 overflow-y-auto h-screen">
                <div class="flex flex-col gap-3">
                    @foreach ($pending as $chirp)
                        <div class="p-6 flex bg-white shadow-sm rounded-lg overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-gray-800">{{ $chirp->user->name }}</span>
                                        <small class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->format('j M Y, g:i a') }}</small>
                                        @unless ($chirp->created_at->eq($chirp->updated_at))
                                            <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                                        @endunless
                                    </div>
                                    @if ($chirp->user->is(auth()->user()) || ($chirp->user->direktur == Auth::user()->id ) || ($chirp->user->manajer == Auth::user()->id))
                                        <x-dropdown>
                                            <x-slot name="trigger">
                                                <button>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    </svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                @if ($chirp->user->is(auth()->user()))
                                                <x-dropdown-link :href="route('chirps.edit', $chirp)">
                                                    {{ __('Edit') }}
                                                </x-dropdown-link>
                                                <form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
                                                    @csrf
                                                    @method('delete')
                                                    <x-dropdown-link :href="route('chirps.destroy', $chirp)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                        {{ __('Delete') }}
                                                    </x-dropdown-link>
                                                </form>
                                                @endif
                                                @if ($chirp->user->manajer == Auth::user()->id)
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="1" name="persetujuan_manajer">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="2" name="penolakan_manajer">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Reject</button>
                                                </form>
                                                @endif
                                                @if($chirp->user->direktur == Auth::user()->id)
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="1" name="persetujuan_direktur">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="2" name="penolakan_direktur">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Reject</button>
                                                </form>
                                                @endif
                                            </x-slot>
                                        </x-dropdown>
                                    @endif
                                </div>
                                <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>
                                <div class="flex justify-between items-center mt-4">
                                    <ol class="flex justify-end items-center w-full p-3 space-x-2 text-[11px] font-medium text-center text-gray-500 bg-white">
                                        <li class="flex items-center text-blue-600">
                                            Terkirim
                                            <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                            </svg>
                                        </li>
                                        @if(!$chirp->persetujuan_manajer == 0)
                                            @if($chirp->persetujuan_manajer == 1)
                                            <li class="flex items-center text-blue-600">
                                                Manajer:<span class="hidden sm:inline-flex">Disetujui</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @elseif ($chirp->persetujuan_manajer == 2)
                                            <li class="flex items-center text-red-600">
                                                Manajer:<span class="hidden sm:inline-flex">Ditolak</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @else
                                            <li class="flex items-center">
                                                Manajer:<span class="hidden sm:inline-flex">Pending</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @endif
                                        @endif

                                        @if ($chirp->persetujuan_direktur == 1)
                                        <li class="flex items-center text-blue-600">
                                            Direktur:<span class="hidden sm:inline-flex">Disetujui</span>
                                        </li>
                                        @elseif ($chirp->persetujuan_direktur == 2)
                                        <li class="flex items-center text-red-600">
                                            Direktur:<span class="hidden sm:inline-flex">Ditolak</span>
                                        </li>
                                        @else
                                        <li class="flex items-center">
                                            Direktur:<span class="hidden sm:inline-flex">Pending</span>
                                        </li>
                                        @endif
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-span-2 overflow-y-auto h-screen">
                <div class="flex flex-col gap-3">
                    @foreach ($manajer as $chirp)
                        <div class="p-6 flex bg-white shadow-sm rounded-lg overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-gray-800">{{ $chirp->user->name }}</span>
                                        <small class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->format('j M Y, g:i a') }}</small>
                                        @unless ($chirp->created_at->eq($chirp->updated_at))
                                            <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                                        @endunless
                                    </div>
                                    @if ($chirp->user->is(auth()->user()) || ($chirp->user->direktur == Auth::user()->id ) || ($chirp->user->manajer == Auth::user()->id))
                                        <x-dropdown>
                                            <x-slot name="trigger">
                                                <button>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    </svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                @if ($chirp->user->is(auth()->user()))
                                                <x-dropdown-link :href="route('chirps.edit', $chirp)">
                                                    {{ __('Edit') }}
                                                </x-dropdown-link>
                                                <form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
                                                    @csrf
                                                    @method('delete')
                                                    <x-dropdown-link :href="route('chirps.destroy', $chirp)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                        {{ __('Delete') }}
                                                    </x-dropdown-link>
                                                </form>
                                                @endif
                                                @if ($chirp->user->manajer == Auth::user()->id)
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="1" name="persetujuan_manajer">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="2" name="penolakan_manajer">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Reject</button>
                                                </form>
                                                @endif
                                                @if($chirp->user->direktur == Auth::user()->id)
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="1" name="persetujuan_direktur">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="2" name="penolakan_direktur">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Reject</button>
                                                </form>
                                                @endif
                                            </x-slot>
                                        </x-dropdown>
                                    @endif
                                </div>
                                <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>
                                <div class="flex justify-between items-center mt-4">
                                    <ol class="flex justify-end items-center w-full p-3 space-x-2 text-[11px] font-medium text-center text-gray-500 bg-white">
                                        <li class="flex items-center text-blue-600">
                                            Terkirim
                                            <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                            </svg>
                                        </li>
                                        @if(!$chirp->persetujuan_manajer == 0)
                                            @if($chirp->persetujuan_manajer == 1)
                                            <li class="flex items-center text-blue-600">
                                                Manajer:<span class="hidden sm:inline-flex">Disetujui</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @elseif ($chirp->persetujuan_manajer == 2)
                                            <li class="flex items-center text-red-600">
                                                Manajer:<span class="hidden sm:inline-flex">Ditolak</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @else
                                            <li class="flex items-center">
                                                Manajer:<span class="hidden sm:inline-flex">Pending</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @endif
                                        @endif

                                        @if ($chirp->persetujuan_direktur == 1)
                                        <li class="flex items-center text-blue-600">
                                            Direktur:<span class="hidden sm:inline-flex">Disetujui</span>
                                        </li>
                                        @elseif ($chirp->persetujuan_direktur == 2)
                                        <li class="flex items-center text-red-600">
                                            Direktur:<span class="hidden sm:inline-flex">Ditolak</span>
                                        </li>
                                        @else
                                        <li class="flex items-center">
                                            Direktur:<span class="hidden sm:inline-flex">Pending</span>
                                        </li>
                                        @endif
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-span-2 overflow-y-auto h-screen">
                <div class="flex flex-col gap-3">
                    @foreach ($direktur as $chirp)
                        <div class="p-6 flex bg-white shadow-sm rounded-lg overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <div class="flex-1">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-gray-800">{{ $chirp->user->name }}</span>
                                        <small class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->format('j M Y, g:i a') }}</small>
                                        @unless ($chirp->created_at->eq($chirp->updated_at))
                                            <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                                        @endunless
                                    </div>
                                    @if ($chirp->user->is(auth()->user()) || ($chirp->user->direktur == Auth::user()->id ) || ($chirp->user->manajer == Auth::user()->id))
                                        <x-dropdown>
                                            <x-slot name="trigger">
                                                <button>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    </svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                @if ($chirp->user->is(auth()->user()))
                                                <x-dropdown-link :href="route('chirps.edit', $chirp)">
                                                    {{ __('Edit') }}
                                                </x-dropdown-link>
                                                <form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
                                                    @csrf
                                                    @method('delete')
                                                    <x-dropdown-link :href="route('chirps.destroy', $chirp)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                        {{ __('Delete') }}
                                                    </x-dropdown-link>
                                                </form>
                                                @endif
                                                @if ($chirp->user->manajer == Auth::user()->id)
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="1" name="persetujuan_manajer">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="2" name="penolakan_manajer">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Reject</button>
                                                </form>
                                                @endif
                                                @if($chirp->user->direktur == Auth::user()->id)
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="1" name="persetujuan_direktur">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('chirps.update', $chirp) }}">
                                                    @csrf
                                                    @method('patch')
                                                    <input type="hidden" value="2" name="penolakan_direktur">
                                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">Reject</button>
                                                </form>
                                                @endif
                                            </x-slot>
                                        </x-dropdown>
                                    @endif
                                </div>
                                <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>
                                <div class="flex justify-between items-center mt-4">
                                    <ol class="flex justify-end items-center w-full p-3 space-x-2 text-[11px] font-medium text-center text-gray-500 bg-white">
                                        <li class="flex items-center text-blue-600">
                                            Terkirim
                                            <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                            </svg>
                                        </li>
                                        @if(!$chirp->persetujuan_manajer == 0)
                                            @if($chirp->persetujuan_manajer == 1)
                                            <li class="flex items-center text-blue-600">
                                                Manajer:<span class="hidden sm:inline-flex">Disetujui</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @elseif ($chirp->persetujuan_manajer == 2)
                                            <li class="flex items-center text-red-600">
                                                Manajer:<span class="hidden sm:inline-flex">Ditolak</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @else
                                            <li class="flex items-center">
                                                Manajer:<span class="hidden sm:inline-flex">Pending</span>
                                                <svg class="w-3 h-3 ms-2 sm:ms-4 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4"/>
                                                </svg>
                                            </li>
                                            @endif
                                        @endif

                                        @if ($chirp->persetujuan_direktur == 1)
                                        <li class="flex items-center text-blue-600">
                                            Direktur:<span class="hidden sm:inline-flex">Disetujui</span>
                                        </li>
                                        @elseif ($chirp->persetujuan_direktur == 2)
                                        <li class="flex items-center text-red-600">
                                            Direktur:<span class="hidden sm:inline-flex">Ditolak</span>
                                        </li>
                                        @else
                                        <li class="flex items-center">
                                            Direktur:<span class="hidden sm:inline-flex">Pending</span>
                                        </li>
                                        @endif
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
