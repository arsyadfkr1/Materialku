<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCartStore } from '@/Stores/cartStore';
import {
    Search,
    RotateCcw,
    Calculator,
    ShoppingCart,
    Truck,
    Clock,
    Building2,
    CheckCircle2,
    AlertTriangle,
    ChevronLeft,
    ChevronRight,
    Layers,
    Sparkles,
    SlidersHorizontal,
    Filter,
    X,
    Check,
    ChevronDown,
    Tag,
    DollarSign
} from 'lucide-vue-next';

const props = defineProps({
    materials: Object,
    categories: Array,
    totalActive: Number,
    availableUnits: Array,
    filters: Object,
    setting: Object,
});

const cart = useCartStore();

// --- Filter State ---
const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || 'all');
const selectedPriceRange = ref(props.filters?.price_range || 'all');
const minPrice = ref(props.filters?.min_price || '');
const maxPrice = ref(props.filters?.max_price || '');
const selectedUnit = ref(props.filters?.unit || 'all');
const availableOnly = ref(props.filters?.available_only === '1' || props.filters?.available_only === true);
const calculatorOnly = ref(props.filters?.calculator_only === '1' || props.filters?.calculator_only === true);
const sortBy = ref(props.filters?.sort || 'relevance');
const isAdvancedFilterOpen = ref(
    !!(props.filters?.price_range && props.filters.price_range !== 'all') ||
    !!props.filters?.min_price ||
    !!props.filters?.max_price ||
    !!(props.filters?.unit && props.filters.unit !== 'all') ||
    !!props.filters?.calculator_only
);

// Presets rentang harga
const pricePresets = [
    { key: 'all', label: 'Semua Harga' },
    { key: 'under_100k', label: '< Rp 100 rb' },
    { key: '100k_250k', label: 'Rp 100 rb – Rp 250 rb' },
    { key: 'above_250k', label: '> Rp 250 rb' },
];

// Presets satuan material
const unitPresets = [
    { key: 'all', label: 'Semua Satuan' },
    { key: 'dus', label: 'Dus (Keramik)' },
    { key: 'sak', label: 'Sak (Semen/Mortar)' },
    { key: 'kaleng', label: 'Kaleng (Cat 5L)' },
    { key: 'pail', label: 'Pail (Cat 20L)' },
    { key: 'batang', label: 'Batang (Besi/Baja)' },
];

const activeAdvancedFilterCount = computed(() => {
    let count = 0;
    if (selectedPriceRange.value !== 'all' || minPrice.value || maxPrice.value) count++;
    if (selectedUnit.value !== 'all') count++;
    if (calculatorOnly.value) count++;
    return count;
});

const hasActiveFilters = computed(() => {
    return (
        search.value.trim() !== '' ||
        selectedCategory.value !== 'all' ||
        selectedPriceRange.value !== 'all' ||
        minPrice.value !== '' ||
        maxPrice.value !== '' ||
        selectedUnit.value !== 'all' ||
        availableOnly.value ||
        calculatorOnly.value ||
        sortBy.value !== 'relevance'
    );
});

const applyFilters = () => {
    router.get(
        route('catalog.index'),
        {
            search: search.value ? search.value.trim() : undefined,
            category: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
            price_range: selectedPriceRange.value !== 'all' ? selectedPriceRange.value : undefined,
            min_price: minPrice.value ? minPrice.value : undefined,
            max_price: maxPrice.value ? maxPrice.value : undefined,
            unit: selectedUnit.value !== 'all' ? selectedUnit.value : undefined,
            available_only: availableOnly.value ? 1 : undefined,
            calculator_only: calculatorOnly.value ? 1 : undefined,
            sort: sortBy.value !== 'relevance' ? sortBy.value : undefined,
        },
        { preserveState: true, replace: true }
    );
};

const filterByCategory = (slug) => {
    selectedCategory.value = slug;
    applyFilters();
};

