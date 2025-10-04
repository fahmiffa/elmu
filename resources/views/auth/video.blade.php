@extends('base.layout')
@section('title', 'Video')
@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center w-full bg-gray-100 p-4">
        <div class="relative w-full h-0 pb-[56.25%] mb-5 max-w-md rounded-3xl overflow-hidden shadow-lg bg-white">
            <!-- https://www.youtube.com/watch?v=Ug1FkmChvo0 -->
            <iframe class="absolute top-0 left-0 w-full h-full rounded-3xl"
                src="https://www.youtube.com/embed/Ug1FkmChvo0" title="YouTube video"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe> 
        </div>
    </div>
@endsection
