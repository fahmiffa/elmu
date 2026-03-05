@extends('base.layout')
@section('title', 'Tata Tertib')

@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6">

    <div class="font-semibold mb-3 text-xl">Tata Tertib</div>

    <form method="POST" action="{{ route('dashboard.master.tata-tertib.update') }}" class="flex flex-col">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Konten Tata Tertib</label>
            <div x-data="trixEditor()" x-init="init()" class="bg-white rounded-lg shadow-md w-full">
                <input id="tata-tertib-content" type="hidden"
                    value="{{ old('content', $item->content ?? '') }}" name="content"
                    x-ref="input">
                <trix-editor input="tata-tertib-content" x-ref="trix" class="border rounded"
                    style="min-height: 300px;"></trix-editor>
            </div>
            @error('content')
            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="bg-orange-500 text-white font-bold py-2 px-3 w-25 rounded-2xl hover:bg-orange-700 text-sm cursor-pointer">
            Simpan
        </button>
    </form>
</div>
@endsection