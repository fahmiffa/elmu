@extends('base.layout')
@section('title', 'Dashboard Master Unit')
@section('content')
    <div class="bg-white rounded-lg shadow-md p-5 md:p-6">
        <form @change="$event.target.form.submit()" action="{{ route('dashboard.master.user.update', $user->id) }}"
            method="POST">
            @csrf
            @method('PUT')
            <div class="flex flex-col md:flex-row md:items-center gap-5 md:gap-10">
                @if ($user->data->img)
                    <img src="{{ asset('storage/' . $user->data->img) }}" class="w-50">
                @else
                    <img src="https://murikaceria.co.id/v2/wp-content/uploads/2024/01/mascot300.png" class="w-50">
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-1 gap-x-10 justify-start">
                    <label class="font-semibold">Nama</label>
                    <div>{{ $user->name }}</div>

                    <label class="font-semibold">Email</label>
                    <div>{{ $user->email }}</div>

                    <label class="font-semibold">Nomor HP</label>
                    <div>{{ $user->nomor }}</div>

                    <label class="font-semibold">Role</label>
                    <div>{{ $user->roles }}</div>

                    <label class="font-semibold">Status</label>
                    <label class="inline-flex items-center me-5 cursor-pointer gap-2">
                        <input type="checkbox" value="" class="sr-only peer"
                            {{ $user->status == 1 ? 'checked' : '' }}>
                        <div
                            class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-100 peer-focus:ring-4 peer-focus:ring-orange-300  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all  peer-checked:bg-orange-500 dark:peer-checked:bg-orange-500">
                        </div>
                        <span class="text-sm">{{ $user->status == 1 ? 'Aktif' : 'Nonaktif' }}</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-center my-6">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="mx-4 text-gray-500 text-sm font-bold">DATA MURID</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 justify-start gap-y-2 gap-x-1">
                <label class="font-semibold text-sm">Alamat</label>
                <div>{{ $user->data->alamat }}</div>

                <label class="font-semibold text-sm">Tempat & Tanggal Lahiar</label>
                <div>{{ $user->data->place }}, {{ $user->data->birth }}</div>

                <label class="font-semibold text-sm">Agama</label>
                <div>{{ $user->data->agama }}</div>

                <label class="font-semibold text-sm">Nomor HP Siswa</label>
                <div>{{ $user->data->hp_siswa }}</div>

                <label class="font-semibold text-sm">Jenis Kelamin</label>
                <div>{{ $user->data->gender == 1 ? 'Laki-laki' : 'Perempuan' }}</div>

                <label class="font-semibold text-sm">Cita-cita Siswa</label>
                <div>{{ $user->data->dream }}</div>

                <label class="font-semibold text-sm">Sosmed anak</label>
                <div>{{ $user->data->sosmedChild ? $user->data->sosmedChild : '-' }}</div>

                <label class="font-semibold text-sm">Sosmed Ortu/Sdra</label>
                <div>{{ $user->data->sosmedOther ? $user->data->sosmedOther : '-' }}</div>

                <label class="font-semibold text-sm">Sekolah/Kelas</label>
                <div>{{ $user->data->sekolah_kelas }}</div>

                <label class="font-semibold text-sm">Alamat Sekolah</label>
                <div>{{ $user->data->alamat_sekolah }}</div>

                <label class="font-semibold text-sm">Pendidikan Formal Siswa</label>
                <div>{{ $user->data->study }}</div>

                <label class="font-semibold text-sm">Peringkat</label>
                <div>{{ $user->data->rank }}</div>

                <label class="font-semibold text-sm">Pendidikan Non-Formal Siswa</label>
                <div>{!! $user->data->pendidikan_non_formal !!}</div>

                <label class="font-semibold text-sm">Pestasi Akademik/Non Akademik Siswa (3 Tahun Kebelakang)</label>
                <div>{!! $user->data->prestasi !!}</div>



            </div>

            <div class="flex items-center justify-center my-6">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="mx-4 text-gray-500 text-sm font-bold">DATA ORANG TUA</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 justify-start gap-y-2 gap-x-1">
                <label class="font-semibold text-sm">Ayah</label>
                <div>{{ $user->data->dad }}</div>

                <label class="font-semibold text-sm">Pekerjaan</label>
                <div>{{ $user->data->dadJob }}</div>

                          <label class="font-semibold text-sm">Ibu</label>
                <div>{{ $user->data->mom }}</div>

                <label class="font-semibold text-sm">Pekerjaan</label>
                <div>{{ $user->data->momJob }}</div>

                <label class="font-semibold text-sm">Nomor HP Orang Tua</label>
                <div>{{ $user->data->hp_parent }}</div>

            </div>
        </form>
    </div>
@endsection
