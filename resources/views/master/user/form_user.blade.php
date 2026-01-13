@extends('base.layout')
@section('title', $action)
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6">
    <div class="font-semibold mb-3 text-xl">{{ $action }}</div>

    <form method="POST"
        action="{{ isset($user) ? route('dashboard.master.user.update-data', $user->id) : route('dashboard.master.user.store') }}"
        class="flex flex-col">
        @csrf
        @isset($user)
        @method('PUT')
        @endisset

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Username (Login)</label>
                <input type="text" name="username" value="{{ old('username', $user->name ?? '') }}"
                    class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]" required>
                @error('username')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                    class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]" required>
                @error('email')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            @if(!isset($user))
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <div class="relative">
                    <input type="password" name="password"
                        class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]" required>
                    <button type="button" onclick="show(this)"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            <path fill-rule="evenodd"
                                d="M1.32 11.45C2.81 6.98 7.03 3.75 12 3.75c4.97 0 9.19 3.22 10.68 7.69.12.36.12.75 0 1.11C21.19 17.02 16.97 20.25 12 20.25c-4.97 0-9.19-3.22-10.68-7.69a1.76 1.76 0 0 1 0-1.11ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                @error('password')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Zona</label>
                <select name="zone_id" class="border border-gray-300 ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                    <option value="">Pilih Zona (Default All)</option>
                    @foreach($zones as $zone)
                    <option value="{{ $zone->id }}" @selected(old('zone_id', $user->zone_id ?? '') == $zone->id)>
                        {{ $zone->name }}
                    </option>
                    @endforeach
                </select>
                @error('zone_id')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-2 mt-4">
            <button type="submit"
                class="cursor-pointer bg-orange-500 text-sm hover:bg-orange-700 text-white font-bold py-2 px-6 rounded-2xl focus:outline-none focus:shadow-outline">
                Simpan
            </button>
            <a href="{{ route('dashboard.master.user') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
    const eyeIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
            <path fill-rule="evenodd" d="M1.32 11.45C2.81 6.98 7.03 3.75 12 3.75c4.97 0 9.19 3.22 10.68 7.69.12.36.12.75 0 1.11C21.19 17.02 16.97 20.25 12 20.25c-4.97 0-9.19-3.22-10.68-7.69a1.76 1.76 0 0 1 0-1.11ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
        </svg>`;

    const eyeOffIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
            <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
            <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
            <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
        </svg>
        `;

    function show(e) {
        const input = e.parentElement.querySelector('input');
        if (input) {
            input.type = input.type === 'password' ? 'text' : 'password';
            e.innerHTML = input.type === 'password' ? eyeIcon : eyeOffIcon;
        }
    }
</script>
@endpush