@extends('base.layout')

@section('title', 'Notifikasi')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Notifikasi</h1>
        @if(Auth::user()->unreadNotifications->count() > 0)
        <form action="{{ route('dashboard.notifications.mark-read') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">Tandai semua telah dibaca</button>
        </form>
        @endif
    </div>

    <div class="space-y-4">
        @forelse($notifications as $notification)
        <div class="flex items-start p-4 border rounded-lg {{ $notification->read_at ? 'bg-white opacity-70' : 'bg-blue-50 border-blue-100' }}">
            <div class="flex-shrink-0 mr-4">
                <div class="p-2 rounded-full {{ $notification->read_at ? 'bg-gray-100 text-gray-500' : 'bg-blue-100 text-blue-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
            <div class="flex-grow">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold {{ $notification->read_at ? 'text-gray-700' : 'text-blue-900' }}">
                        {{ $notification->data['message'] ?? 'Notifikasi Baru' }}
                    </h3>
                    <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $notification->data['reason'] ?? '' }}
                </p>
                @if(isset($notification->data['url']))
                <a href="{{ $notification->data['url'] }}" class="text-xs text-blue-600 hover:underline mt-2 inline-block">Lihat Detail</a>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p>Tidak ada notifikasi saat ini.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection