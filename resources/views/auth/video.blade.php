@extends('base.layout')
@section('title', 'Video')

@section('content')
<div class="flex flex-col items-center justify-center w-full bg-gray-100 p-8">

    @if(count($items) > 0)

        @foreach($items as $val)
            <div class="w-full max-w-3xl mb-5">
                   <label class="font-semilbold text-sm capitalize mb-3">{{$val->name}}</label>
                    <iframe 
                        class="w-full h-full"
                        src="{{$val->pile}}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
            </div>
        @endforeach
        
    @else
    <div class="text-xs font-semibold text-black">Tidak Ada Data</div>        
    @endif

</div>
@endsection
