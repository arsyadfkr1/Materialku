<script setup>
import { ref } from 'vue';
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
    Sparkles
} from 'lucide-vue-next';

const props = defineProps({
    materials: Object,
    categories: Array,
    totalActive: Number,
    filters: Object,
    setting: Object,
});

const cart = useCartStore();

const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || 'all');
const availableOnly = ref(props.filters?.available_only === '1' || props.filters?.available_only === true);
const sortBy = ref(props.filters?.sort || 'relevance');

const applyFilters = () => {
    router.get(
        route('catalog.index'),
        {
            search: search.value || undefined,
            category: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
            available_only: availableOnly.value ? 1 : undefined,
            sort: sortBy.value,
        },
        { preserveState: true, replace: true }
    );
};

const filterByCategory = (slug) => {
    selectedCategory.value = slug;
    applyFilters();
};

const toggleAvailableOnly = () => {
    availableOnly.value = !availableOnly.value;
    applyFilters();
};

const resetFilters = () => {
    search.value = '';
    selectedCategory.value = 'all';
    availableOnly.value = false;
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
                            <span>TB. MANDIRI SEJAHTERA ABADI • PENGADAAN CEPAT</span>
                        </div>
                        <h1 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 tracking-tight">
                            Katalog Lengkap Material Bangunan Berkualitas
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                            Belanja langsung tanpa login akun, harga transparan, dan armada toko siap kirim. Cek ketersediaan aktual spesifikasi material proyek renovasi rumah dan kontraktor mandiri.
                        </p>
                    </div>

                    <!-- Right: Info Delivery & Hours -->
                    <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6 text-xs text-slate-600">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-primary-50 text-primary flex items-center justify-center shrink-0">
                                <Truck class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-secondary-900">Armada Toko Lengkap</h4>
                                <p class="text-[11px] text-slate-400">Pickup Bak, Engkel & Dump Truk</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-tertiary-600 flex items-center justify-center shrink-0">
                                <Clock class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-secondary-900">Buka Hari Ini: 07.30 - 17.00</h4>
                                <p class="text-[11px] text-slate-400">Pengiriman Sameday Radius 15km</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SEARCH & FILTER BAR CARD -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs space-y-4">
                <!-- Top Row: Search Input, Reset, Sort, and Available Toggle -->
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input
                            type="text"
                            v-model="search"
                            @keyup.enter="applyFilters"
                            placeholder="Granit & Semen..."
                            class="w-full pl-10 pr-4 py-2.5 text-xs font-medium bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition"
                        />
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    </div>

                    <!-- Actions: Reset, Sort, Toggle -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Reset Button -->
                        <button
                            @click="resetFilters"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl border border-slate-200 transition"
                        >
                            <RotateCcw class="w-3.5 h-3.5" />
                            <span>Reset</span>
                        </button>

                        <!-- Sort Dropdown -->
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-slate-500 font-medium hidden sm:inline">Urutan:</span>
                            <select
                                v-model="sortBy"
                                @change="applyFilters"
                                class="text-xs font-bold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20 py-2 pl-3 pr-8 bg-slate-50"
                            >
                                <option value="relevance">Paling Relevan</option>
                                <option value="price_asc">Harga Terendah</option>
                                <option value="price_desc">Harga Tertinggi</option>
                                <option value="name_asc">Nama A-Z</option>
                            </select>
                        </div>

                        <!-- Tersedia Saja Toggle Button -->
                        <button
                            @click="toggleAvailableOnly"
                            type="button"
                            :class="availableOnly ? 'bg-primary text-white border-primary shadow-xs' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border text-xs font-bold transition"
                        >
                            <span
                                :class="availableOnly ? 'bg-white' : 'bg-slate-400'"
                                class="w-2 h-2 rounded-full transition"
                            ></span>
                            <span>Tersedia Saja</span>
                        </button>
                    </div>
                </div>

                <!-- Bottom Row: Category Pills -->
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
                        Semua ({{ totalActive || 24 }})
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
            </div>

            <!-- 3. PRODUCTS GRID (4 COLUMNS Sesuai Mockup) -->
            <div v-if="materials.data.length === 0" class="bg-white rounded-2xl border border-slate-200 p-16 text-center space-y-3">
                <Building2 class="w-12 h-12 text-slate-300 mx-auto" />
                <h3 class="font-headline font-bold text-base text-secondary-900">
                    Tidak ada material yang sesuai
                </h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    Coba ubah kata kunci pencarian atau bersihkan filter kategori Anda.
                </p>
                <button
                    @click="resetFilters"
                    class="px-5 py-2.5 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-600 transition"
                >
                    Reset Filter
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

            <!-- 4. BOTTOM PAGINATION BAR (Sesuai Mockup) -->
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
