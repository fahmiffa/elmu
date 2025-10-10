@extends('base.layout')
@section('title', 'Video')
@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center w-full bg-gray-100 p-4">
        @foreach ($items as $row)
            <div class="relative w-full h-0 pb-[56.25%] mb-5 max-w-md rounded-3xl overflow-hidden shadow-lg bg-white">
                <video class="w-full h-full object-cover" controls :src="'{{ asset('storage') }}/' + row.pile"
                    type="video/mp4"></video>
            </div>
        @endforeach
    </div>
@endsection
