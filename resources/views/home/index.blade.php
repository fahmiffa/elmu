@extends('base.layout')
@section('title', 'Dashboard')
@section('content')
<div x-data="payChart('Pembayaran', 'pay')" x-init="fetchData('/dashboard/chart-json/pay')">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <!-- Paid Card -->
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500 overflow-hidden relative">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm text-gray-500 font-medium tracking-wider uppercase">Tagihan Terbayar</p>
                    <h3 class="text-3xl font-extrabold text-gray-800 mt-2" x-text="'Rp ' + formatNumber(paidTotal)"></h3>
                    <p class="text-xs text-gray-400 mt-1" x-text="(months[selectedMonth-1] || '') + ' ' + selectedYear"></p>
                </div>
                <div class="p-4 bg-green-50 rounded-2xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Unpaid Card -->
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500 overflow-hidden relative">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm text-gray-500 font-medium tracking-wider uppercase">Belum Terbayar</p>
                    <h3 class="text-3xl font-extrabold text-gray-800 mt-2" x-text="'Rp ' + formatNumber(unpaidTotal)"></h3>
                    <p class="text-xs text-gray-400 mt-1" x-text="(months[selectedMonth-1] || '') + ' ' + selectedYear"></p>
                </div>
                <div class="p-4 bg-red-50 rounded-2xl">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section with Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Analisis Pembayaran</h2>
                <p class="text-sm text-gray-500">Grafik perbandingan status pembayaran per bulan</p>
            </div>

            <div class="flex items-center gap-3">
                <select x-model="selectedMonth" @change="updateChart()" class="border border-gray-300 rounded-xl px-4 py-2 focus:outline-[#FF9966] text-sm font-medium bg-gray-50">
                    <template x-for="(month, index) in months" :key="index">
                        <option :value="index + 1" x-text="month"></option>
                    </template>
                </select>

                <select x-model="selectedYear" @change="updateChart()" class="border border-gray-300 rounded-xl px-4 py-2 focus:outline-[#FF9966] text-sm font-medium bg-gray-50">
                    <template x-for="year in years" :key="year">
                        <option :value="year" x-text="year"></option>
                    </template>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto scroll-show">
            <div id="pay" class="w-full flex justify-center"></div>
        </div>
    </div>
</div>

@endsection