@extends('base.layout')
@section('title', 'Dashboard')
@section('content')
    <div class="flex bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <div class="flex-col items-center justify-center">
                <div x-data="salesChart('Pendaftaran', 'reg')" x-init="fetchData('/dashboard/chart-json/reg')" class="bg-white rounded shadow p-4" id="reg"></div>

                <div x-data="payChart('Pembayaran', 'pay')" x-init="fetchData('/dashboard/chart-json/pay')" class="bg-white rounded shadow p-4" id="pay"></div>
            </div>
        </div>
    </div>
@endsection
