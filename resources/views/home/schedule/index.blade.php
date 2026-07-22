@extends('base.layout')
@section('title', 'Dashboard Penjadwalan')
@section('content')
<div class="flex flex-col bg-white rounded-lg shadow-md p-6" x-data="dataTableReg({{ json_encode($items) }})">

    <!-- Global Header -->
    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
        <div class="flex items-center gap-3 w-full md:w-3/4">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest whitespace-nowrap">Filter Global Unit:</span>
            <select x-model="filterUnit"
                class="w-full md:w-1/3 border border-gray-300 ring-0 rounded-xl px-3 py-1.5 focus:outline-[#FF9966] bg-white text-sm shadow-sm transition">
                <option value="">Semua Unit</option>
                @foreach ($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('dashboard.jadwal.create') }}"
            class="cursor-pointer bg-orange-500 text-sm hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-xl shadow-md transition-all">
            Tambah Jadwal
        </a>
    </div>

    <!-- Collapse / Expand all controls -->
    <div class="flex items-center gap-2 mb-4 bg-gray-50 p-3 rounded-xl border border-gray-200">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-widest mr-2">Navigasi:</span>
        <button @click="expandAllUnits()"
            class="cursor-pointer bg-white text-xs hover:bg-gray-100 text-gray-700 font-semibold py-1.5 px-3 rounded-lg border border-gray-300 shadow-sm focus:outline-none transition">
            Expand Semua
        </button>
        <button @click="collapseAllUnits(groupedUnits())"
            class="cursor-pointer bg-white text-xs hover:bg-gray-100 text-gray-700 font-semibold py-1.5 px-3 rounded-lg border border-gray-300 shadow-sm focus:outline-none transition">
            Collapse Semua
        </button>
    </div>

    <!-- Collapsible Cards by Unit -->
    <div class="space-y-6">
        <template x-for="unit in groupedUnits()" :key="unit.id">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden transition-all duration-200">
                <!-- Card Header -->
                <div @click="toggleUnit(unit.id)"
                    class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b border-gray-200 cursor-pointer select-none hover:bg-gray-100/70 transition-colors duration-150">
                    <div class="flex items-center gap-3">
                        <span class="p-2 rounded-lg bg-orange-100 text-orange-600">
                            <!-- Icon for Unit/School/Building -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </span>
                        <h3 class="text-base font-bold text-gray-800 tracking-wide uppercase" x-text="unit.name"></h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-orange-100 text-orange-800 font-semibold px-2.5 py-0.5 rounded-full"
                            x-text="unit.rows.length + ' Sesi/Siswa'"></span>
                        <!-- Chevron icon -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-gray-500 transform transition-transform duration-200"
                            :class="isCollapsed(unit.id) ? '' : 'rotate-180'"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Card Body -->
                <div x-show="!isCollapsed(unit.id)" class="p-0 overflow-x-auto">
                    <!-- Filters inside Unit Card -->
                    <div class="bg-gray-50/50 px-6 py-3 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200">
                        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                            <!-- Local Search -->
                            <div class="relative w-full md:w-64">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text"
                                    x-model="localSearch[unit.id]"
                                    placeholder="Cari siswa di unit ini..."
                                    class="w-full text-xs border border-gray-300 rounded-xl pl-9 pr-4 py-2 focus:outline-none focus:border-[#FF9966] focus:ring-1 focus:ring-[#FF9966] bg-white transition shadow-sm" />
                            </div>

                            <!-- Local Program Filter -->
                            <div class="w-full md:w-48">
                                <select x-model="localProgram[unit.id]"
                                    class="w-full text-xs border border-gray-300 rounded-xl px-3 py-2 bg-white focus:outline-none focus:border-[#FF9966] focus:ring-1 focus:ring-[#FF9966] transition shadow-sm">
                                    <option value="">Semua Program Studi</option>
                                    @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Clear local filters if active -->
                        <div x-show="localSearch[unit.id] || localProgram[unit.id]">
                            <button @click="localSearch[unit.id] = ''; localProgram[unit.id] = ''"
                                class="text-xs text-red-500 hover:text-red-700 font-semibold underline transition">
                                Reset Filter Unit
                            </button>
                        </div>
                    </div>

                    <!-- Local Empty State inside card -->
                    <div x-show="unit.rows.length === 0" class="p-8 text-center text-gray-500 border-b border-gray-200">
                        <p class="font-semibold text-gray-700 text-sm">Siswa atau Jadwal tidak ditemukan</p>
                        <p class="text-xs text-gray-400 mt-1">Coba gunakan kata kunci lain atau pilih program studi berbeda.</p>
                    </div>

                    <table x-show="unit.rows.length > 0" class="min-w-full bg-white text-sm border-collapse">
                        <thead>
                            <tr class="bg-orange-500/10 text-left text-orange-950 font-semibold border-b border-gray-200 text-xs uppercase tracking-wider">
                                <th class="px-5 py-3 border-r border-gray-200 w-1/6 text-center">Hari</th>
                                <th class="px-5 py-3 border-r border-gray-200 w-1/4">Jadwal</th>
                                <th class="px-5 py-3 border-r border-gray-200">Nama Siswa</th>
                                <th class="px-5 py-3 border-r border-gray-200">Program Studi</th>
                                <th class="px-5 py-3 text-center w-24">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, idx) in unit.rows" :key="idx">
                                <tr class="border-b border-gray-200 hover:bg-orange-50/20 transition-colors duration-100">
                                    <!-- Hari Cell -->
                                    <td x-show="row.showDay" :rowspan="row.dayRowspan"
                                        class="px-5 py-4 border-r border-gray-200 font-bold bg-orange-50/5 text-gray-800 text-center align-middle whitespace-nowrap"
                                        x-text="row.dayName">
                                    </td>

                                    <!-- Jadwal Cell -->
                                    <td x-show="row.showSession" :rowspan="row.sessionRowspan"
                                        class="px-5 py-4 border-r border-gray-200 align-middle bg-gray-50/40">
                                        <div class="font-semibold text-gray-800 capitalize" x-text="row.sessionName"></div>
                                        <div class="text-xs font-semibold text-orange-600 mt-0.5" x-text="row.sessionTime"></div>
                                    </td>

                                    <!-- Nama Cell -->
                                    <td class="px-5 py-4 border-r border-gray-200 align-top">
                                        <div class="font-medium text-gray-900" x-text="row.studentName"></div>
                                        <div class="text-xs text-gray-500 mt-0.5" x-text="'Panggilan: ' + row.studentNickname"></div>
                                    </td>

                                    <!-- Program Cell -->
                                    <td class="px-5 py-4 border-r border-gray-200 align-top">
                                        <div class="font-semibold text-gray-800" x-text="row.studentProgram"></div>
                                        <div class="text-xs text-gray-500 mt-0.5" x-text="row.studentClass"></div>
                                    </td>

                                    <!-- Action Cell -->
                                    <td class="px-5 py-4 align-middle text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a :href="'/dashboard/jadwal/' + row.studentId + '/edit'"
                                                class="text-orange-600 hover:text-orange-950 transition-colors duration-150"
                                                title="Edit Jadwal Siswa">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-pencil text-orange-600">
                                                    <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                                    <path d="m15 5 4 4" />
                                                </svg>
                                            </a>

                                            <form :action="'/dashboard/jadwal/' + row.studentId + '/hapus'" method="POST"
                                                @submit.prevent="deleteRow($event)">
                                                <input type="hidden" name="par[]" :value="row.sessionRaw.id">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:text-red-800 transition-colors duration-150 cursor-pointer"
                                                    title="Hapus Jadwal Sesi Ini">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-trash-2 text-red-500">
                                                        <path d="M10 11v6" />
                                                        <path d="M14 11v6" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                        <path d="M3 6h18" />
                                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="groupedUnits().length === 0"
            class="flex flex-col items-center justify-center p-12 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-base font-semibold text-gray-700">Jadwal tidak ditemukan</p>
            <p class="text-sm text-gray-500 mt-1">Coba sesuaikan pencarian atau filter unit / program Anda.</p>
        </div>
    </div>
</div>
@endsection