<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCartStore } from '@/Stores/cartStore';
import {
    Calculator,
    ShoppingCart,
    Truck,
    ShieldCheck,
    CheckCircle2,
    MessageSquare,
    ArrowRight,
    Star,
    Layers,
    Sparkles,
    Check,
    MapPin,
    Clock,
    Phone,
    HelpCircle,
    Info,
    ExternalLink,
    BrickWall,
    DoorClosed
} from 'lucide-vue-next';

const props = defineProps({
    setting: Object,
    categories: Array,
    featuredMaterials: Array,
    heroMaterials: Object,
});

const cart = useCartStore();

// --- State Hero Calculator ---
const activeTab = ref('ceramic'); // 'ceramic' or 'paint'

// Ceramic State
const ceramicLength = ref(4);
const ceramicWidth = ref(3);
const ceramicWaste = ref(10); // 10%
const ceramicCoverage = ref(props.heroMaterials?.ceramic?.coverage_per_box || 1.44);
const ceramicPrice = ref(props.heroMaterials?.ceramic?.price || 85000);

const ceramicArea = computed(() => {
    return Math.round(ceramicLength.value * ceramicWidth.value * 100) / 100;
});
const ceramicRequiredArea = computed(() => {
    return Math.round(ceramicArea.value * (1 + ceramicWaste.value / 100) * 100) / 100;
});
const ceramicBoxes = computed(() => {
    if (ceramicCoverage.value <= 0) return 0;
    return Math.ceil(ceramicRequiredArea.value / ceramicCoverage.value);
});
const ceramicTotalCost = computed(() => {
    return ceramicBoxes.value * ceramicPrice.value;
});

// Paint State
const paintLength = ref(10);
const paintHeight = ref(3);
const paintOpeningArea = ref(0);
const paintCoats = ref(2);
const paintSpread = ref(props.heroMaterials?.paint?.spread_per_liter_per_coat || 10);
const paintVolume = ref(props.heroMaterials?.paint?.package_volume_liters || 5);
const paintWaste = ref(10);
const paintPrice = ref(props.heroMaterials?.paint?.price || 150000);

const paintGrossArea = computed(() => {
    return Math.round(paintLength.value * paintHeight.value * 100) / 100;
});
const paintNetArea = computed(() => {
    return Math.max(0, Math.round((paintGrossArea.value - paintOpeningArea.value) * 100) / 100);
});
const paintLitersNeeded = computed(() => {
    if (paintSpread.value <= 0) return 0;
    const liters = (paintNetArea.value * paintCoats.value / paintSpread.value) * (1 + paintWaste.value / 100);
    return Math.round(liters * 100) / 100;
});
const paintCans = computed(() => {
    if (paintVolume.value <= 0) return 0;
    return Math.ceil(paintLitersNeeded.value / paintVolume.value);
});
const paintTotalCost = computed(() => {
    return paintCans.value * paintPrice.value;
});

// Format Rupiah Helper
const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

// Add Hero Calculation directly to Cart
const addHeroCalculationToCart = () => {
    if (activeTab.value === 'ceramic') {
        const prod = props.heroMaterials?.ceramic || {
            id: 1,
            name: 'Keramik Granit Motif Marmer 60x60',
            price: 85000,
            sale_unit: 'dus',
        };
        cart.addItem(prod, ceramicBoxes.value, {
            type: 'ceramic',
            length: ceramicLength.value,
            width: ceramicWidth.value,
            waste_percent: ceramicWaste.value,
            boxes: ceramicBoxes.value,
        });
        if (window.materialkuToast) {
            window.materialkuToast(`Berhasil menambahkan ${ceramicBoxes.value} dus ${prod.name} ke keranjang!`);
        }
    } else {
        const prod = props.heroMaterials?.paint || {
            id: 3,
            name: 'Cat Tembok Interior Premium Putih 5L',
            price: 150000,
            sale_unit: 'kaleng',
        };
        cart.addItem(prod, paintCans.value, {
            type: 'paint',
            length: paintLength.value,
            height: paintHeight.value,
            coats: paintCoats.value,
            waste_percent: paintWaste.value,
            cans: paintCans.value,
        });
        if (window.materialkuToast) {
            window.materialkuToast(`Berhasil menambahkan ${paintCans.value} kaleng ${prod.name} ke keranjang!`);
        }
    }
};

// Active category filter for featured section
const selectedCategoryFilter = ref('all');
const filteredFeaturedMaterials = computed(() => {
    if (selectedCategoryFilter.value === 'all') {
        return props.featuredMaterials || [];
    }
    return (props.featuredMaterials || []).filter(
        (m) => m.category?.slug === selectedCategoryFilter.value
    );
});

// Direct add to cart from card
const addProductDirect = (product) => {
    cart.addItem(product, 1);
    if (window.materialkuToast) {
        window.materialkuToast(`${product.name} telah ditambahkan ke keranjang.`);
    }
};
</script>

