@extends('base.layout')
@section('title', 'Dashboard')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>

        @if ($errors->any())
            <div class="text-red-500">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @isset($items)
            <form method="POST" action="{{ route('dashboard.reg.store', $items->id) }}" class="flex flex-col"
                enctype="multipart/form-data">
                @method('PUT')
            @else
                <form method="POST" action="{{ route('dashboard.reg.store') }}" class="flex flex-col"
                enctype="multipart/form-data">
                @endisset
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor Induk</label>
                        <div class="relative">
                            <input type="text" name="induk" value="{{ old('induk', $items->induk ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('induk')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Jenjang</label>
                        <select name="grade" required
                            class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih Jenjang</option>
                            <option value="pra_tk">Pra TK</option>
                            <option value="tk">TK</option>
                            <option value="sd">SD</option>
                            <option value="smp">SMP</option>
                            <option value="alumni">Alumni</option>
                        </select>

                        @error('grade')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Kelas</label>
                        <select name="kelas" required
                            class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih Kelas</option>
                            @foreach ($kelas as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                        @error('kelas')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-center my-6">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="mx-4 text-gray-500 text-sm font-bold">DATA SISWA</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ old('name', $items->name ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4" x-data="{ imagePreview: null }">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Photo</label>
                        <input type="file" name="image" accept="image/*"
                            @change="let file = $event.target.files[0]; imagePreview = URL.createObjectURL(file)"
                            class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0
                   file:text-sm file:font-semibold file:bg-blue-50 file:text-orange-700 
                   hover:file:bg-blue-100 cursor-pointer" />
                        <template x-if="imagePreview">
                            <img :src="imagePreview"
                                class="w-24 h-24 object-cover rounded border border-gray-300 my-3" />
                        </template>

                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Gender</label>
                        <select name="gender" required
                            class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih Gender</option>
                            <option value="1">Laki-laki</option>
                            <option value="2">Perempuan</option>
                        </select>

                        @error('gender')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tempat, Tanggal lahir</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="place" placeholder="Tempat lahir"
                                value="{{ old('place', $items->place ?? '') }}" required
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <input type="date" name="birth" value="{{ old('birth', $items->birth ?? '') }}" required
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Sekolah/Kelas</label>
                        <div class="relative">
                            <input type="text" name="sekolah_kelas"
                                value="{{ old('sekolah_kelas', $items->sekolah_kelas ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('sekolah_kelas')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat Sekolah</label>
                        <textarea name="alamat_sekolah"
                            class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('alamat_sekolah', $items->alamat_sekolah ?? '') }}</textarea>
                        @error('alamat_sekolah')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Cita-cita Siswa</label>
                        <div class="relative">
                            <input type="text" name="dream" value="{{ old('dream', $items->dream ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('dream')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP Siswa</label>
                        <div class="relative">
                            <input type="text" name="hp_siswa" value="{{ old('hp_siswa', $items->hp_siswa ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('hp_siswa')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Agama</label>
                        <div class="relative">
                            <input type="text" name="agama" value="{{ old('agama', $items->agama ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('agama')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4"></div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Sosmed anak</label>
                        <div class="relative">
                            <input type="text" name="sosmedChild"
                                value="{{ old('sosmedChild', $items->sosmedChild ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('sosmedChild')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Sosmed Ortu/Sdra</label>
                        <div class="relative">
                            <input type="text" name="sosmedOther"
                                value="{{ old('sosmedOther', $items->sosmedOther ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('sosmedOther')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-center my-6">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="mx-4 text-gray-500 text-sm font-bold">DATA ORANG TUA/WALI</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Ayah</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="dad" placeholder="Nama"
                                value="{{ old('dad', $items->dad ?? '') }}" required
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <input type="text" placeholder="Pekerjaan" name="dadJob"
                                value="{{ old('dadJob', $items->dadJob ?? '') }}" required
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Ibu</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="mom" placeholder="Nama"
                                value="{{ old('mom', $items->mom ?? '') }}" required
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <input type="text" placeholder="Pekerjaan" name="momJob"
                                value="{{ old('momJob', $items->momJob ?? '') }}" required
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP Orang Tua</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="hp_parent" placeholder="Nomor HP"
                                value="{{ old('hp_parent', $items->hp_parent ?? '') }}" required
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4"></div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pendidikan Formal Siswa</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="study" value="{{ old('study', $items->study ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Peringkat</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="rank" value="{{ old('rank', $items->rank ?? '') }}" `
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>

                    <div class="mb-4 col-span-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pendidikan Non-Formal Siswa</label>
                        <input id="pendidikan_non_formal" type="hidden" name="pendidikan_non_formal"
                            value="{{ old('pendidikan_non_formal', $data->pendidikan_non_formal ?? '') }}">
                        <trix-editor input="pendidikan_non_formal"
                            class="trix-content bg-transparent border rounded p-2"></trix-editor>

                    </div>

                    <div class="mb-4 col-span-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pestasi Akademik/Non Akademik Siswa
                            (3 Tahun Kebelakang)</label>
                        <input id="prestasi" type="hidden" name="prestasi"
                            value="{{ old('prestasi', $data->prestasi ?? '') }}">
                        <trix-editor input="prestasi"
                            class="trix-content bg-transparent border rounded p-2"></trix-editor>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Kontrak</label>
                        <select name="kontrak" required
                            class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih kontrak</option>
                            @foreach ($kontrak as $row)
                                <option value="{{ $row->id }}">{{ $row->name }} ({{ $row->month }} Bulan)
                                </option>
                            @endforeach
                        </select>
                        @error('kontrak')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4" ">
                                                <label class="block text-gray-700 text-sm font-semibold mb-2">Paket</label>
                                                <select name="paket" required  class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                                                    <option value="">Pilih paket</option>
                                                          @foreach ($paket as $row)
                        <option value="{{ $row->id }}">{{ $row->name }}
                        </option>
                        @endforeach
                        </select>
                        @error('paket')
                            <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <button type="submit"
                        class="cursor-pointer bg-orange-500 text-sm hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline">
                        Simpan
                    </button>
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
            <path d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
            <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
            <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
        </svg>
        `;

        function show(e) {
            const input = e.parentElement.querySelector('input[type="password"], input[type="text"]');
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
                e.innerHTML = input.type === 'password' ? eyeIcon : eyeOffIcon;
            }
        }
    </script>
@endpush
