@extends('base.layout')
@section('title', 'Dashboard')
@section('content')
    <div class="flex flex-col bg-white rounded-lg shadow-md p-6">
        <div class="font-semibold mb-3 text-xl">{{ $action }}</div>
        @isset($items)
            <form method="POST" action="{{ route('dashboard.slide.update', $items->id) }}" class="flex flex-col"
                enctype="multipart/form-data">
                @method('PUT')
            @else
                <form method="POST" action="{{ route('dashboard.slide.store') }}" class="flex flex-col"
                    enctype="multipart/form-data">
                @endisset
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="mb-4" x-data="{ imagePreview: '{{isset($items) ? asset('storage/'.$items->img) : null }}' }">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Photo</label>
                        <input type="file" name="image" accept="image/*"
                            @change="let file = $event.target.files[0]; imagePreview = URL.createObjectURL(file)"
                            class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0
                   file:text-sm file:font-semibold file:bg-blue-50 file:text-orange-700 
                   hover:file:bg-blue-100 cursor-pointer" />
                        <template x-if="imagePreview">
                            <img :src="imagePreview"
                                class="w-100 h-24 object-cover rounded border border-gray-300 my-3" />
                        </template>
                        @error('image')
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