<template>
    <PublicLayout :setting="setting">
        <Head title="MATERIALKU - Web Pemesanan Produk Material Bangunan" />

        <!-- 1. HERO SECTION -->
        <section class="relative bg-gradient-to-b from-white via-slate-50 to-[#F8FAFC] pt-10 pb-16 lg:pt-14 lg:pb-24 border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Left Column: Copywriting & Value Propositions -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Small Badge -->
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-50 border border-primary-200 text-primary-700 text-xs font-bold tracking-wide">
                            <Truck class="w-4 h-4 text-primary" />
                            <span>Pengiriman via Armada Sendiri (Truk, Pickup & Tronton)</span>
                        </div>

                        <!-- Main Headline -->
                        <h1 class="font-headline font-extrabold text-3xl sm:text-4xl lg:text-5xl text-secondary-900 tracking-tight leading-[1.15]">
                            Hitung Material Akurat, <br class="hidden sm:inline" />
                            <span class="text-primary underline decoration-primary-200 decoration-wavy decoration-2">
                                Belanja Bangunan
                            </span>
                            Tanpa Ribet Bikin Akun.
                        </h1>

                        <!-- Subtitle -->
                        <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl font-normal">
                            Masukkan ukuran luas bidang kerja & kalkulasi otomatis sampai ke sub-satuan dus atau kaleng. Sudah teroptimasi dengan cadangan/waste dispensasi. Format pesanan langsung terintegrasi reguler WhatsApp toko resmi mitra.
                        </p>

                        <!-- CTA Buttons -->
                        <div class="flex flex-wrap items-center gap-3.5 pt-2">
                            <Link
                                :href="route('calculator.index')"
                                class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 bg-primary hover:bg-primary-600 text-white font-headline font-bold text-sm rounded-xl shadow-lg shadow-primary/25 transition transform hover:-translate-y-0.5"
                            >
                                <Calculator class="w-4 h-4" />
                                <span>Mulai Kalkulator Proyek</span>
                            </Link>

                            <Link
                                :href="route('catalog.index')"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-white hover:bg-slate-50 text-secondary-800 border-2 border-slate-300 font-headline font-bold text-sm rounded-xl transition hover:border-slate-400"
                            >
                                <span>Lihat Semua Kategori Toko</span>
                            </Link>
                        </div>

                        <!-- 4 Selling Points Badges -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-6 border-t border-slate-200/80">
                            <div class="p-3 bg-white rounded-xl border border-slate-200/80 shadow-xs">
                                <div class="w-7 h-7 rounded-lg bg-primary-50 text-primary flex items-center justify-center mb-2">
                                    <ShieldCheck class="w-4 h-4" />
                                </div>
                                <h4 class="font-bold text-xs text-secondary-900">100% Produk Asli</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Langsung dari pabrik</p>
                            </div>

                            <div class="p-3 bg-white rounded-xl border border-slate-200/80 shadow-xs">
                                <div class="w-7 h-7 rounded-lg bg-tertiary-50 text-tertiary-600 flex items-center justify-center mb-2">
                                    <CheckCircle2 class="w-4 h-4" />
                                </div>
                                <h4 class="font-bold text-xs text-secondary-900">Transaksi Aman</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Harga resmi server</p>
                            </div>

                            <div class="p-3 bg-white rounded-xl border border-slate-200/80 shadow-xs">
                                <div class="w-7 h-7 rounded-lg bg-primary-50 text-primary flex items-center justify-center mb-2">
                                    <MessageSquare class="w-4 h-4" />
                                </div>
                                <h4 class="font-bold text-xs text-secondary-900">Konfirmasi WA</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Format order otomatis</p>
                            </div>

                            <div class="p-3 bg-white rounded-xl border border-slate-200/80 shadow-xs">
                                <div class="w-7 h-7 rounded-lg bg-secondary-100 text-secondary-800 flex items-center justify-center mb-2">
                                    <Sparkles class="w-4 h-4" />
                                </div>
                                <h4 class="font-bold text-xs text-secondary-900">Bebas Bikin Akun</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Checkout tamu instan</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Interactive Quick Calculator Card -->
                    <div class="lg:col-span-5">
                        <div class="bg-white rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden">
                            <!-- Card Header -->
                            <div class="bg-secondary-900 text-white p-5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white">
                                        <Calculator class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <h3 class="font-headline font-bold text-base">Kalkulator Cepat Ruang</h3>
                                        <p class="text-xs text-slate-400">Estimasi akurat satuan jual & biaya</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 bg-tertiary-500/20 text-tertiary-400 border border-tertiary-500/30 text-[11px] font-semibold px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary-400 animate-ping"></span>
                                    Live Hitung
                                </span>
                            </div>

                            <!-- Tabs Toggle -->
                            <div class="grid grid-cols-2 p-1.5 bg-slate-100 border-b border-slate-200 text-xs font-bold">
                                <button
                                    @click="activeTab = 'ceramic'"
                                    :class="activeTab === 'ceramic' ? 'bg-white text-primary shadow-xs' : 'text-slate-600 hover:text-secondary-900'"
                                    class="py-2.5 rounded-xl transition font-headline"
                                >
                                    Keramik Lantai (Dus)
                                </button>
                                <button
                                    @click="activeTab = 'paint'"
                                    :class="activeTab === 'paint' ? 'bg-white text-primary shadow-xs' : 'text-slate-600 hover:text-secondary-900'"
                                    class="py-2.5 rounded-xl transition font-headline"
                                >
                                    Cat Dinding & Plafon (Kaleng)
                                </button>
                            </div>

                            <!-- Form Body -->
                            <div class="p-5 space-y-4 text-xs">
                                <!-- TAB 1: KERAMIK -->
                                <div v-if="activeTab === 'ceramic'" class="space-y-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-bold text-secondary-800 mb-1">Panjang Ruang (P)</label>
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    v-model.number="ceramicLength"
                                                    min="0.5"
                                                    step="0.5"
                                                    class="w-full pr-8 py-2 text-sm font-semibold rounded-lg border-slate-300 focus:border-primary focus:ring-primary/20"
                                                />
                                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-medium">m</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block font-bold text-secondary-800 mb-1">Lebar Ruang (L)</label>
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    v-model.number="ceramicWidth"
                                                    min="0.5"
                                                    step="0.5"
                                                    class="w-full pr-8 py-2 text-sm font-semibold rounded-lg border-slate-300 focus:border-primary focus:ring-primary/20"
                                                />
                                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-medium">m</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <label class="font-bold text-secondary-800">Cadangan / Potongan (Waste Buffer)</label>
                                            <span class="font-bold text-primary">{{ ceramicWaste }}%</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <button
                                                type="button"
                                                v-for="w in [5, 10, 15]"
                                                :key="w"
                                                @click="ceramicWaste = w"
                                                :class="ceramicWaste === w ? 'bg-primary text-white border-primary' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                                class="py-1.5 rounded-lg border font-semibold text-xs transition"
                                            >
                                                {{ w }}% {{ w === 10 ? '(Standar)' : '' }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Calculation Breakdown Box -->
                                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                                        <div class="flex justify-between text-slate-600">
                                            <span>Luas Bersih Lantai:</span>
                                            <span class="font-semibold text-secondary-900">{{ ceramicArea }} m²</span>
                                        </div>
                                        <div class="flex justify-between text-slate-600">
                                            <span>Kebutuhan + Waste ({{ ceramicWaste }}%):</span>
                                            <span class="font-semibold text-secondary-900">{{ ceramicRequiredArea }} m²</span>
                                        </div>
                                        <div class="pt-2 border-t border-slate-200 flex items-baseline justify-between">
                                            <div>
                                                <span class="text-[11px] text-slate-500 block">Rekomendasi Beli:</span>
                                                <span class="font-headline font-extrabold text-2xl text-primary leading-none">
                                                    {{ ceramicBoxes }} Dus
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-[11px] text-slate-500 block">Estimasi Biaya:</span>
                                                <span class="font-headline font-bold text-base text-secondary-900">
                                                    {{ formatRupiah(ceramicTotalCost) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 2: CAT -->
                                <div v-else class="space-y-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-bold text-secondary-800 mb-1">Panjang Dinding</label>
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    v-model.number="paintLength"
                                                    min="1"
                                                    step="0.5"
                                                    class="w-full pr-8 py-2 text-sm font-semibold rounded-lg border-slate-300 focus:border-primary focus:ring-primary/20"
                                                />
                                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-medium">m</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block font-bold text-secondary-800 mb-1">Tinggi Dinding</label>
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    v-model.number="paintHeight"
                                                    min="1"
                                                    step="0.5"
                                                    class="w-full pr-8 py-2 text-sm font-semibold rounded-lg border-slate-300 focus:border-primary focus:ring-primary/20"
                                                />
                                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-medium">m</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-bold text-secondary-800 mb-1">Luas Bukaan (Pintu/Jendela)</label>
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    v-model.number="paintOpeningArea"
                                                    min="0"
                                                    step="0.5"
                                                    class="w-full pr-8 py-2 text-sm font-semibold rounded-lg border-slate-300 focus:border-primary focus:ring-primary/20"
                                                />
                                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-medium">m²</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block font-bold text-secondary-800 mb-1">Jumlah Lapisan</label>
                                            <div class="grid grid-cols-2 gap-1">
                                                <button
                                                    type="button"
                                                    v-for="c in [2, 3]"
                                                    :key="c"
                                                    @click="paintCoats = c"
                                                    :class="paintCoats === c ? 'bg-primary text-white border-primary' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                                    class="py-2 rounded-lg border font-semibold text-xs transition"
                                                >
                                                    {{ c }} Lapis
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Calculation Breakdown Box -->
                                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                                        <div class="flex justify-between text-slate-600">
                                            <span>Luas Bersih Dinding:</span>
                                            <span class="font-semibold text-secondary-900">{{ paintNetArea }} m²</span>
                                        </div>
                                        <div class="flex justify-between text-slate-600">
                                            <span>Liter Cat Dibutuhkan:</span>
                                            <span class="font-semibold text-secondary-900">{{ paintLitersNeeded }} Liter</span>
                                        </div>
                                        <div class="pt-2 border-t border-slate-200 flex items-baseline justify-between">
                                            <div>
                                                <span class="text-[11px] text-slate-500 block">Rekomendasi Beli:</span>
                                                <span class="font-headline font-extrabold text-2xl text-primary leading-none">
                                                    {{ paintCans }} Kaleng (5L)
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-[11px] text-slate-500 block">Estimasi Biaya:</span>
                                                <span class="font-headline font-bold text-base text-secondary-900">
                                                    {{ formatRupiah(paintTotalCost) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button: Add to Cart -->
                                <button
                                    @click="addHeroCalculationToCart"
                                    type="button"
                                    class="w-full flex items-center justify-center gap-2 py-3.5 bg-primary hover:bg-primary-600 text-white font-headline font-bold text-sm rounded-xl shadow-md shadow-primary/20 transition transform active:scale-98"
                                >
                                    <ShoppingCart class="w-4 h-4" />
                                    <span>+ Masukkan Hasil Hitungan ke Keranjang</span>
                                </button>

                                <p class="text-[11px] text-center text-slate-400 italic">
                                    *Perhitungan estimasi otomatis berdasarkan spesifikasi kemasan toko mitra resmi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. KATEGORI MATERIAL TERBANYAK DICARI -->
        <section class="py-16 bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-primary font-bold text-xs uppercase tracking-widest font-headline">
                        TEMUKAN MATERIAL KEBUTUHAN ANDA
                    </span>
                    <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 mt-1">
                        Kategori Material Terbanyak Dicari
                    </h2>
                    <p class="text-sm text-slate-500 mt-2">
                        Pilih kategori untuk melihat katalog lengkap, estimasi harga grosir, serta ketersediaan stok fisik toko.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Category 1: Keramik & Granit -->
                    <Link
                        :href="route('catalog.index', { category: 'keramik' })"
                        class="p-5 rounded-2xl bg-slate-50 hover:bg-white border border-slate-200 hover:border-primary/40 hover:shadow-lg transition group flex flex-col items-center text-center"
                    >
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary flex items-center justify-center mb-3 group-hover:scale-110 transition duration-300">
                            <Layers class="w-7 h-7" />
                        </div>
                        <h3 class="font-headline font-bold text-sm text-secondary-900 group-hover:text-primary transition">
                            Keramik & Granit
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">
                            Lantai 60x60, Dinding, Subway, Plin
                        </p>
                    </Link>

                    <!-- Category 2: Semen & Mortar -->
                    <Link
                        :href="route('catalog.index', { category: 'semen-mortar' })"
                        class="p-5 rounded-2xl bg-slate-50 hover:bg-white border border-slate-200 hover:border-primary/40 hover:shadow-lg transition group flex flex-col items-center text-center"
                    >
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-secondary-700 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-300">
                            <BrickWall class="w-7 h-7" />
                        </div>
                        <h3 class="font-headline font-bold text-sm text-secondary-900 group-hover:text-primary transition">
                            Semen & Mortar
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">
                            PCC 50kg, Mortar Perekat Bata, Acian
                        </p>
                    </Link>

                    <!-- Category 3: Cat & Waterproofing -->
                    <Link
                        :href="route('catalog.index', { category: 'cat-pelapis' })"
                        class="p-5 rounded-2xl bg-slate-50 hover:bg-white border border-slate-200 hover:border-primary/40 hover:shadow-lg transition group flex flex-col items-center text-center"
                    >
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary flex items-center justify-center mb-3 group-hover:scale-110 transition duration-300">
                            <Sparkles class="w-7 h-7" />
                        </div>
                        <h3 class="font-headline font-bold text-sm text-secondary-900 group-hover:text-primary transition">
                            Cat & Waterproofing
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">
                            Interior, Eksterior, Anti Bocor
                        </p>
                    </Link>

                    <!-- Category 4: Baja Ringan & Besi -->
                    <Link
                        :href="route('catalog.index', { category: 'besi-baja' })"
                        class="p-5 rounded-2xl bg-slate-50 hover:bg-white border border-slate-200 hover:border-primary/40 hover:shadow-lg transition group flex flex-col items-center text-center"
                    >
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-secondary-700 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-300">
                            <ShieldCheck class="w-7 h-7" />
                        </div>
                        <h3 class="font-headline font-bold text-sm text-secondary-900 group-hover:text-primary transition">
                            Baja Ringan & Besi
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">
                            Besi Beton SNI, Kanal C, Hollow
                        </p>
                    </Link>

                    <!-- Category 5: Atap & Plafon -->
                    <Link
                        :href="route('catalog.index')"
                        class="p-5 rounded-2xl bg-slate-50 hover:bg-white border border-slate-200 hover:border-primary/40 hover:shadow-lg transition group flex flex-col items-center text-center"
                    >
                        <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary flex items-center justify-center mb-3 group-hover:scale-110 transition duration-300">
                            <Layers class="w-7 h-7" />
                        </div>
                        <h3 class="font-headline font-bold text-sm text-secondary-900 group-hover:text-primary transition">
                            Atap & Plafon
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">
                            Spandek, Genteng Metal, Gypsum
                        </p>
                    </Link>

                    <!-- Category 6: Pintu & Sanitary -->
                    <Link
                        :href="route('catalog.index')"
                        class="p-5 rounded-2xl bg-slate-50 hover:bg-white border border-slate-200 hover:border-primary/40 hover:shadow-lg transition group flex flex-col items-center text-center"
                    >
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-secondary-700 flex items-center justify-center mb-3 group-hover:scale-110 transition duration-300">
                            <DoorClosed class="w-7 h-7" />
                        </div>
                        <h3 class="font-headline font-bold text-sm text-secondary-900 group-hover:text-primary transition">
                            Pintu & Sanitary
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">
                            Pintu PVC, Closet, Pipa & Kran
                        </p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- 3. MATERIAL TERLARIS SIAP KIRIM ARMADA TOKO -->
        <section class="py-16 bg-[#F8FAFC]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header + Filter Tabs -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                    <div>
                        <div class="flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-wider mb-1">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            Stok Fisik Tersedia di Gudang
                        </div>
                        <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900">
                            Material Terlaris Siap Kirim Armada Toko
                        </h2>
                    </div>

                    <!-- Category Filter Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            @click="selectedCategoryFilter = 'all'"
                            :class="selectedCategoryFilter === 'all' ? 'bg-primary text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                            class="px-4 py-1.5 rounded-lg text-xs font-bold transition shadow-xs"
                        >
                            Semua Produk
                        </button>
                        <button
                            @click="selectedCategoryFilter = 'keramik'"
                            :class="selectedCategoryFilter === 'keramik' ? 'bg-primary text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                            class="px-4 py-1.5 rounded-lg text-xs font-bold transition shadow-xs"
                        >
                            Keramik & Granit
                        </button>
                        <button
                            @click="selectedCategoryFilter = 'cat-pelapis'"
                            :class="selectedCategoryFilter === 'cat-pelapis' ? 'bg-primary text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                            class="px-4 py-1.5 rounded-lg text-xs font-bold transition shadow-xs"
                        >
                            Cat & Pelapis
                        </button>
                        <button
                            @click="selectedCategoryFilter = 'semen-mortar'"
                            :class="selectedCategoryFilter === 'semen-mortar' ? 'bg-primary text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100'"
                            class="px-4 py-1.5 rounded-lg text-xs font-bold transition shadow-xs"
                        >
                            Semen & Mortar
                        </button>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="mat in filteredFeaturedMaterials"
                        :key="mat.id"
                        class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-xl transition duration-300 flex flex-col group"
                    >
                        <!-- Card Image / Placeholder Area -->
                        <div class="relative h-48 bg-slate-100 overflow-hidden flex items-center justify-center p-4">
                            <!-- Visual representation / Placeholder image -->
                            <div class="text-slate-300 group-hover:scale-105 transition duration-300">
                                <Layers v-if="mat.calculation_type === 'ceramic'" class="w-20 h-20 text-slate-300" />
                                <Sparkles v-else-if="mat.calculation_type === 'paint'" class="w-20 h-20 text-slate-300" />
                                <Building2 v-else class="w-20 h-20 text-slate-300" />
                            </div>

                            <!-- Top Badges -->
                            <div class="absolute top-3 left-3 right-3 flex items-center justify-between pointer-events-none">
                                <span class="bg-secondary-900/90 backdrop-blur-xs text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    SIAP KIRIM
                                </span>
                                <span
                                    v-if="mat.calculator_enabled"
                                    class="bg-tertiary-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-md flex items-center gap-1 shadow-xs"
                                >
                                    <Calculator class="w-3 h-3" />
                                    <span>Fitur Kalkulator</span>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-primary">
                                    {{ mat.category?.name || 'Material Bangunan' }}
                                </span>
                                <h3 class="font-headline font-bold text-base text-secondary-900 mt-1 line-clamp-2 leading-snug group-hover:text-primary transition">
                                    {{ mat.name }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed">
                                    {{ mat.description }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex flex-col gap-3">
                                <div class="flex items-baseline justify-between">
                                    <div>
                                        <span class="text-xs text-slate-400 block">Harga Resmi:</span>
                                        <span class="font-headline font-extrabold text-xl text-primary">
                                            {{ formatRupiah(mat.price) }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-500"> / {{ mat.sale_unit }}</span>
                                    </div>
                                    <span class="text-[11px] text-tertiary-600 font-semibold bg-tertiary-50 px-2 py-0.5 rounded">
                                        Stok Aman
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="grid grid-cols-2 gap-2 pt-1">
                                    <Link
                                        v-if="mat.calculator_enabled"
                                        :href="route('calculator.index', { product: mat.slug })"
                                        class="flex items-center justify-center gap-1.5 py-2.5 px-3 bg-primary-50 hover:bg-primary-100 text-primary border border-primary/20 rounded-xl font-headline font-bold text-xs transition"
                                    >
                                        <Calculator class="w-3.5 h-3.5" />
                                        <span>Hitung Estimasi</span>
                                    </Link>
                                    <button
                                        v-else
                                        @click="addProductDirect(mat)"
                                        class="flex items-center justify-center gap-1.5 py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-secondary-800 rounded-xl font-headline font-bold text-xs transition"
                                    >
                                        <span>Detail Info</span>
                                    </button>

                                    <button
                                        @click="addProductDirect(mat)"
                                        class="flex items-center justify-center gap-1.5 py-2.5 px-3 bg-secondary-900 hover:bg-secondary-800 text-white rounded-xl font-headline font-bold text-xs transition shadow-xs"
                                    >
                                        <ShoppingCart class="w-3.5 h-3.5" />
                                        <span>+ Keranjang</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom CTA to Full Catalog -->
                <div class="mt-12 text-center">
                    <Link
                        :href="route('catalog.index')"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-white hover:bg-slate-50 text-secondary-900 border-2 border-slate-300 font-headline font-bold text-sm rounded-xl transition shadow-xs hover:border-primary hover:text-primary group"
                    >
                        <span>Buka Katalog Lengkap 100+ Material Bangunan</span>
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- 4. CARA PEMESANAN (3 LANGKAH BELANJA TANPA AKUN) -->
        <section id="cara-pesan" class="py-20 bg-white border-b border-slate-200 scroll-mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-primary font-bold text-xs uppercase tracking-widest font-headline">
                        CARA PEMESANAN MUDAH & CEPAT
                    </span>
                    <h2 class="font-headline font-extrabold text-2xl sm:text-4xl text-secondary-900 mt-1">
                        3 Langkah Belanja Tanpa Perlu Daftar Akun
                    </h2>
                    <p class="text-sm text-slate-500 mt-3 leading-relaxed">
                        Hemat waktu tanpa formulir panjang. Cukup kalkulasi ataupun pilih produk langsung dari katalog kami.
                    </p>
                </div>

                <!-- 3 Steps Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                    <!-- Step 1 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 relative flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-primary text-white font-headline font-extrabold text-base flex items-center justify-center mb-4 shadow-md shadow-primary/20">
                                1
                            </div>
                            <h3 class="font-headline font-bold text-lg text-secondary-900 mb-2">
                                Input Dimensi Lapangan
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Masukkan panjang & lebar pekerjaan di kalkulator. Sistem otomatis mengonversi ke satuan dus atau kaleng plus toleransi cadangan waste (5-15%).
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center gap-2 text-primary font-bold text-xs">
                            <Calculator class="w-4 h-4" />
                            <span>Tersedia untuk Keramik & Cat</span>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 relative flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-secondary-900 text-white font-headline font-extrabold text-base flex items-center justify-center mb-4">
                                2
                            </div>
                            <h3 class="font-headline font-bold text-lg text-secondary-900 mb-2">
                                Review Keranjang & Checkout
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Periksa ringkasan pesanan, isi nama penerima, WhatsApp, serta alamat pengantaran armada. Data pesanan langsung tersimpan ke sistem database.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center gap-2 text-secondary-700 font-bold text-xs">
                            <ShieldCheck class="w-4 h-4 text-tertiary-600" />
                            <span>Tervalidasi aman oleh server</span>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 relative flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-tertiary-600 text-white font-headline font-extrabold text-base flex items-center justify-center mb-4">
                                3
                            </div>
                            <h3 class="font-headline font-bold text-lg text-secondary-900 mb-2">
                                Kirim Chat ke WhatsApp Toko
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Klik tombol WhatsApp untuk mengirim teks format pesanan rapi yang sudah terisi otomatis. Tim toko langsung mengonfirmasi ongkir & pembayaran.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center gap-2 text-tertiary-700 font-bold text-xs">
                            <MessageSquare class="w-4 h-4 text-tertiary-600" />
                            <span>Tanpa ketik ulang manual di chat</span>
                        </div>
                    </div>
                </div>

                <!-- Mockup WhatsApp Preview Box (Sesuai Desain PRD & Wireframe) -->
                <div class="max-w-2xl mx-auto bg-secondary-950 text-slate-200 rounded-2xl p-6 sm:p-8 border border-secondary-800 shadow-2xl relative">
                    <div class="flex items-center justify-between pb-4 border-b border-secondary-800 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-tertiary-500 flex items-center justify-center text-white">
                                <MessageSquare class="w-4 h-4" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-white">Format Pesan WhatsApp Toko</h4>
                                <p class="text-[11px] text-slate-400">Otomatis dihasilkan setelah klik "Lanjutkan ke WhatsApp"</p>
                            </div>
                        </div>
                        <span class="text-[11px] bg-secondary-800 text-slate-300 px-2.5 py-1 rounded-full font-mono">
                            wa.me
                        </span>
                    </div>

                    <!-- Chat Bubble Preview -->
                    <div class="bg-secondary-900/90 rounded-xl p-5 border border-secondary-800 font-mono text-xs text-slate-300 space-y-3 leading-relaxed">
                        <p class="text-white">
                            Halo Admin MATERIALKU, saya ingin mengonfirmasi pesanan berikut.
                        </p>
                        <div class="text-primary-300 font-bold">
                            Kode: MK-20261104-A7K9<br />
                            Nama: Budi Santoso
                        </div>
                        <div>
                            Detail material:<br />
                            1. Keramik Granit 60x60 — 10 dus × Rp85.000 = Rp850.000<br />
                            2. Cat Tembok Interior 5L — 2 kaleng × Rp150.000 = Rp300.000
                        </div>
                        <div class="pt-2 border-t border-secondary-800 text-white font-bold">
                            Estimasi biaya material: Rp1.150.000<br />
                            <span class="text-slate-400 text-[11px] font-normal">Ongkir belum termasuk.</span>
                        </div>
                        <p class="text-slate-400 text-[11px] italic pt-1">
                            Mohon konfirmasi ketersediaan, ongkir armada toko, dan cara pembayaran. Terima kasih.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. INFORMASI PENGIRIMAN & KETENTUAN LOGISTIK -->
        <section id="informasi-pengiriman" class="py-16 bg-[#F8FAFC] border-b border-slate-200 scroll-mt-20">
            <!-- Anchor alias for backward compatibility -->
            <div id="armada-pengiriman" class="-mt-20 pt-20"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                    <div>
                        <span class="text-primary font-bold text-xs uppercase tracking-widest font-headline">
                            LAYANAN LOGISTIK & PENGIRIMAN
                        </span>
                        <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 mt-1">
                            Informasi Pengiriman & Ketentuan Ongkir
                        </h2>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <p class="text-xs text-slate-500 max-w-sm">
                            Informasi lengkap area layanan pengiriman, skema ongkir transparan, dan armada mandiri toko tanpa kurir pihak ketiga untuk menjamin keutuhan semen, cat, dan keramik.
                        </p>
                        <Link
                            :href="route('shipping.info')"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white border border-slate-300 hover:border-primary text-secondary-900 hover:text-primary text-xs font-bold rounded-xl transition shadow-xs shrink-0"
                        >
                            <span>Panduan Lengkap & FAQ</span>
                            <ArrowRight class="w-3.5 h-3.5 text-primary" />
                        </Link>
                    </div>
                </div>

                <!-- 3 Pilar Informasi Pengiriman (Area Layanan, Ketentuan Ongkir, Ketentuan Kirim) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Pilar 1: Area Layanan -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary flex items-center justify-center mb-3">
                                <MapPin class="w-5 h-5" />
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1.5">
                                Area Layanan Toko
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Melayani pengiriman ke seluruh <span class="font-semibold text-secondary-900">Kota Bandar Lampung, Kab. Lampung Selatan, dan Kab. Pesawaran</span> dalam radius s/d 25 km langsung ke lokasi proyek atau rumah.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] text-tertiary-700 font-bold">
                            <CheckCircle2 class="w-4 h-4 shrink-0 text-tertiary-600" />
                            <span>Jangkauan Luas & Tepat Alamat</span>
                        </div>
                    </div>

                    <!-- Pilar 2: Ketentuan Ongkir -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-tertiary-50 text-tertiary-700 flex items-center justify-center mb-3">
                                <Truck class="w-5 h-5" />
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1.5">
                                Biaya & Ketentuan Ongkir
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Ongkir transparan dihitung berdasarkan jarak tempuh armada toko. <span class="font-semibold text-secondary-900">Gratis Ongkir</span> untuk pembelanjaan proyek tertentu dalam radius 5 km dari gudang pusat.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] text-primary font-bold">
                            <CheckCircle2 class="w-4 h-4 shrink-0 text-primary" />
                            <span>Tarif Adil & Dikonfirmasi di Awal</span>
                        </div>
                    </div>

                    <!-- Pilar 3: Ketentuan Kirim -->
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                                <Clock class="w-5 h-5" />
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1.5">
                                Jadwal & Ketentuan Kirim
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Pesanan terkonfirmasi sebelum <span class="font-semibold text-secondary-900">14.00 WIB</span> dikirim di hari yang sama (Sameday). Pengiriman dilengkapi tenaga bongkar muat toko berhati-hati menangani barang pecah belah.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-[11px] text-blue-700 font-bold">
                            <CheckCircle2 class="w-4 h-4 shrink-0 text-blue-600" />
                            <span>Sameday & Bebas Khawatir Rusak</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-header Armada Mandiri -->
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="font-headline font-bold text-base text-secondary-900 flex items-center gap-2">
                        <span>Pilihan Armada Mandiri Toko</span>
                        <span class="text-xs font-normal text-slate-500">(Disesuaikan dengan volume barang)</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Vehicle 1: Truk Engkel -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary flex items-center justify-center">
                                    <Truck class="w-6 h-6" />
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-md bg-slate-100 text-secondary-800">
                                    Kapasitas s/d 4 Ton
                                </span>
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1">
                                Truk Engkel 4 Roda
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Ideal untuk pengiriman semen, bata ringan, hebel, pasir curah, atau keramik volume sedang ke area perumahan.
                            </p>
                        </div>
                        <ul class="text-xs text-slate-600 space-y-1.5 pt-4 border-t border-slate-100">
                            <li class="flex items-center gap-2"><Check class="w-3.5 h-3.5 text-tertiary-600" /> Muat s/d 80 sak semen / 120 dus keramik</li>
                            <li class="flex items-center gap-2"><Check class="w-3.5 h-3.5 text-tertiary-600" /> Bisa masuk jalan perumahan lebar 3-4 meter</li>
                        </ul>
                    </div>

                    <!-- Vehicle 2: Pickup Bak -->
                    <div class="p-6 rounded-2xl bg-white border-2 border-primary/30 shadow-md flex flex-col justify-between relative">
                        <span class="absolute -top-3 right-6 bg-primary text-white text-[10px] font-extrabold px-3 py-0.5 rounded-full uppercase tracking-wider">
                            Paling Cepat
                        </span>
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center">
                                    <Truck class="w-6 h-6" />
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-md bg-primary-50 text-primary-800">
                                    Kapasitas s/d 1.5 Ton
                                </span>
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1">
                                Pickup Bak Terbuka
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Bahan cepat seperti kaleng cat, pipa PVC, fitting sanitari, keramik jumlah sedang, dan perkakas tukang.
                            </p>
                        </div>
                        <ul class="text-xs text-slate-600 space-y-1.5 pt-4 border-t border-slate-100">
                            <li class="flex items-center gap-2"><Check class="w-3.5 h-3.5 text-tertiary-600" /> Kirim cepat sameday (1-3 jam)</li>
                            <li class="flex items-center gap-2"><Check class="w-3.5 h-3.5 text-tertiary-600" /> Fleksibel masuk gang & jalan sempit</li>
                        </ul>
                    </div>

                    <!-- Vehicle 3: Truk Cold Diesel -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-secondary-800 flex items-center justify-center">
                                    <Truck class="w-6 h-6" />
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-md bg-slate-100 text-secondary-800">
                                    Kapasitas s/d 10 Ton
                                </span>
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1">
                                Truk Cold Diesel / Tronton
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Untuk proyek konstruksi skala besar: pengiriman besi beton batangan, baja ringan panjang, semen grosir sak berton-ton.
                            </p>
                        </div>
                        <ul class="text-xs text-slate-600 space-y-1.5 pt-4 border-t border-slate-100">
                            <li class="flex items-center gap-2"><Check class="w-3.5 h-3.5 text-tertiary-600" /> Muat besi panjang 12 meter utuh</li>
                            <li class="flex items-center gap-2"><Check class="w-3.5 h-3.5 text-tertiary-600" /> Penjadwalan langsung dengan mandor proyek</li>
                        </ul>
                    </div>
                </div>

                <!-- Jangkauan Pengiriman Notice & WhatsApp Consultation -->
                <div class="mt-8 p-4 bg-white rounded-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                    <div class="flex items-center gap-3">
                        <MapPin class="w-5 h-5 text-primary shrink-0" />
                        <div>
                            <span class="font-bold text-secondary-900">Jangkauan Wilayah Layanan:</span>
                            <span class="text-slate-600 ml-1">Kota Bandar Lampung, Kab. Lampung Selatan (Natar, Jati Agung, Tj. Bintang), dan Kab. Pesawaran.</span>
                        </div>
                    </div>
                    <a
                        :href="`https://wa.me/${setting?.store_whatsapp || '6281234567890'}?text=${encodeURIComponent('Halo Admin MATERIALKU, saya mau tanya estimasi ongkir dan jangkauan kirim ke lokasi saya...')}`"
                        target="_blank"
                        class="text-primary font-bold hover:underline inline-flex items-center gap-1 shrink-0"
                    >
                        <span>Cek Estimasi Ongkir ke Alamat Anda</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </section>

        <!-- 6. TESTIMONI PELANGGAN -->
        <section class="py-16 bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-primary font-bold text-xs uppercase tracking-widest font-headline">
                        PENGALAMAN NYATA
                    </span>
                    <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 mt-1">
                        Dipercaya Kontraktor & Renovasi Mandiri
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <div class="flex text-amber-400">
                            <Star v-for="i in 5" :key="i" class="w-4 h-4 fill-current" />
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed italic">
                            "Kalkulator waste 10% nya sangat akurat. Pasang keramik ruang tamu 60m2 pas tidak ada sisa berlebih ataupun kurang waktu dipasang tukang di lapangan."
                        </p>
                        <div class="pt-2 border-t border-slate-200/80">
                            <h4 class="font-bold text-xs text-secondary-900">Bambang S.</h4>
                            <p class="text-[11px] text-slate-500">Mandor Renovasi Rumah Tinggal</p>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <div class="flex text-amber-400">
                            <Star v-for="i in 5" :key="i" class="w-4 h-4 fill-current" />
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed italic">
                            "Paling malas belanja di toko material kalau disuruh registrasi akun email macam-macam. Di Materialku tinggal klik keranjang, langsung kirim ke WhatsApp toko, langsung direspon admin!"
                        </p>
                        <div class="pt-2 border-t border-slate-200/80">
                            <h4 class="font-bold text-xs text-secondary-900">Rizki Anugrah</h4>
                            <p class="text-[11px] text-slate-500">Pemilik Rumah (Renovasi Mandiri)</p>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <div class="flex text-amber-400">
                            <Star v-for="i in 5" :key="i" class="w-4 h-4 fill-current" />
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed italic">
                            "Pengiriman semen dan cat kami minta pagi jam 9 sudah sampai ke lokasi proyek. Sopir toko ramah dan bantu turunkan semen sampai ke dalam teras rumah."
                        </p>
                        <div class="pt-2 border-t border-slate-200/80">
                            <h4 class="font-bold text-xs text-secondary-900">Hendra Wijaya</h4>
                            <p class="text-[11px] text-slate-500">Kontraktor Sipil CV. Bangun Jaya</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. TOKO FISIK MITRA & GUDANG MATERIAL (HUBUNGI TOKO) -->
        <section id="kontak-toko" class="py-16 bg-[#F8FAFC] border-b border-slate-200 scroll-mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-sm">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-7 space-y-5">
                            <span class="bg-primary-50 text-primary-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                LOKASI GUDANG & TOKO FISIK
                            </span>
                            <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900">
                                TB. Mandiri Sejahtera Abadi
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                Gedung pusat keagenan semen, keramik grosir, cat dinding, serta besi konstruksi bersertifikasi SNI. Memiliki armada truk mandiri siap kirim ke seluruh wilayah Lampung.
                            </p>

                            <div class="space-y-3 pt-2 text-xs text-slate-600">
                                <div class="flex items-start gap-3">
                                    <MapPin class="w-4 h-4 text-primary shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-bold text-secondary-900">Alamat Toko & Gudang:</p>
                                        <p>{{ setting?.store_address || 'Jl. Soekarno-Hatta No. 88, By Pass, Kota Bandar Lampung' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <Clock class="w-4 h-4 text-tertiary-600 shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-bold text-secondary-900">Jam Operasional Layanan Toko:</p>
                                        <p>Senin - Sabtu: 07.30 - 17.00 WIB • Minggu: 08.00 - 14.00 WIB</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 flex flex-wrap gap-3">
                                <a
                                    :href="`https://wa.me/${setting?.store_whatsapp || '6281234567890'}?text=${encodeURIComponent('Halo Toko Mandiri Sejahtera, saya ingin tanya ketersediaan stok...')}`"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-tertiary-600 hover:bg-tertiary-500 text-white rounded-xl text-xs font-bold transition shadow-sm"
                                >
                                    <MessageSquare class="w-4 h-4" />
                                    <span>Chat Langsung Admin Toko</span>
                                </a>

                                <Link
                                    :href="route('contact.index')"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-secondary-900 border border-slate-300 rounded-xl text-xs font-bold transition shadow-xs group"
                                >
                                    <span>Buka Peta & Info Lengkap</span>
                                    <ArrowRight class="w-4 h-4 text-primary group-hover:translate-x-0.5 transition" />
                                </Link>
                            </div>
                        </div>

                        <!-- Warehouse Photo Card (Sesuai Wireframe Toko & Gudang) -->
                        <div class="lg:col-span-5 relative rounded-2xl overflow-hidden shadow-lg border border-slate-200 group h-full min-h-[280px] bg-slate-900">
                            <img
                                src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=900&auto=format&fit=crop"
                                alt="Gedung Pusat Toko TB. Mandiri Sejahtera Abadi"
                                class="w-full h-full object-cover min-h-[280px] group-hover:scale-105 transition duration-500 opacity-90"
                            />
                            <!-- Elegant Dark Gradient Overlay (Hanya di bawah untuk keterbacaan teks) -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

                            <!-- Badges Overlay -->
                            <div class="absolute top-4 left-4">
                                <span class="bg-primary/95 text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-md">
                                    GUDANG PUSAT 500+ TON
                                </span>
                            </div>

                            <div class="absolute bottom-4 left-4 right-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                                <span class="bg-slate-900/90 backdrop-blur-md px-3 py-1.5 rounded-xl border border-slate-700/80 font-bold text-xs text-white shadow-md">
                                    🏢 Gedung Pusat Toko TB. Mandiri Sejahtera
                                </span>
                                <span class="bg-tertiary-600/95 text-white px-2.5 py-1 rounded-lg font-bold text-[11px] shadow-sm flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    Stok Siap Kirim
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. ORANGE CTA BANNER (#D9531E) -->
        <section class="bg-primary text-white py-14">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                <h2 class="font-headline font-extrabold text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight">
                    Punya Denah atau Sketsa Renovasi Sendiri?
                </h2>
                <p class="text-sm sm:text-base text-primary-100 max-w-2xl mx-auto leading-relaxed">
                    Kirimkan file denah arsitek atau gambar sketsa Anda via WhatsApp. Tim spesialis TB. Mandiri Sejahtera Abadi siap menghitung volume kebutuhan material lengkap dengan estimasi biaya secara gratis!
                </p>
                <div class="pt-2">
                    <a
                        :href="`https://wa.me/${setting?.store_whatsapp || '6281234567890'}?text=${encodeURIComponent('Halo Admin MATERIALKU, saya punya sketsa denah renovasi dan ingin dibantu hitung estimasi material...')}`"
                        target="_blank"
                        class="inline-flex items-center gap-2.5 px-8 py-3.5 bg-white text-primary hover:bg-primary-50 font-headline font-bold text-sm rounded-xl shadow-xl transition transform hover:-translate-y-0.5"
                    >
                        <MessageSquare class="w-4 h-4 text-tertiary-600" />
                        <span>Kirim Gambar Denah via WhatsApp Toko</span>
                    </a>
                </div>
                <p class="text-[11px] text-primary-200">
                    *Respon cepat di jam operasional toko: 07.30 - 17.00 WIB
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
