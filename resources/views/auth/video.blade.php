@extends('base.layout')
@section('title', 'Video')
@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center w-full bg-gray-100 p-4">
        @foreach ($items as $row)
            <div class="flex-row">
                <div class="font-semibold">{{ $row->name }}</div>
                <video class="w-100 h-50 object-cover my-3" @play="playing = true" @pause="playing = false" controls
                    src="{{ asset('storage/' . $row->pile) }}" type="video/mp4"></video>
            </div>
        @endforeach
    </div>
@endsection
