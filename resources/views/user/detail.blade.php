@extends('base.layout')
@section('title', 'Dashboard Master Unit')
@section('content')
    <div class="flex flex-col md:flex-row items-center justify-center bg-white rounded-lg shadow-md p-6 gap-10">
        <form @change="$event.target.form.submit()" action="{{ route('dashboard.master.user.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="flex flex-col md:flex-row items-center justify-center bg-white rounded-lg shadow-md p-6 gap-10">
                <div>
                    <img src="{{ asset('storage/' . $user->data->img) }}" class="w-50 rounded-lg shadow-sm">
                </div>
                <div class="grid grid-cols-2 gap-y-1 gap-x-10 justify-start">
                    <label class="font-semibold">Nama</label>
                    <div>{{ $user->name }}</div>

                    <label class="font-semibold">Tanggal Lahir</label>
                    <div>{{ $user->data->birth }}</div>

                    <label class="font-semibold">Email</label>
                    <div>{{ $user->email }}</div>

                    <label class="font-semibold">Nomor HP</label>
                    <div>{{ $user->nomor }}</div>

                    <label class="font-semibold">Role</label>
                    <div>{{ $user->roles }}</div>

                    <label class="font-semibold">Alamat</label>
                    <div class="text-justify">{{ $user->data->addr }}</div>

                    <label class="font-semibold">Status</label>
                    <label class="inline-flex items-center me-5 cursor-pointer gap-2">
                        <input type="checkbox" value="" class="sr-only peer" {{ $user->status == 1 ? 'checked' : '' }}>
                        <div
                            class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-100 peer-focus:ring-4 peer-focus:ring-orange-300  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all  peer-checked:bg-orange-500 dark:peer-checked:bg-orange-500">
                        </div>
                            <span class="text-sm">{{ $user->status == 1 ? 'Aktif' : 'Nonaktif' }}</span>
                    </label>
                </div>
            </div>
        </form>

    </div>
@endsection
