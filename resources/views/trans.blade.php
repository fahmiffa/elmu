@extends('layout.base')
@section('title', 'Dashboard')
@section('content')
    <div class="flex-row p-3" x-data="posApp()" x-init="refreshPending()">
        <div class="grid md:grid-cols-2 grid-cols-1 mb-5">
            <div class="w-full bg-gray-100 p-4 overflow-y-auto" x-data="{ search: '' }">
                <div class="mb-4">
                    <input type="text" x-model="search" placeholder="Cari Items"
                        class="w-full p-2 rounded border border-gray-300 focus:outline-none focus:ring focus:border-blue-300" />
                </div>
                <div class="overflow-x-auto md:overflow-x-visible">
                    <div class="flex md:grid md:grid-cols-4 gap-4 min-w-max md:min-w-0">
                        @foreach ($products as $product)
                            <template x-if="'{{ strtolower($product->name) }}'.includes(search.toLowerCase())">
                                <button
                                    @click="addToCart({ id: {{ $product->id }}, name: '{{ $product->name }}', price: {{ $product->price }}, stok : '{{ $product->stoks }}' })"
                                    class="min-w-[140px] bg-white shadow p-2 rounded hover:bg-blue-100 transition">
                                    <div class="flex flex-col items-center justify-center">
                                        @if ($product->img)
                                            <img src="{{ asset('storage/' . $product->img) }}" alt="{{ $product->name }}"
                                                class="h-20 mx-auto mb-2 object-cover" />
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-25 w-24 h-24 text-gray-400"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <rect width="18" height="18" x="3" y="3" rx="2"
                                                    ry="2" />
                                                <circle cx="9" cy="9" r="2" />
                                                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                            </svg>
                                        @endif
                                        <p class="text-sm text-center font-semibold">{{ $product->name }}</p>
                                        <p class="text-xs text-center font-semibold">
                                            {{ number_format($product->price, 0, ',', '.') }}</p>
                                    </div>
                                </button>
                            </template>
                        @endforeach
                    </div>
                </div>

            </div>
            <div class="w-full bg-white flex flex-col p-4">
                <div class="text-sm mb-4 flex-1 overflow-y-auto">
                    <div class="font-semibold mb-2 text-xl">Transaksi</div>

                    <template x-for="item in cart" :key="item.id">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex-row">
                                <p class="font-medium text-sm" x-text="item.name"></p>
                                <p class="font-medium text-sm" x-text="item.price"></p>
                                <div class="flex items-center space-x-1">
                                    <button @click="item.qty = Math.max(item.qty - 1, 1)"
                                        class="px-2 py-1 bg-gray-300 rounded text-sm">−</button>
                                    <input type="number" min="1" :max="item.stok"
                                        class="w-12 text-center border rounded text-sm" x-model.number="item.qty"
                                        @input="item.qty = Math.min(item.qty, item.stok)">
                                    <button
                                        @click="item.qty < parseInt(item.stok) ? item.qty++ : alert('Stok tidak mencukupi!')"
                                        class="px-2
                                    py-1 bg-gray-300 rounded text-sm">+</button>
                                </div>
                            </div>
                            <div class="text-sm font-semibold ml-auto" x-text="rupiah(item.qty * item.price)"></div>
                            <button @click="removeCart(item.id)" class="ml-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2">
                                    <path d="M10 11v6" />
                                    <path d="M14 11v6" />
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                    <path d="M3 6h18" />
                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                </svg>
                            </button>
                        </div>
                    </template>

                    <template x-if="cart.length === 0">
                        <p class="text-gray-400 text-sm text-center mt-10">kosong</p>
                    </template>

                    <hr class="my-2">

                    {{-- <div class="flex justify-between text-red-500">
                        <span>Discount</span>
                        <span x-text="rupiah(discount)"></span>
                    </div>

                    <div class="flex justify-between font-medium mt-2">
                        <span>Sub-Total</span>
                        <span x-text="rupiah(subtotal)"></span>
                    </div>

                    <div class="flex justify-between">
                        <span>Service Charge (10%)</span>
                        <span x-text="rupiah(serviceCharge)"></span>
                    </div>

                    <div class="flex justify-between">
                        <span>Take Away Fee</span>
                        <span x-text="rupiah(takeawayFee)"></span>
                    </div> --}}

                    <div class="flex justify-between font-bold text-lg mt-2">
                        <span>Total</span>
                        <span x-text="rupiah(total)"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 items-center mb-3">
                    <button @click="clearCart"
                        class="bg-red-500 text-white px-4 py-2 rounded font-bold flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-brush-cleaning-icon lucide-brush-cleaning">
                            <path d="m16 22-1-4" />
                            <path
                                d="M19 13.99a1 1 0 0 0 1-1V12a2 2 0 0 0-2-2h-3a1 1 0 0 1-1-1V4a2 2 0 0 0-4 0v5a1 1 0 0 1-1 1H6a2 2 0 0 0-2 2v.99a1 1 0 0 0 1 1" />
                            <path d="M5 14h14l1.973 6.767A1 1 0 0 1 20 22H4a1 1 0 0 1-.973-1.233z" />
                            <path d="m8 22 1-4" />
                        </svg>
                        Clear
                    </button>
                    <button @click="savePending" class="bg-gray-900 text-white px-4 py-2 rounded flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-archive-icon lucide-archive">
                            <rect width="20" height="5" x="2" y="3" rx="1" />
                            <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                            <path d="M10 12h4" />
                        </svg>
                        Pending
                    </button>
                    <button @click="printBill" class="bg-gray-300 px-4 py-2 rounded flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-printer-icon lucide-printer">
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6" />
                            <rect x="6" y="14" width="12" height="8" rx="1" />
                        </svg>
                        Print
                    </button>
                    <button @click="submitCart" class="bg-blue-500 text-white px-6 py-2 rounded text-lg font-bold"
                        :disabled="isLoading" x-text="isLoading ? 'Loading...' : 'Bayar'">
                        Bayar
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-2">Pending Transaksi</h2>
            <table class="w-full text-sm border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="text-left px-2 py-1">No.</th>
                        <th class="text-left px-2 py-1">Nama</th>
                        <th class="text-left px-2 py-1">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in pending" :key="index">
                        <tr class="border-t">
                            <td class="px-2 py-1" x-text="index + 1"></td>
                            <td class="px-2 py-1" x-text="item.name"></td>
                            <td class="px-2 py-1">
                                <button @click="loadPendingByName(item.name)"
                                    class="bg-gray-800 text-white p-2 rounded"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-archive-restore-icon lucide-archive-restore">
                                        <rect width="20" height="5" x="2" y="3" rx="1" />
                                        <path d="M4 8v11a2 2 0 0 0 2 2h2" />
                                        <path d="M20 8v11a2 2 0 0 1-2 2h-2" />
                                        <path d="m9 15 3-3 3 3" />
                                        <path d="M12 12v9" />
                                    </svg></button>
                                <button @click="deletePending(item.name)"
                                    class="bg-red-500 text-white p-2 rounded text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-archive-x-icon lucide-archive-x">
                                        <rect width="20" height="5" x="2" y="3" rx="1" />
                                        <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                        <path d="m9.5 17 5-5" />
                                        <path d="m9.5 12 5 5" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
@endsection
