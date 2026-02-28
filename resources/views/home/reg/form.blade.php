@extends('base.layout')
@section('title', 'Dashboard')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6">
    <div class="font-semibold mb-3 text-xl">{{ $action }}</div>


    @if ($errors->any())
    <div style="color:red">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($edit) ? route('dashboard.reg.update', md5($items->id)) : route('dashboard.reg.store') }}"
        class="flex flex-col" id="formReg" enctype="multipart/form-data" x-data="formHandler('{{ route('dashboard.reg.index') }}')"
        @submit.prevent="submit">
        @csrf
        @if(isset($edit)) @method('PUT') @endif
        <div x-data="{ jenis: '{{ old('option', isset($edit) ? '1' : '1') }}' }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2"
                x-data="reg(kelasData, { 
                    kelas: '{{ old('kelas', $items->kelas ?? '') }}', 
                    program: '{{ old('program', $items->program ?? '') }}', 
                    unit: '{{ old('unit', $items->unit ?? '') }}' 
                })">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Jenjang</label>
                    <select name="grade" required
                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        <option value="">Pilih Jenjang</option>
                        @foreach ($grade as $val)
                        <option value="{{ $val->id }}" @selected(old('grade', $items->murid->grade_id ?? '') == $val->id)>{{ $val->name }}</option>
                        @endforeach
                    </select>

                    @error('grade')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Kelas</label>
                    <select x-model="selectedKelas" name="kelas"
                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        <template x-for="(option, index) in optionsKelas" :key="index">
                            <option :value="option.value" x-text="option.label"></option>
                        </template>
                    </select>
                    @error('kelas')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Pembayaran</label>
                    <select name="kontrak" required
                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        <option value="">Pilih Pembayaran</option>
                        @foreach ($kontrak as $row)
                        <option value="{{ $row->id }}" @selected(old('kontrak', $items->payment ?? '') == $row->id)>{{ $row->name }} ({{ $row->month }} Bulan)
                        </option>
                        @endforeach
                    </select>
                    @error('kontrak')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Unit</label>
                    <select x-model="selectedUnit" name="unit"
                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"
                        required>
                        <template x-for="option in filteredUnits" :key="option.value">
                            <option :value="option.value" x-text="option.label"></option>
                        </template>
                    </select>
                    @error('unit')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Program Belajar</label>
                    <select x-model="selectedProgram" name="program"
                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]"
                        required>
                        <template x-for="option in filteredPrograms" :key="option.value">
                            <option :value="option.value" x-text="option.label"></option>
                        </template>
                    </select>
                    @error('program')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4" x-show="!{{ isset($edit) ? 'true' : 'false' }}">
                    <label class="block text-gray-700 text-sm font-semibold mb-3">Jenis Siswa</label>
                    <div class="space-y-3">
                        <label class="inline-flex items-center space-x-2">
                            <input type="radio" x-model="jenis" name="option" value="1"
                                class="form-radio text-orange-600">
                            <span class="text-gray-700">Baru</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="radio" x-model="jenis" name="option" value="2"
                                class="form-radio text-orange-600">
                            <span class="text-gray-700">Existing</span>
                        </label>
                    </div>
                </div>
                <div class="mb-4" x-show="jenis === '2'" id="old">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Pilih Murid</label>
                    <select name="murid" x-data="dropdownSelect()"
                        class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        <option value="">Pilih Murid</option>
                        @foreach ($head as $val)
                        <option value="{{ $val->id }}" @selected(old('murid')==$val->id)>{{ $val->murid->name }}</option>
                        @endforeach
                    </select>

                    @error('murid')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="new" x-show="jenis === '1'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Siswa</label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ old('name', $items->murid->name ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email', $items->murid->users->email ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('email')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Panggilan</label>
                        <div class="relative">
                            <input type="text" name="panggilan" value="{{ old('panggilan', $items->murid->nama_panggilan ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('panggilan')
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
                    <div class="mb-4" x-data="{ imagePreview: '{{ $items->murid->img ?? '' ? asset('storage/'.$items->murid->img) : '' }}' }">
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
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat Utama</label>
                        <textarea name="alamat" class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('alamat', $items->murid->alamat ?? '') }}</textarea>
                        @error('alamat')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Gender</label>
                        <select name="gender"
                            class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <option value="">Pilih Gender</option>
                            <option value="1" @selected(old('gender', $items->murid->gender ?? '') == 1)>Laki-laki</option>
                            <option value="2" @selected(old('gender', $items->murid->gender ?? '') == 2)>Perempuan</option>
                        </select>

                        @error('gender')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Tempat, Tanggal lahir</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="place" placeholder="Tempat lahir"
                                value="{{ old('place', $items->murid->place ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <input type="date" name="birth" value="{{ old('birth', $items->murid->birth ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Sekolah/Kelas</label>
                        <div class="relative">
                            <input type="text" name="sekolah_kelas"
                                value="{{ old('sekolah_kelas', $items->murid->sekolah_kelas ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('sekolah_kelas')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat Sekolah</label>
                        <textarea name="alamat_sekolah"
                            class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('alamat_sekolah', $items->murid->alamat_sekolah ?? '') }}</textarea>
                        @error('alamat_sekolah')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Cita-cita Siswa</label>
                        <div class="relative">
                            <input type="text" name="dream" value="{{ old('dream', $items->murid->dream ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('dream')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP Siswa</label>
                        <div class="relative">
                            <input type="text" name="hp_siswa"
                                value="{{ old('hp_siswa', $items->murid->hp_siswa ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                        @error('hp_siswa')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Agama</label>
                        <div class="relative">
                            <input type="text" name="agama" value="{{ old('agama', $items->murid->agama ?? '') }}"
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
                                value="{{ old('sosmedChild', $items->murid->sosmedChild ?? '') }}"
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
                                value="{{ old('sosmedOther', $items->murid->sosmedOther ?? '') }}"
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
                                value="{{ old('dad', $items->murid->dad ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <input type="text" placeholder="Pekerjaan" name="dadJob"
                                value="{{ old('dadJob', $items->murid->dadJob ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Ibu</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="mom" placeholder="Nama"
                                value="{{ old('mom', $items->murid->mom ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                            <input type="text" placeholder="Pekerjaan" name="momJob"
                                value="{{ old('momJob', $items->murid->momJob ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP Orang Tua</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="hp_parent" placeholder="Nomor HP"
                                value="{{ old('hp_parent', $items->murid->hp_parent ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4"></div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pendidikan Formal
                            Siswa</label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="study" value="{{ old('study', $items->murid->study ?? '') }}"
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Peringkat</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="rank" value="{{ old('rank', $items->murid->rank ?? '') }}" `
                                class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                        </div>
                    </div>

                    <div class="mb-4 col-span-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pendidikan Non-Formal
                            Siswa</label>
                        <input id="pendidikan_non_formal" type="hidden" name="pendidikan_non_formal"
                            value="{{ old('pendidikan_non_formal', $items->murid->pendidikan_non_formal ?? '') }}">
                        <trix-editor input="pendidikan_non_formal"
                            class="trix-content bg-transparent border rounded p-2"></trix-editor>

                    </div>

                    <div class="mb-4 col-span-2">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Pestasi Akademik/Non Akademik
                            Siswa
                            (3 Tahun Kebelakang)</label>
                        <input id="prestasi" type="hidden" name="prestasi"
                            value="{{ old('prestasi', $items->murid->prestasi ?? '') }}">
                        <trix-editor input="prestasi"
                            class="trix-content bg-transparent border rounded p-2"></trix-editor>
                    </div>

                </div>
            </div>
        </div>
        <div class="flex items-center">
            <button type="submit" :disabled="loading"
                class="cursor-pointer bg-orange-500 text-sm hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-2xl focus:outline-none focus:shadow-outline flex items-center gap-2">
                <template x-if="loading">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </template>
                <span x-text="loading ? 'Sedang Menyimpan...' : 'Simpan'"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
    window.kelasData = @json($kelas);
</script>
@endpush