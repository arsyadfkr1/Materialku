<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cartStore';
import {
    ShoppingCart,
    Search,
    Phone,
    MapPin,
    Clock,
    X,
    Trash2,
    Plus,
    Minus,
    ArrowRight,
    MessageSquare,
    Calculator,
    Menu,
    CheckCircle2,
    Building2,
    ShieldCheck
} from 'lucide-vue-next';

const props = defineProps({
    setting: {
        type: Object,
        default: () => ({
            store_name: 'MATERIALKU',
            store_whatsapp: '6281234567890',
            store_address: 'Jl. Merdeka No. 123, Kota Bandung',
        }),
    },
});

const cart = useCartStore();
const isCartOpen = ref(false);
const isMobileMenuOpen = ref(false);
const searchQuery = ref('');
const toastMessage = ref('');
const showToast = ref(false);

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('catalog.index'), { search: searchQuery.value });
    }
};

const formatRupiah = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(amount);
};

const triggerToast = (msg) => {
    toastMessage.value = msg;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

// Expose toast to children via provide or window helper if needed
if (typeof window !== 'undefined') {
    window.materialkuToast = triggerToast;
}
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[#F8FAFC] text-secondary-800 font-sans selection:bg-primary-100 selection:text-primary-800">
        <!-- Toast Notification -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showToast"
                class="fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-secondary-900 text-white px-5 py-3.5 rounded-xl shadow-xl border border-secondary-700 max-w-md"
            >
                <CheckCircle2 class="w-5 h-5 text-tertiary-500 shrink-0" />
                <span class="text-sm font-medium">{{ toastMessage }}</span>
            </div>
        </transition>

        <!-- Main Header -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20 gap-4">
                    <!-- Brand Logo -->
                    <Link :href="route('home')" class="flex items-center gap-3 shrink-0">
                        <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-white shadow-md shadow-primary/20">
                            <Building2 class="w-6 h-6" />
                        </div>
                        <div class="flex flex-col">
                            <span class="font-headline font-extrabold text-2xl tracking-tight text-secondary-900 leading-none">
                                MATERIAL<span class="text-primary">KU</span>
                            </span>
                            <span class="text-[11px] text-neutral-500 font-medium tracking-wide">
                                Bangun & Renovasi Mudah
                            </span>
                        </div>
                    </Link>

                    <!-- Search Bar (Desktop) -->
                    <div class="hidden md:flex flex-1 max-w-xl mx-4">
                        <form @submit.prevent="handleSearch" class="relative w-full">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Cari semen, keramik 60x60, cat tembok..."
                                class="w-full pl-11 pr-24 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none"
                            />
                            <Search class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" />
                            <button
                                type="submit"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 px-4 py-1.5 bg-secondary-900 text-white text-xs font-semibold rounded-lg hover:bg-secondary-800 transition"
                            >
                                Cari
                            </button>
                        </form>
                    </div>

                    <!-- Right Action Buttons -->
                    <div class="flex items-center gap-3 sm:gap-4">
                        <Link
                            :href="route('calculator.index')"
                            class="hidden lg:inline-flex items-center gap-2 px-3.5 py-2 text-sm font-semibold text-primary bg-primary-50 rounded-xl hover:bg-primary-100 transition border border-primary/20"
                        >
                            <Calculator class="w-4 h-4 text-primary" />
                            <span>Kalkulator Material</span>
                        </Link>

                        <!-- Cart Button with Counter -->
                        <button
                            @click="isCartOpen = true"
                            class="relative flex items-center gap-2.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-secondary-900 rounded-xl transition border border-slate-200 font-medium"
                        >
                            <div class="relative">
                                <ShoppingCart class="w-5 h-5 text-secondary-800" />
                                <span
                                    v-if="cart.totalCount > 0"
                                    class="absolute -top-2 -right-2 bg-primary text-white text-[11px] font-bold w-5 h-5 rounded-full flex items-center justify-center animate-pulse"
                                >
                                    {{ cart.totalCount }}
                                </span>
                            </div>
                            <span class="hidden sm:inline text-xs font-bold text-secondary-800">
                                {{ formatRupiah(cart.totalPrice) }}
                            </span>
                        </button>

                        <!-- Mobile Menu Toggle -->
                        <button
                            @click="isMobileMenuOpen = !isMobileMenuOpen"
                            class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100"
                        >
                            <Menu class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <!-- Sub Navigation Bar -->
                <nav class="hidden md:flex items-center gap-8 py-3 text-sm font-semibold border-t border-slate-100">
                    <Link
                        :href="route('home')"
                        :class="route().current('home') ? 'text-primary border-b-2 border-primary pb-1' : 'text-slate-600 hover:text-primary transition'"
                    >
                        Beranda
                    </Link>
                    <Link
                        :href="route('catalog.index')"
                        :class="route().current('catalog.*') ? 'text-primary border-b-2 border-primary pb-1' : 'text-slate-600 hover:text-primary transition'"
                    >
                        Katalog Material
                    </Link>
                    <Link
                        :href="route('calculator.index')"
                        :class="route().current('calculator.*') ? 'text-primary border-b-2 border-primary pb-1' : 'text-slate-600 hover:text-primary transition'"
                    >
                        Kalkulator Proyek
                    </Link>
                    <a href="#cara-pesan" class="text-slate-600 hover:text-primary transition">
                        Cara Pemesanan
                    </a>
                    <a href="#armada-pengiriman" class="text-slate-600 hover:text-primary transition">
                        Armada Pengiriman
                    </a>
                    <a href="#kontak-toko" class="text-slate-600 hover:text-primary transition">
                        Hubungi Toko
                    </a>
                </nav>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div v-if="isMobileMenuOpen" class="md:hidden border-t border-slate-200 px-4 py-3 bg-white space-y-3">
                <form @submit.prevent="handleSearch" class="relative w-full">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Cari material..."
                        class="w-full pl-10 pr-20 py-2 text-sm bg-slate-50 border border-slate-300 rounded-lg outline-none"
                    />
                    <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1 bg-secondary-900 text-white text-xs font-semibold rounded">
                        Cari
                    </button>
                </form>
                <div class="flex flex-col gap-2 pt-2 text-sm font-semibold">
                    <Link :href="route('home')" class="py-2 text-slate-700 hover:text-primary">Beranda</Link>
                    <Link :href="route('catalog.index')" class="py-2 text-slate-700 hover:text-primary">Katalog Material</Link>
                    <Link :href="route('calculator.index')" class="py-2 text-slate-700 hover:text-primary">Kalkulator Material</Link>
                    <a href="#cara-pesan" class="py-2 text-slate-700 hover:text-primary">Cara Pemesanan</a>
                    <a href="#kontak-toko" class="py-2 text-slate-700 hover:text-primary">Hubungi Toko</a>
                    <Link :href="route('login')" class="py-2 text-primary font-bold">Masuk Admin Toko</Link>
                </div>
            </div>
        </header>

        <!-- Main Content Slot -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Slide-over Cart Drawer -->
        <div v-if="isCartOpen" class="fixed inset-0 z-50 overflow-hidden">
            <div @click="isCartOpen = false" class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                    <!-- Drawer Header -->
                    <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                        <div class="flex items-center gap-2">
                            <ShoppingCart class="w-5 h-5 text-primary" />
                            <h2 class="font-headline font-bold text-lg text-secondary-900">
                                Keranjang Belanja ({{ cart.totalCount }})
                            </h2>
                        </div>
                        <button @click="isCartOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Drawer Items List -->
                    <div class="flex-1 overflow-y-auto p-5 space-y-4">
                        <div v-if="cart.items.length === 0" class="text-center py-16">
                            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <ShoppingCart class="w-8 h-8" />
                            </div>
                            <h3 class="font-bold text-secondary-800 text-base mb-1">Keranjang Masih Kosong</h3>
                            <p class="text-sm text-neutral-500 mb-6 max-w-xs mx-auto">
                                Anda belum menambahkan barang. Pilih material dari katalog atau gunakan kalkulator.
                            </p>
                            <Link
                                :href="route('catalog.index')"
                                @click="isCartOpen = false"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-semibold text-sm rounded-xl hover:bg-primary-600 transition"
                            >
                                Buka Katalog Material
                            </Link>
                        </div>

                        <div
                            v-for="item in cart.items"
                            :key="item.id"
                            class="p-4 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition flex flex-col gap-2.5 shadow-xs"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-bold text-sm text-secondary-900 leading-snug">
                                        {{ item.name }}
                                    </h4>
                                    <span class="text-xs font-semibold text-primary">
                                        {{ formatRupiah(item.price) }} / {{ item.sale_unit }}
                                    </span>
                                </div>
                                <button
                                    @click="cart.removeItem(item.id)"
                                    class="text-slate-400 hover:text-red-500 p-1 transition"
                                    title="Hapus"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <!-- Calculation snapshot tag if present -->
                            <div
                                v-if="item.calculation_snapshot"
                                class="text-[11px] bg-primary-50 text-primary-800 px-2.5 py-1.5 rounded-lg border border-primary/20 space-y-0.5"
                            >
                                <div class="font-semibold flex items-center gap-1">
                                    <Calculator class="w-3 h-3" />
                                    <span>Hasil Kalkulasi: {{ item.calculation_snapshot.type === 'ceramic' ? 'Keramik' : 'Cat' }}</span>
                                </div>
                                <div class="text-slate-600">
                                    <template v-if="item.calculation_snapshot.type === 'ceramic'">
                                        Ukuran: {{ item.calculation_snapshot.length }}m × {{ item.calculation_snapshot.width }}m (Waste: {{ item.calculation_snapshot.waste_percent }}%)
                                    </template>
                                    <template v-else>
                                        Ukuran: {{ item.calculation_snapshot.length }}m × {{ item.calculation_snapshot.height }}m ({{ item.calculation_snapshot.coats }} lapis)
                                    </template>
                                </div>
                            </div>

                            <!-- Quantity controls & subtotal -->
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden bg-slate-50">
                                    <button
                                        @click="cart.updateQuantity(item.id, item.quantity - 1)"
                                        class="px-2.5 py-1 text-slate-600 hover:bg-slate-200 transition"
                                    >
                                        <Minus class="w-3.5 h-3.5" />
                                    </button>
                                    <span class="px-3 py-1 text-xs font-bold text-secondary-900 bg-white min-w-8 text-center">
                                        {{ item.quantity }}
                                    </span>
                                    <button
                                        @click="cart.updateQuantity(item.id, item.quantity + 1)"
                                        class="px-2.5 py-1 text-slate-600 hover:bg-slate-200 transition"
                                    >
                                        <Plus class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                                <span class="font-bold text-sm text-secondary-900">
                                    {{ formatRupiah(item.price * item.quantity) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Drawer Footer -->
                    <div v-if="cart.items.length > 0" class="p-5 border-t border-slate-200 bg-slate-50 space-y-4">
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between text-slate-600">
                                <span>Total Item</span>
                                <span class="font-semibold">{{ cart.totalCount }} barang</span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-secondary-900 pt-1 border-t border-slate-200">
                                <span>Estimasi Total</span>
                                <span class="text-primary font-extrabold text-lg">{{ formatRupiah(cart.totalPrice) }}</span>
                            </div>
                            <p class="text-[11px] text-neutral-500 italic">
                                *Belum termasuk ongkir armada toko (dikonfirmasi via WhatsApp).
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <Link
                                :href="route('checkout.index')"
                                @click="isCartOpen = false"
                                class="flex-1 flex items-center justify-center gap-2 py-3 px-4 bg-primary hover:bg-primary-600 text-white font-bold text-sm rounded-xl shadow-md shadow-primary/20 transition"
                            >
                                <span>Lanjut ke Form Checkout</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Footer -->
        <footer id="kontak-toko" class="bg-secondary-900 text-slate-400 border-t border-secondary-800 pt-16 pb-12 mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                    <!-- Column 1: Info Brand -->
                    <div class="space-y-4 md:col-span-1">
                        <div class="flex items-center gap-2.5 text-white">
                            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center text-white">
                                <Building2 class="w-5 h-5" />
                            </div>
                            <span class="font-headline font-extrabold text-xl tracking-tight text-white">
                                MATERIAL<span class="text-primary">KU</span>
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Web kalkulator kebutuhan material & pemesanan bahan bangunan cepat langsung terhubung ke armada pengiriman toko via WhatsApp.
                        </p>
                        <div class="pt-2 text-xs text-slate-400 space-y-1">
                            <p class="font-semibold text-slate-300">TB. Mandiri Sejahtera Abadi</p>
                            <p>Mitra Resmi Toko Bangunan & Material Terpercaya</p>
                        </div>
                    </div>

                    <!-- Column 2: Layanan & Alur -->
                    <div class="space-y-3">
                        <h4 class="font-headline font-bold text-sm text-white uppercase tracking-wider">Layanan Belanja</h4>
                        <ul class="text-xs space-y-2.5">
                            <li><Link :href="route('catalog.index')" class="hover:text-primary transition">Katalog Material Lengkap</Link></li>
                            <li><Link :href="route('calculator.index')" class="hover:text-primary transition">Kalkulator Kebutuhan Keramik</Link></li>
                            <li><Link :href="route('calculator.index')" class="hover:text-primary transition">Kalkulator Kebutuhan Cat</Link></li>
                            <li><a href="#cara-pesan" class="hover:text-primary transition">Cara Pemesanan Tamu</a></li>
                            <li><a href="#armada-pengiriman" class="hover:text-primary transition">Jadwal Kirim Armada Toko</a></li>
                        </ul>
                    </div>

                    <!-- Column 3: Toko Fisik & Gudang -->
                    <div class="space-y-3">
                        <h4 class="font-headline font-bold text-sm text-white uppercase tracking-wider">Lokasi Toko Fisik</h4>
                        <div class="text-xs space-y-2.5 text-slate-400">
                            <div class="flex items-start gap-2">
                                <MapPin class="w-4 h-4 text-primary shrink-0 mt-0.5" />
                                <span>{{ setting?.store_address || 'Jl. Raya Industri Timur No. 45, Kawasan Niaga Utama' }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <Clock class="w-4 h-4 text-tertiary-500 shrink-0 mt-0.5" />
                                <div>
                                    <p class="font-semibold text-slate-300">Jam Operasional Toko & Kirim:</p>
                                    <p>Senin - Sabtu: 07.30 - 17.00 WIB</p>
                                    <p>Minggu: 08.00 - 14.00 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 4: Kontak Resmi & WhatsApp -->
                    <div class="space-y-3">
                        <h4 class="font-headline font-bold text-sm text-white uppercase tracking-wider">Bantuan Pemesanan</h4>
                        <p class="text-xs text-slate-400">
                            Butuh bantuan menghitung volume proyek atau tanya stok khusus? Hubungi tim penjualan kami:
                        </p>
                        <a
                            :href="`https://wa.me/${setting?.store_whatsapp || '6281234567890'}?text=${encodeURIComponent('Halo Admin MATERIALKU, saya ingin bertanya tentang stok dan pemesanan material.')}`"
                            target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-tertiary-600 hover:bg-tertiary-500 text-white rounded-xl text-xs font-bold transition shadow-sm"
                        >
                            <MessageSquare class="w-4 h-4" />
                            <span>WhatsApp CS: +{{ setting?.store_whatsapp || '6281234567890' }}</span>
                        </a>
                        <p class="text-[11px] text-slate-500 pt-1">
                            *Pesanan tersimpan aman di database sebelum dialihkan ke WhatsApp.
                        </p>
                    </div>
                </div>

                <!-- Bottom Disclaimer & Copyright -->
                <div class="pt-8 border-t border-secondary-800 text-[11px] text-slate-500 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p>
                        © 2026 <strong>MATERIALKU</strong>. Proyek Akhir Pemrograman Web 2. All rights reserved.
                        <span class="mx-2">•</span>
                        <Link :href="route('login')" class="hover:text-slate-300 transition">Login Admin</Link>
                    </p>
                    <p class="text-center md:text-right max-w-xl italic">
                        Batas tanggung jawab: Hasil kalkulator adalah estimasi pembelian belanja, bukan jaminan kecukupan mutlak atau rekomendasi struktur profesional.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
