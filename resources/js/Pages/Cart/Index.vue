<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCartStore } from '@/Stores/cartStore';
import {
    ShoppingCart,
    Trash2,
    Plus,
    Minus,
    ArrowRight,
    ArrowLeft,
    ShieldCheck,
    Truck,
    FileText,
    Calculator,
    PackageOpen,
    Building2,
    CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
    setting: Object,
});

const cart = useCartStore();

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const handleUpdateQty = (id, currentQty, delta) => {
    const newQty = currentQty + delta;
    if (newQty <= 0) {
        if (confirm('Hapus material ini dari keranjang?')) {
            cart.removeItem(id);
        }
    } else {
        cart.updateQuantity(id, newQty);
    }
};

const handleManualQty = (id, event) => {
    const val = parseInt(event.target.value, 10);
    if (!isNaN(val) && val > 0) {
        cart.updateQuantity(id, val);
    } else {
        event.target.value = 1;
        cart.updateQuantity(id, 1);
    }
};

const handleClearCart = () => {
    if (confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang belanja?')) {
        cart.clearCart();
    }
};
</script>

<template>
    <PublicLayout :setting="setting">
        <Head title="Keranjang Belanja - MATERIALKU" />

        <div class="py-10 bg-[#F8FAFC] min-h-[80vh]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
                    <Link :href="route('home')" class="hover:text-primary transition font-medium">Beranda</Link>
                    <span>/</span>
                    <span class="text-secondary-900 font-semibold">Keranjang Belanja</span>
                </nav>

                <!-- Stepper Progress Header -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 mb-8 shadow-xs">
                    <div class="flex items-center justify-between max-w-3xl mx-auto">
                        <!-- Step 1: Active -->
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary text-white font-bold flex items-center justify-center text-xs shadow-md shadow-primary/20">
                                1
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-xs font-bold text-secondary-900 leading-tight">Keranjang</p>
                                <p class="text-[11px] text-slate-500">Rincian Material</p>
                            </div>
                        </div>

                        <div class="flex-1 h-0.5 bg-slate-200 mx-4"></div>

                        <!-- Step 2: Next -->
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 font-bold flex items-center justify-center text-xs">
                                2
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-xs font-bold text-slate-400 leading-tight">Pengiriman</p>
                                <p class="text-[11px] text-slate-400">Alamat & Penerima</p>
                            </div>
                        </div>

                        <div class="flex-1 h-0.5 bg-slate-200 mx-4"></div>

                        <!-- Step 3: Confirmation -->
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 font-bold flex items-center justify-center text-xs">
                                3
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-xs font-bold text-slate-400 leading-tight">Selesai</p>
                                <p class="text-[11px] text-slate-400">Konfirmasi WhatsApp</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="cart.items.length === 0" class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-2xl mx-auto shadow-sm my-10">
                    <div class="w-20 h-20 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-5">
                        <PackageOpen class="w-10 h-10" />
                    </div>
                    <h2 class="font-headline font-extrabold text-2xl text-secondary-900 mb-2">
                        Keranjang Belanja Masih Kosong
                    </h2>
                    <p class="text-sm text-slate-500 max-w-md mx-auto mb-8 leading-relaxed">
                        Anda belum memasukkan material bangunan ke keranjang. Silakan jelajahi katalog lengkap atau hitung kebutuhan ruangan dengan kalkulator proyek kami.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <Link
                            :href="route('catalog.index')"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary hover:bg-primary-600 text-white rounded-xl text-xs sm:text-sm font-bold transition shadow-sm w-full sm:w-auto"
                        >
                            <span>Jelajahi Katalog Material</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <Link
                            :href="route('calculator.index')"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-300 hover:border-primary text-secondary-900 hover:text-primary rounded-xl text-xs sm:text-sm font-bold transition shadow-xs w-full sm:w-auto"
                        >
                            <Calculator class="w-4 h-4 text-primary" />
                            <span>Buka Kalkulator Proyek</span>
                        </Link>
                    </div>
                </div>

                <!-- Cart Content (2 Columns Layout) -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Left Column: Item List (8 Cols) -->
                    <div class="lg:col-span-8 space-y-4">
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <ShoppingCart class="w-5 h-5 text-primary" />
                                <h1 class="font-headline font-bold text-lg text-secondary-900">
                                    Daftar Material ({{ cart.totalCount }} barang)
                                </h1>
                            </div>
                            <button
                                @click="handleClearCart"
                                class="text-xs font-semibold text-rose-600 hover:text-rose-700 flex items-center gap-1.5 transition"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                                <span>Kosongkan Semua</span>
                            </button>
                        </div>

                        <!-- Item Cards Table / List -->
                        <div class="space-y-3">
                            <div
                                v-for="item in cart.items"
                                :key="item.id"
                                class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition hover:border-slate-300"
                            >
                                <div class="flex items-start gap-4">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200/80 flex items-center justify-center">
                                        <img
                                            v-if="item.image_path"
                                            :src="item.image_path"
                                            :alt="item.name"
                                            class="w-full h-full object-cover"
                                        />
                                        <Building2 v-else class="w-8 h-8 text-slate-400" />
                                    </div>
                                    <div class="space-y-1">
                                        <Link
                                            :href="route('catalog.show', item.slug || item.id)"
                                            class="font-headline font-bold text-sm sm:text-base text-secondary-900 hover:text-primary transition line-clamp-2"
                                        >
                                            {{ item.name }}
                                        </Link>
                                        <div class="flex flex-wrap items-center gap-2 text-xs">
                                            <span class="font-semibold text-primary">
                                                {{ formatRupiah(item.price) }}
                                            </span>
                                            <span class="text-slate-400">•</span>
                                            <span class="text-slate-500 uppercase text-[11px] font-medium">
                                                Satuan: {{ item.sale_unit }}
                                            </span>
                                        </div>
                                        <div
                                            v-if="item.calculation_snapshot"
                                            class="mt-1 text-[11px] text-tertiary-700 font-medium bg-tertiary-50 px-2 py-0.5 rounded border border-tertiary-200/60 inline-flex items-center gap-1"
                                        >
                                            <CheckCircle2 class="w-3 h-3 text-tertiary-600" />
                                            <span>Kalkulator: {{ item.calculation_snapshot.coverage_info || 'Sesuai volume proyek' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quantity Controls & Line Total -->
                                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                    <!-- Stepper Quantity -->
                                    <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                        <button
                                            type="button"
                                            @click="handleUpdateQty(item.id, item.quantity, -1)"
                                            class="p-2 hover:bg-slate-200 text-slate-600 transition"
                                            title="Kurangi kuantitas"
                                        >
                                            <Minus class="w-3.5 h-3.5" />
                                        </button>
                                        <input
                                            type="number"
                                            :value="item.quantity"
                                            @change="handleManualQty(item.id, $event)"
                                            min="1"
                                            class="w-12 text-center text-xs font-bold bg-transparent border-0 focus:ring-0 p-0 text-secondary-900 outline-none"
                                        />
                                        <button
                                            type="button"
                                            @click="handleUpdateQty(item.id, item.quantity, 1)"
                                            class="p-2 hover:bg-slate-200 text-slate-600 transition"
                                            title="Tambah kuantitas"
                                        >
                                            <Plus class="w-3.5 h-3.5" />
                                        </button>
                                    </div>

                                    <!-- Subtotal Item -->
                                    <div class="text-right min-w-[100px]">
                                        <p class="text-[11px] text-slate-400">Total Harga</p>
                                        <p class="font-headline font-bold text-sm text-secondary-900">
                                            {{ formatRupiah(item.price * item.quantity) }}
                                        </p>
                                    </div>

                                    <!-- Delete Item Button -->
                                    <button
                                        @click="cart.removeItem(item.id)"
                                        class="p-2 text-slate-400 hover:text-rose-600 transition rounded-lg hover:bg-rose-50"
                                        title="Hapus barang ini"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Back to Catalog Link -->
                        <div class="pt-2">
                            <Link
                                :href="route('catalog.index')"
                                class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:underline"
                            >
                                <ArrowLeft class="w-3.5 h-3.5" />
                                <span>Lanjutkan Belanja Material Lainnya</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Right Column: Order Summary (4 Cols - Sticky) -->
                    <div class="lg:col-span-4 sticky top-24 space-y-4">
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-5">
                            <h2 class="font-headline font-bold text-base text-secondary-900 pb-3 border-b border-slate-100">
                                Ringkasan Belanja
                            </h2>

                            <div class="space-y-3 text-xs text-slate-600">
                                <div class="flex items-center justify-between">
                                    <span>Total Jenis Material:</span>
                                    <span class="font-bold text-secondary-900">{{ cart.items.length }} jenis</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Total Kuantitas Barang:</span>
                                    <span class="font-bold text-secondary-900">{{ cart.totalCount }} sak/dus/unit</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Subtotal Material:</span>
                                    <span class="font-bold text-secondary-900">{{ formatRupiah(cart.totalPrice) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-500">
                                    <span>Ongkos Kirim Armada:</span>
                                    <span class="text-tertiary-700 font-semibold">Dihitung di Checkout (Lampung)</span>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex items-end justify-between">
                                <div>
                                    <p class="text-xs text-slate-500">Estimasi Total:</p>
                                    <p class="font-headline font-extrabold text-xl text-primary">
                                        {{ formatRupiah(cart.totalPrice) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Checkout Primary Button -->
                            <Link
                                :href="route('checkout.index')"
                                class="w-full py-3.5 px-4 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl transition shadow-md shadow-primary/20 flex items-center justify-center gap-2"
                            >
                                <span>Lanjut ke Pengiriman (Checkout)</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>

                            <!-- Trust Badges -->
                            <div class="pt-4 border-t border-slate-100 space-y-2.5 text-[11px] text-slate-500">
                                <div class="flex items-center gap-2">
                                    <Truck class="w-4 h-4 text-tertiary-600 shrink-0" />
                                    <span>Pengiriman langsung via Armada Toko Lampung</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <ShieldCheck class="w-4 h-4 text-primary shrink-0" />
                                    <span>Garansi barang utuh & ganti di tempat jika cacat</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <FileText class="w-4 h-4 text-slate-400 shrink-0" />
                                    <span>Surat jalan & nota resmi toko berstempel basah</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
