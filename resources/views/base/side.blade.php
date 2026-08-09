@if (Route::is('dashboard.master.*'))
<aside class="bg-white rounded-lg shadow-md scroll-show scroll-hidden">
    <div class="bg-orange-600 text-white px-4 py-2 font-bold">MENU</div>
    <ul>
        @include('base.master')
    </ul>
</aside>
@else
<aside class="bg-white rounded-lg shadow-md scroll-show scroll-hidden">
    <div class="bg-orange-600 text-white px-4 py-2 font-bold">MENU</div>
    <ul>
        @include('base.home')
    </ul>
</aside>
@endif