const selectPricePreset = (key) => {
    selectedPriceRange.value = key;
    minPrice.value = '';
    maxPrice.value = '';
    applyFilters();
};

const applyCustomPrice = () => {
    selectedPriceRange.value = 'custom';
    applyFilters();
};

const selectUnit = (unit) => {
    selectedUnit.value = unit;
    applyFilters();
};

const toggleAvailableOnly = () => {
    availableOnly.value = !availableOnly.value;
    applyFilters();
};

const toggleCalculatorOnly = () => {
    calculatorOnly.value = !calculatorOnly.value;
    applyFilters();
};

const resetFilters = () => {
    search.value = '';
    selectedCategory.value = 'all';
    selectedPriceRange.value = 'all';
    minPrice.value = '';
    maxPrice.value = '';
    selectedUnit.value = 'all';
    availableOnly.value = false;
    calculatorOnly.value = false;
    sortBy.value = 'relevance';
    router.get(route('catalog.index'));
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const addProductDirect = (product) => {
    cart.addItem(product, 1);
    if (window.materialkuToast) {
        window.materialkuToast(`${product.name} telah ditambahkan ke keranjang.`);
    }
};
</script>

<template>
    <PublicLayout :setting="setting">
        <Head title="Katalog Lengkap Material Bangunan Berkualitas - MATERIALKU" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            
            <!-- 1. TOP HEADER BANNER CARD -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <!-- Left: Title & Subtitle -->
                    <div class="space-y-2 max-w-3xl">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-[11px] font-bold tracking-wide uppercase">
                            <Building2 class="w-3.5 h-3.5 text-primary" />
                            <span>TB. MANDIRI SEJAHTERA ABADI • PUSAT MATERIAL LAMPUNG</span>
                        </div>
                        <h1 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 tracking-tight">
                            Katalog Lengkap Material Bangunan Berkualitas
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                            Belanja langsung tanpa login akun, harga transparan, dan armada toko siap kirim ke seluruh wilayah Bandar Lampung & sekitarnya. Cek ketersediaan aktual spesifikasi material proyek renovasi Anda.
                        </p>
                    </div>

                    <!-- Right: Info Delivery & Hours -->
                    <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6 text-xs text-slate-600">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary-50 text-primary flex items-center justify-center shrink-0">
                                <Truck class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-secondary-900">Armada Mandiri Lampung</h4>
                                <p class="text-[11px] text-slate-400">Pickup Bak, Engkel & Dump Truk</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-tertiary-600 flex items-center justify-center shrink-0">
                                <Clock class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-secondary-900">Buka Hari Ini: 07.30 - 17.00</h4>
                                <p class="text-[11px] text-slate-400">Pengiriman Sameday Radius 25km</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SEARCH & FILTER BAR CARD -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs space-y-4">
                <!-- Top Row: Search Input, Advanced Filter Toggle, Sort, and Quick Toggles -->
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                    <!-- Search Input with Clear Button -->
                    <div class="relative flex-1">
                        <input
                            type="text"
                            v-model="search"
                            @keyup.enter="applyFilters"
                            placeholder="Cari semen, keramik 60x60, cat tembok, hebel..."
                            class="w-full pl-10 pr-10 py-2.5 text-xs font-medium bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                        />
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        <button
                            v-if="search"
                            type="button"
                            @click="search = ''; applyFilters();"
                            class="p-1 text-slate-400 hover:text-slate-600 absolute right-3 top-1/2 -translate-y-1/2"
                        >
                            <X class="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- Actions: Filter Lanjutan, Sort, Toggles, Reset -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Toggle Button: Filter Lanjutan -->
                        <button
                            type="button"
                            @click="isAdvancedFilterOpen = !isAdvancedFilterOpen"
                            :class="[
                                isAdvancedFilterOpen || activeAdvancedFilterCount > 0
                                    ? 'bg-primary-50 text-primary border-primary/30'
                                    : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'
                            ]"
                            class="inline-flex items-center gap-2 px-3 py-2 text-xs font-bold rounded-xl border transition shadow-2xs"
                        >
                            <SlidersHorizontal class="w-3.5 h-3.5" />
                            <span>Filter Lanjutan</span>
                            <span
                                v-if="activeAdvancedFilterCount > 0"
                                class="px-1.5 py-0.2 rounded-full bg-primary text-white text-[10px] font-extrabold"
                            >
                                {{ activeAdvancedFilterCount }}
                            </span>
                            <ChevronDown
                                :class="{ 'rotate-180': isAdvancedFilterOpen }"
                                class="w-3 h-3 transition-transform duration-200"
                            />
                        </button>

                        <!-- Sort Dropdown -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <select
                                v-model="sortBy"
                                @change="applyFilters"
                                class="text-xs font-bold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20 py-2 pl-3 pr-8 bg-slate-50 cursor-pointer"
                            >
                                <option value="relevance">Urutan: Paling Relevan</option>
                                <option value="price_asc">Harga: Terendah</option>
                                <option value="price_desc">Harga: Tertinggi</option>
                                <option value="name_asc">Nama: A - Z</option>
                                <option value="name_desc">Nama: Z - A</option>
                            </select>
                        </div>

                        <!-- Tersedia Saja Quick Toggle Button -->
                        <button
                            @click="toggleAvailableOnly"
                            type="button"
                            :class="availableOnly ? 'bg-primary text-white border-primary shadow-xs' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-bold transition"
                            title="Tampilkan hanya material dengan stok tersedia"
                        >
                            <span
                                :class="availableOnly ? 'bg-white' : 'bg-slate-400'"
                                class="w-2 h-2 rounded-full transition"
                            ></span>
                            <span class="hidden sm:inline">Tersedia Saja</span>
                            <span class="sm:hidden">Ready</span>
                        </button>

                        <!-- Bisa Kalkulator Quick Toggle -->
                        <button
                            @click="toggleCalculatorOnly"
                            type="button"
                            :class="calculatorOnly ? 'bg-secondary-900 text-white border-secondary-900 shadow-xs' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border text-xs font-bold transition"
                            title="Material yang bisa dihitung dengan kalkulator ruangan"
                        >
                            <Calculator class="w-3.5 h-3.5" :class="calculatorOnly ? 'text-primary' : 'text-slate-500'" />
                            <span class="hidden md:inline">Bisa Kalkulator</span>
                        </button>

                        <!-- Reset Button -->
                        <button
                            v-if="hasActiveFilters"
                            @click="resetFilters"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition"
                            title="Bersihkan semua filter"
                        >
                            <RotateCcw class="w-3.5 h-3.5" />
                            <span>Reset</span>
                        </button>
                    </div>
                </div>

                <!-- Category Pills (Row 2) -->
                <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-400 flex items-center gap-1 mr-1">
                        Kategori:
                    </span>

                    <!-- Semua -->
                    <button
                        @click="filterByCategory('all')"
                        :class="selectedCategory === 'all' ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition"
                    >
                        Semua ({{ totalActive || 12 }})
                    </button>

                    <!-- Categories from DB -->
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        @click="filterByCategory(cat.slug)"
                        :class="selectedCategory === cat.slug ? 'bg-primary text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition"
                    >
                        {{ cat.name }} ({{ cat.materials_count }})
                    </button>
                </div>

                <!-- EXPANDABLE ADVANCED FILTER PANEL -->
                <transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="transform -translate-y-2 opacity-0"
                    enter-to-class="transform translate-y-0 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="transform translate-y-0 opacity-100"
                    leave-to-class="transform -translate-y-2 opacity-0"
                >
                    <div
                        v-if="isAdvancedFilterOpen"
                        class="pt-4 border-t border-slate-200/80 bg-slate-50/60 p-4 sm:p-5 rounded-xl border border-slate-200 space-y-4"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-bold text-secondary-900">
                                <Filter class="w-4 h-4 text-primary" />
                                <span>Pilihan Filter Spesifik Material</span>
                            </div>
                            <span class="text-[11px] text-slate-400">Saring berdasarkan budget & jenis kemasan</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
                            <!-- Filter Section 1: Rentang Harga (Presets + Custom Range) -->
                            <div class="md:col-span-7 space-y-2.5">
                                <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <DollarSign class="w-3.5 h-3.5 text-primary" />
                                    <span>Rentang Harga Material:</span>
                                </label>
                                
                                <!-- Presets Pills -->
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <button
                                        v-for="p in pricePresets"
                                        :key="p.key"
                                        type="button"
                                        @click="selectPricePreset(p.key)"
                                        :class="selectedPriceRange === p.key && !minPrice && !maxPrice ? 'bg-primary text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                        class="px-2.5 py-1 text-xs rounded-lg transition"
                                    >
                                        {{ p.label }}
                                    </button>
                                </div>

                                <!-- Custom Min - Max Input -->
                                <div class="flex items-center gap-2 pt-1 text-xs">
                                    <div class="relative flex-1">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[11px] text-slate-400 font-bold">Rp</span>
                                        <input
                                            type="number"
                                            v-model="minPrice"
                                            placeholder="Harga Min"
                                            class="w-full pl-8 pr-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20 outline-none"
                                        />
                                    </div>
                                    <span class="text-slate-400 font-bold">-</span>
                                    <div class="relative flex-1">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[11px] text-slate-400 font-bold">Rp</span>
                                        <input
                                            type="number"
                                            v-model="maxPrice"
                                            placeholder="Harga Max"
                                            class="w-full pl-8 pr-2.5 py-1.5 text-xs bg-white border border-slate-300 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary/20 outline-none"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        @click="applyCustomPrice"
                                        class="px-3 py-1.5 bg-secondary-900 hover:bg-secondary-800 text-white rounded-lg text-xs font-bold transition shrink-0"
                                    >
                                        Terapkan
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Section 2: Jenis Satuan / Kemasan -->
                            <div class="md:col-span-5 space-y-2.5">
                                <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <Tag class="w-3.5 h-3.5 text-primary" />
                                    <span>Satuan Kemasan Jual:</span>
                                </label>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <button
                                        v-for="u in unitPresets"
                                        :key="u.key"
                                        type="button"
                                        @click="selectUnit(u.key)"
                                        :class="selectedUnit === u.key ? 'bg-secondary-900 text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                        class="px-2.5 py-1 text-xs rounded-lg transition"
                                    >
                                        {{ u.label }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- Active Filter Tags (Chips) Bar -->
                <div v-if="hasActiveFilters" class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs">
                    <span class="font-bold text-slate-400 mr-1 text-[11px]">Filter Aktif:</span>

                    <!-- Search Chip -->
                    <span v-if="search" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 text-secondary-800 text-xs font-medium">
                        <span>Pencarian: "<strong>{{ search }}</strong>"</span>
                        <button @click="search = ''; applyFilters();" class="text-slate-400 hover:text-slate-700">
                            <X class="w-3 h-3" />
                        </button>
                    </span>

                    <!-- Category Chip -->
                    <span v-if="selectedCategory !== 'all'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-primary-50 text-primary-800 text-xs font-medium">
                        <span>Kategori: <strong>{{ categories.find(c => c.slug === selectedCategory)?.name || selectedCategory }}</strong></span>
                        <button @click="filterByCategory('all')" class="text-primary hover:text-primary-800">
                            <X class="w-3 h-3" />
                        </button>
                    </span>

                    <!-- Price Preset Chip -->
                    <span v-if="selectedPriceRange !== 'all' && selectedPriceRange !== 'custom'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 text-xs font-medium">
                        <span>Harga: <strong>{{ pricePresets.find(p => p.key === selectedPriceRange)?.label }}</strong></span>
                        <button @click="selectPricePreset('all')" class="text-emerald-700 hover:text-emerald-900">
                            <X class="w-3 h-3" />
                        </button>
                    </span>

                    <!-- Custom Price Chip -->
                    <span v-if="minPrice || maxPrice" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 text-xs font-medium">
                        <span>Harga: <strong>{{ minPrice ? formatRupiah(minPrice) : '0' }} - {{ maxPrice ? formatRupiah(maxPrice) : 'Max' }}</strong></span>
                        <button @click="minPrice = ''; maxPrice = ''; selectedPriceRange = 'all'; applyFilters();" class="text-emerald-700 hover:text-emerald-900">
                            <X class="w-3 h-3" />
                        </button>
                    </span>

                    <!-- Unit Chip -->
                    <span v-if="selectedUnit !== 'all'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 text-secondary-800 text-xs font-medium">
                        <span>Kemasan: <strong>{{ selectedUnit }}</strong></span>
                        <button @click="selectUnit('all')" class="text-slate-400 hover:text-slate-700">
                            <X class="w-3 h-3" />
                        </button>
                    </span>

                    <!-- Available Only Chip -->
                    <span v-if="availableOnly" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 text-xs font-medium">
                        <span>Stok Tersedia</span>
                        <button @click="toggleAvailableOnly" class="text-emerald-700 hover:text-emerald-900">
                            <X class="w-3 h-3" />
                        </button>
                    </span>

                    <!-- Calculator Only Chip -->
                    <span v-if="calculatorOnly" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-50 text-blue-800 text-xs font-medium">
                        <span>Bisa Kalkulator</span>
                        <button @click="toggleCalculatorOnly" class="text-blue-700 hover:text-blue-900">
                            <X class="w-3 h-3" />
                        </button>
                    </span>

                    <!-- Clear All Button -->
                    <button
                        @click="resetFilters"
                        class="text-xs font-bold text-rose-600 hover:underline ml-auto"
                    >
                        Hapus Semua Filter
                    </button>
                </div>
            </div>

            <!-- 3. PRODUCTS GRID (4 COLUMNS) -->
            <div v-if="materials.data.length === 0" class="bg-white rounded-2xl border border-slate-200 p-16 text-center space-y-3">
                <Building2 class="w-12 h-12 text-slate-300 mx-auto" />
                <h3 class="font-headline font-bold text-base text-secondary-900">
                    Tidak ada material yang sesuai dengan filter Anda
                </h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    Coba sesuaikan kata kunci pencarian, rentang harga, atau bersihkan filter spesifik untuk menampilkan kembali seluruh material.
                </p>
                <button
                    @click="resetFilters"
                    class="px-5 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-600 transition shadow-sm"
                >
                    Reset Semua Filter
                </button>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div
                    v-for="mat in materials.data"
                    :key="mat.id"
                    class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-xl transition duration-300 flex flex-col justify-between overflow-hidden group"
                >
                    <!-- Card Image with Badges -->
                    <div class="relative h-48 bg-slate-100 overflow-hidden flex items-center justify-center">
                        <img
                            v-if="mat.image_path"
                            :src="mat.image_path"
                            :alt="mat.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                        <div v-else class="text-slate-300">
                            <Layers v-if="mat.calculation_type === 'ceramic'" class="w-16 h-16 text-slate-300" />
                            <Sparkles v-else-if="mat.calculation_type === 'paint'" class="w-16 h-16 text-slate-300" />
                            <Building2 v-else class="w-16 h-16 text-slate-300" />
                        </div>

                        <!-- Top Floating Badges -->
                        <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between pointer-events-none gap-1">
                            <!-- Stock Status Badge -->
                            <span
                                v-if="mat.stock_badge === 'Stok Menipis'"
                                class="bg-amber-500/95 text-white text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1 shadow-xs"
                            >
                                <AlertTriangle class="w-3 h-3" />
                                <span>Stok Menipis</span>
                            </span>
                            <span
                                v-else
                                class="bg-emerald-600/95 text-white text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1 shadow-xs"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                <span>Tersedia</span>
                            </span>

                            <!-- Packaging / Dimension Spec Badge -->
                            <span
                                v-if="mat.spec_badge"
                                class="bg-slate-900/90 text-white text-[10px] font-semibold px-2 py-0.5 rounded-md shadow-xs backdrop-blur-xs"
                            >
                                {{ mat.spec_badge }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase block">
                                {{ mat.category?.name }}
                            </span>
                            <h3 class="font-headline font-bold text-sm text-secondary-900 line-clamp-2 leading-snug group-hover:text-primary transition min-h-[40px]">
                                {{ mat.name }}
                            </h3>
                            <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed min-h-[32px]">
                                {{ mat.description }}
                            </p>
                        </div>

                        <!-- Price Section -->
                        <div class="pt-2 border-t border-slate-100 space-y-3">
                            <div class="flex items-center justify-between gap-1">
                                <div>
                                    <span class="font-headline font-extrabold text-base text-secondary-900 block leading-tight">
                                        {{ formatRupiah(mat.price) }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 block font-medium">
                                        {{ mat.unit_detail || `per ${mat.sale_unit}` }}
                                    </span>
                                </div>

                                <!-- Sub Price / Unit Info Badge -->
                                <span
                                    v-if="mat.sub_price_badge"
                                    class="bg-blue-50 text-blue-700 text-[10px] font-semibold px-2 py-0.5 rounded-md shrink-0 border border-blue-100"
                                >
                                    {{ mat.sub_price_badge }}
                                </span>
                            </div>

                            <!-- 2 Stacked Action Buttons -->
                            <div class="space-y-1.5 pt-1">
                                <!-- Top Button: Hitung di Kalkulator -->
                                <Link
                                    v-if="mat.calculator_enabled"
                                    :href="route('calculator.index', { product: mat.slug })"
                                    class="w-full flex items-center justify-center gap-1.5 py-2 px-3 bg-white hover:bg-slate-50 text-secondary-800 border border-slate-300 hover:border-slate-400 rounded-xl font-headline font-bold text-xs transition"
                                >
                                    <Calculator class="w-3.5 h-3.5 text-primary" />
                                    <span>Hitung di Kalkulator</span>
                                </Link>
                                <Link
                                    v-else
                                    :href="route('catalog.show', mat.slug)"
                                    class="w-full flex items-center justify-center gap-1.5 py-2 px-3 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-xl font-headline font-semibold text-xs transition"
                                >
                                    <span>Lihat Spesifikasi</span>
                                </Link>

                                <!-- Bottom Button: Tambah ke Keranjang (Solid Orange) -->
                                <button
                                    @click="addProductDirect(mat)"
                                    type="button"
                                    class="w-full flex items-center justify-center gap-1.5 py-2 px-3 bg-primary hover:bg-primary-600 text-white rounded-xl font-headline font-bold text-xs shadow-sm transition active:scale-98"
                                >
                                    <ShoppingCart class="w-3.5 h-3.5" />
                                    <span>Tambah ke Keranjang</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. BOTTOM PAGINATION BAR -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 shadow-xs">
                <div>
                    Menampilkan <strong>{{ materials.from || 1 }} - {{ materials.to || materials.total }}</strong> dari <strong>{{ materials.total }}</strong> material terdaftar
                </div>

                <!-- Numbered Pagination -->
                <div v-if="materials.links && materials.links.length > 3" class="flex items-center gap-1.5">
                    <template v-for="(link, i) in materials.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                'min-w-[34px] h-[34px] flex items-center justify-center rounded-lg text-xs font-bold transition border',
                                link.active
                                    ? 'bg-primary text-white border-primary shadow-xs'
                                    : 'bg-white text-secondary-800 border-slate-200 hover:bg-slate-50'
                            ]"
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="min-w-[34px] h-[34px] flex items-center justify-center rounded-lg text-xs font-medium text-slate-300 border border-slate-100 bg-slate-50 cursor-not-allowed"
                        />
                    </template>
                </div>
            </div>

        </div>
    </PublicLayout>
</template>
