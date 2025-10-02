@extends('base.layout')
@section('title', 'Video')
@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center w-full bg-gray-100 p-4">
        <div class="relative w-full h-0 pb-[56.25%] mb-5 max-w-md rounded-3xl overflow-hidden shadow-lg bg-white">
            {{-- <iframe class="absolute top-0 left-0 w-full h-full rounded-3xl"
                src="https://drive.google.com/file/d/1m14IhAJ_QnyrpAcesS5VU40M30sqnrM9/preview" title="YouTube video"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe> --}}

            <video class="w-full max-w-3xl mx-auto rounded-lg shadow-lg" controls controlsList="nodownload noremoteplayback"
                disablePictureInPicture oncontextmenu="return false;">
                <source src="https://drive.google.com/file/d/1m14IhAJ_QnyrpAcesS5VU40M30sqnrM9/preview" type="video/mp4">
                Your browser does not support the video tag.
            </video>

        </div>
    </div>


@endsection
