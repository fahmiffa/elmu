@extends('base.layout')
@section('title', 'Video')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center w-full bg-gray-100 p-4">

    <div class="w-full max-w-3xl">
            <iframe 
                class="w-full h-full"
                src="https://www.youtube.com/embed/3LBViQkGjlg"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
            ></iframe>
    </div>

</div>
@endsection
