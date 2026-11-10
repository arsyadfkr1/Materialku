<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCartStore } from '@/Stores/cartStore';
import {
    Search,
    Filter,
    Calculator,
    ShoppingCart,
    ArrowRight,
    Building2,
    Layers,
    Sparkles,
    Check,
    RotateCcw
} from 'lucide-vue-next';

const props = defineProps({
    materials: Object,
    categories: Array,
    filters: Object,
    setting: Object,
});

const cart = useCartStore();

const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');
const calculatorOnly = ref(props.filters?.calculator_only === '1' || props.filters?.calculator_only === true);
const sortBy = ref(props.filters?.sort || 'newest');

const applyFilters = () => {
    router.get(
        route('catalog.index'),
        {
            search: search.value || undefined,
            category: selectedCategory.value || undefined,
            calculator_only: calculatorOnly.value ? 1 : undefined,
            sort: sortBy.value,
        },
        { preserveState: true, replace: true }
    );
};

const resetFilters = () => {
    search.value = '';
    selectedCategory.value = '';
    calculatorOnly.value = false;
    sortBy.value = 'newest';
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
        <Head title="Katalog Material Bangunan - MATERIALKU" />

        <div class="bg-white border-b border-slate-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-bold text-primary uppercase tracking-wider mb-1">
                            <Link :href="route('home')" class="hover:underline">Beranda</Link>
                            <span>/</span>
                            <span>Katalog</span>
                        </div>
                        <h1 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900">
                            Katalog Material Bangunan
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Semua harga resmi dari gudang toko dan siap dikirim via armada mandiri.
                        </p>
                    </div>

                    <!-- Search Box -->
                    <div class="w-full md:w-80">
                        <form @submit.prevent="applyFilters" class="relative">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Cari nama material..."
                                class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none"
                            />
                            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar Filter -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-5">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2 font-headline font-bold text-sm text-secondary-900">
                                <Filter class="w-4 h-4 text-primary" />
                                <span>Filter Produk</span>
                            </div>
                            <button
                                @click="resetFilters"
                                class="text-[11px] text-slate-400 hover:text-primary flex items-center gap-1 transition"
                            >
                                <RotateCcw class="w-3 h-3" />
                                <span>Reset</span>
                            </button>
                        </div>

                        <!-- Category Radio / List -->
                        <div class="space-y-2">
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-2">
                                Kategori
                            </label>
                            <div class="space-y-1">
                                <button
                                    @click="selectedCategory = ''; applyFilters()"
                                    :class="selectedCategory === '' ? 'bg-primary-50 text-primary font-bold border-primary/20' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
                                    class="w-full text-left px-3 py-2 text-xs rounded-lg border transition flex items-center justify-between"
                                >
                                    <span>Semua Kategori</span>
                                    <span class="text-[11px] text-slate-400">{{ materials.total }}</span>
                                </button>
                                <button
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    @click="selectedCategory = cat.slug; applyFilters()"
                                    :class="selectedCategory === cat.slug ? 'bg-primary-50 text-primary font-bold border-primary/20' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
                                    class="w-full text-left px-3 py-2 text-xs rounded-lg border transition flex items-center justify-between"
                                >
                                    <span>{{ cat.name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ cat.materials_count }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Calculator Only Checkbox -->
                        <div class="pt-4 border-t border-slate-100">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="calculatorOnly"
                                    @change="applyFilters"
                                    class="rounded border-slate-300 text-primary focus:ring-primary/20 w-4 h-4"
                                />
                                <span class="text-xs font-semibold text-secondary-800">
                                    Hanya yang ada fitur kalkulator
                                </span>
                            </label>
                        </div>

                        <!-- Sorting -->
                        <div class="pt-4 border-t border-slate-100 space-y-2">
                            <label class="block font-headline font-bold text-xs text-secondary-900">
                                Urutkan Harga / Nama
                            </label>
                            <select
                                v-model="sortBy"
                                @change="applyFilters"
                                class="w-full text-xs rounded-lg border-slate-300 focus:border-primary focus:ring-primary/20 py-2"
                            >
                                <option value="newest">Terbaru</option>
                                <option value="price_asc">Harga Terendah</option>
                                <option value="price_desc">Harga Tertinggi</option>
                                <option value="name_asc">Nama A-Z</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Result Count -->
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Menampilkan <strong>{{ materials.data.length }}</strong> dari <strong>{{ materials.total }}</strong> material</span>
                    </div>

                    <!-- Empty state -->
                    <div
                        v-if="materials.data.length === 0"
                        class="bg-white rounded-2xl border border-slate-200 p-12 text-center space-y-3"
                    >
                        <Building2 class="w-12 h-12 text-slate-300 mx-auto" />
                        <h3 class="font-headline font-bold text-base text-secondary-900">
                            Tidak Ada Material yang Cocok
                        </h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Coba ubah kata kunci pencarian atau reset filter untuk melihat katalog lengkap kembali.
                        </p>
                        <button
                            @click="resetFilters"
                            class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl hover:bg-primary-600 transition"
                        >
                            Reset Filter
                        </button>
                    </div>

                    <!-- Grid -->
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="mat in materials.data"
                            :key="mat.id"
                            class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-xl transition duration-300 flex flex-col justify-between group"
                        >
                            <div class="relative h-44 bg-slate-100 flex items-center justify-center p-4">
                                <div class="text-slate-300 group-hover:scale-105 transition duration-300">
                                    <Layers v-if="mat.calculation_type === 'ceramic'" class="w-16 h-16 text-slate-300" />
                                    <Sparkles v-else-if="mat.calculation_type === 'paint'" class="w-16 h-16 text-slate-300" />
                                    <Building2 v-else class="w-16 h-16 text-slate-300" />
                                </div>

                                <div class="absolute top-2.5 left-2.5 right-2.5 flex justify-between pointer-events-none">
                                    <span class="bg-secondary-900 text-white text-[10px] font-bold px-2 py-0.5 rounded">
                                        SIAP KIRIM
                                    </span>
                                    <span
                                        v-if="mat.calculator_enabled"
                                        class="bg-tertiary-600 text-white text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1"
                                    >
                                        <Calculator class="w-3 h-3" />
                                        <span>Kalkulator</span>
                                    </span>
                                </div>
                            </div>

                            <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-primary">
                                        {{ mat.category?.name }}
                                    </span>
                                    <h3 class="font-headline font-bold text-sm text-secondary-900 mt-1 line-clamp-2 leading-snug group-hover:text-primary transition">
                                        {{ mat.name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                        {{ mat.description }}
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5">
                                    <div class="flex items-baseline justify-between">
                                        <div>
                                            <span class="font-headline font-extrabold text-lg text-primary">
                                                {{ formatRupiah(mat.price) }}
                                            </span>
                                            <span class="text-xs font-semibold text-slate-500"> / {{ mat.sale_unit }}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 pt-1">
                                        <Link
                                            v-if="mat.calculator_enabled"
                                            :href="route('calculator.index', { product: mat.slug })"
                                            class="flex items-center justify-center gap-1 py-2 px-2.5 bg-primary-50 hover:bg-primary-100 text-primary border border-primary/20 rounded-xl font-headline font-bold text-xs transition"
                                        >
                                            <Calculator class="w-3.5 h-3.5" />
                                            <span>Hitung</span>
                                        </Link>
                                        <div v-else></div>

                                        <button
                                            @click="addProductDirect(mat)"
                                            class="flex items-center justify-center gap-1.5 py-2 px-3 bg-secondary-900 hover:bg-secondary-800 text-white rounded-xl font-headline font-bold text-xs transition"
                                        >
                                            <ShoppingCart class="w-3.5 h-3.5" />
                                            <span>+ Keranjang</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Links -->
                    <div v-if="materials.links && materials.links.length > 3" class="flex justify-center gap-1 pt-6">
                        <Link
                            v-for="(link, i) in materials.links"
                            :key="i"
                            :href="link.url || '#'"
                            v-html="link.label"
                            :class="[
                                'px-3.5 py-2 text-xs font-bold rounded-lg transition border',
                                link.active
                                    ? 'bg-primary text-white border-primary'
                                    : link.url
                                    ? 'bg-white text-secondary-800 border-slate-200 hover:bg-slate-50'
                                    : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed'
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
