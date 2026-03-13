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

    <form method="POST" action="{{ route('dashboard.master.student.update', ['student' => $student]) }}"
        class="flex flex-col" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Jenjang</label>
                <select name="grade" required
                    class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                    <option value="">Pilih Jenjang</option>
                    @foreach ($grade as $val)
                    <option value="{{ $val->id }}" @selected($val->id == $student->grade_id)>{{ $val->name }}</option>
                    @endforeach
                </select>
                @error('grade')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Siswa</label>
                <div class="relative">
                    <input type="text" name="name" value="{{ old('name', $student->name ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full">
                </div>
                @error('name')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email', $student->users->email ?? '') }}"
                        required
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
                @error('email')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="mb-4" x-data="{ 
                    imagePreview: '{{ $student->img ? asset('storage/' . $student->img) : null }}',
                    async handleImage(e) {
                        const file = e.target.files[0];
                        if (!file) return;
                        
                        // Show preview immediately
                        this.imagePreview = URL.createObjectURL(file);
                        
                        try {
                            const compressedFile = await window.compressImage(file);
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressedFile);
                            e.target.files = dataTransfer.files;
                        } catch (error) {
                            console.error('Compression failed:', error);
                        }
                    }
                }">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Photo</label>
                <input type="file" name="image" accept="image/*"
                    @change="handleImage"
                    class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0
                   file:text-sm file:font-semibold file:bg-blue-50 file:text-orange-700 
                   hover:file:bg-blue-100 cursor-pointer" />
                <template x-if="imagePreview">
                    <img :src="imagePreview" class="w-24 h-24 object-cover rounded border border-gray-300 my-3" />
                </template>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
                <textarea name="alamat" class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('alamat', $student->alamat ?? '') }}</textarea>
                @error('alamat')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Gender</label>
                <select name="gender"
                    class="block border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                    <option value="">Pilih Gender</option>
                    <option value="1" @selected($student->gender == '1')>Laki-laki</option>
                    <option value="2" @selected($student->gender == '2')>Perempuan</option>
                </select>

                @error('gender')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tempat, Tanggal lahir</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="place" placeholder="Tempat lahir"
                        value="{{ old('place', $student->place ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                    <input type="date" name="birth" value="{{ old('birth', $student->birth ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Sekolah/Kelas</label>
                <div class="relative">
                    <input type="text" name="sekolah_kelas"
                        value="{{ old('sekolah_kelas', $student->sekolah_kelas ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
                @error('sekolah_kelas')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat Sekolah</label>
                <textarea name="alamat_sekolah"
                    class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">{{ old('alamat_sekolah', $student->alamat_sekolah ?? '') }}</textarea>
                @error('alamat_sekolah')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Cita-cita Siswa</label>
                <div class="relative">
                    <input type="text" name="dream" value="{{ old('dream', $student->dream ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
                @error('dream')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP Siswa</label>
                <div class="relative">
                    <input type="text" name="hp_siswa" value="{{ old('hp_siswa', $student->hp_siswa ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
                @error('hp_siswa')
                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Agama</label>
                <div class="relative">
                    <input type="text" name="agama" value="{{ old('agama', $student->agama ?? '') }}"
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
                        value="{{ old('sosmedChild', $student->sosmedChild ?? '') }}"
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
                        value="{{ old('sosmedOther', $student->sosmedOther ?? '') }}"
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
                        value="{{ old('dad', $student->dad ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                    <input type="text" placeholder="Pekerjaan" name="dadJob"
                        value="{{ old('dadJob', $student->dadJob ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Ibu</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="mom" placeholder="Nama"
                        value="{{ old('mom', $student->mom ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                    <input type="text" placeholder="Pekerjaan" name="momJob"
                        value="{{ old('momJob', $student->momJob ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Nomor HP Orang Tua</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="hp_parent" placeholder="Nomor HP"
                        value="{{ old('hp_parent', $student->hp_parent ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
            </div>
            <div class="mb-4"></div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Pendidikan Formal Siswa</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="study" value="{{ old('study', $student->study ?? '') }}"
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Peringkat</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="rank" value="{{ old('rank', $student->rank ?? '') }}" `
                        class="border border-gray-300  ring-0 rounded-xl px-3 py-2 w-full focus:outline-[#FF9966]">
                </div>
            </div>
            <div class="mb-4 col-span-2">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Pendidikan Non-Formal Siswa</label>
                <input id="pendidikan_non_formal" type="hidden" name="pendidikan_non_formal"
                    value="{{ old('pendidikan_non_formal', $student->pendidikan_non_formal ?? '') }}">
                <trix-editor input="pendidikan_non_formal"
                    class="trix-content bg-transparent border rounded p-2"></trix-editor>

            </div>
            <div class="mb-4 col-span-2">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Pestasi Akademik/Non Akademik Siswa
                    (3 Tahun Kebelakang)</label>
                <input id="prestasi" type="hidden" name="prestasi"
                    value="{{ old('prestasi', $student->prestasi ?? '') }}">
                <trix-editor input="prestasi" class="trix-content bg-transparent border rounded p-2"></trix-editor>
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
@push('script')
<script>
    window.compressImage = function(file, {
        quality = 0.6,
        maxWidth = 800,
        maxHeight = 800
    } = {}) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (!blob) {
                            reject(new Error('Canvas to Blob failed'));
                            return;
                        }
                        resolve(new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        }));
                    }, 'image/jpeg', quality);
                };
                img.onerror = reject;
            };
            reader.onerror = reject;
        });
    }
</script>
@endpush
@endsection