<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCartStore } from '@/Stores/cartStore';
import {
    ShoppingCart,
    Calculator,
    ArrowLeft,
    CheckCircle2,
    Truck,
    ShieldCheck,
    Layers,
    Sparkles,
    Building2,
    Plus,
    Minus
} from 'lucide-vue-next';

const props = defineProps({
    material: Object,
    relatedMaterials: Array,
    setting: Object,
});

const cart = useCartStore();
const quantity = ref(1);

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const addToCart = () => {
    cart.addItem(props.material, quantity.value);
    if (window.materialkuToast) {
        window.materialkuToast(`${quantity.value} ${props.material.sale_unit} ${props.material.name} berhasil ditambahkan ke keranjang.`);
    }
};
</script>

<template>
    <PublicLayout :setting="setting">
        <Head :title="`${material.name} - MATERIALKU`" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <Link :href="route('home')" class="hover:text-primary">Beranda</Link>
                <span>/</span>
                <Link :href="route('catalog.index')" class="hover:text-primary">Katalog</Link>
                <span>/</span>
                <span class="text-secondary-900 font-bold truncate max-w-xs">{{ material.name }}</span>
            </div>

            <!-- Main Product Detail Card -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    <!-- Left: Product Photo Area -->
                    <div class="lg:col-span-6 relative rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 aspect-4/3 flex items-center justify-center">
                        <img
                            v-if="material.image_path"
                            :src="material.image_path"
                            :alt="material.name"
                            class="w-full h-full object-cover"
                        />
                        <div v-else class="text-slate-300">
                            <Layers v-if="material.calculation_type === 'ceramic'" class="w-28 h-28" />
                            <Sparkles v-else-if="material.calculation_type === 'paint'" class="w-28 h-28" />
                            <Building2 v-else class="w-28 h-28" />
                        </div>

                        <!-- Top Badges -->
                        <div class="absolute top-4 left-4 right-4 flex justify-between items-center pointer-events-none">
                            <span class="bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-lg shadow-sm">
                                {{ material.stock_badge || 'Tersedia' }}
                            </span>
                            <span v-if="material.spec_badge" class="bg-slate-900/90 text-white text-xs font-bold px-3 py-1 rounded-lg backdrop-blur-xs">
                                {{ material.spec_badge }}
                            </span>
                        </div>
                    </div>

                    <!-- Right: Product Information & Purchase -->
                    <div class="lg:col-span-6 space-y-6">
                        <div>
                            <span class="text-xs font-bold text-primary uppercase tracking-wider block mb-1">
                                {{ material.category?.name }}
                            </span>
                            <h1 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 leading-tight">
                                {{ material.name }}
                            </h1>
                            <div class="flex items-center gap-3 mt-3">
                                <span class="font-headline font-extrabold text-3xl text-primary">
                                    {{ formatRupiah(material.price) }}
                                </span>
                                <span class="text-sm font-semibold text-slate-500">
                                    {{ material.unit_detail || `per ${material.sale_unit}` }}
                                </span>
                                <span v-if="material.sub_price_badge" class="bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-md border border-blue-100">
                                    {{ material.sub_price_badge }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <h3 class="font-headline font-bold text-xs uppercase tracking-wider text-slate-400 mb-2">
                                Deskripsi & Spesifikasi Produk
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                {{ material.description }}
                            </p>
                        </div>

                        <!-- Calculator Callout if supported -->
                        <div v-if="material.calculator_enabled" class="p-4 rounded-2xl bg-primary-50 border border-primary/20 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center shrink-0">
                                    <Calculator class="w-5 h-5" />
                                </div>
                                <div class="text-xs">
                                    <h4 class="font-bold text-secondary-900">Mendukung Hitung Otomatis</h4>
                                    <p class="text-slate-600">Hitung berapa dus/kaleng yang pas untuk ukuran ruangan Anda.</p>
                                </div>
                            </div>
                            <Link
                                :href="route('calculator.index', { product: material.slug })"
                                class="px-4 py-2 bg-primary text-white font-headline font-bold text-xs rounded-xl hover:bg-primary-600 transition shrink-0"
                            >
                                Buka Kalkulator
                            </Link>
                        </div>

                        <!-- Quantity Selector & Add to Cart -->
                        <div class="pt-4 border-t border-slate-100 space-y-4">
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold text-secondary-900">Jumlah Beli:</span>
                                <div class="flex items-center border border-slate-300 rounded-xl overflow-hidden bg-slate-50">
                                    <button
                                        @click="quantity = Math.max(1, quantity - 1)"
                                        class="px-3.5 py-2 text-slate-600 hover:bg-slate-200 transition"
                                    >
                                        <Minus class="w-4 h-4" />
                                    </button>
                                    <span class="px-4 py-2 text-xs font-bold text-secondary-900 bg-white min-w-10 text-center">
                                        {{ quantity }}
                                    </span>
                                    <button
                                        @click="quantity++"
                                        class="px-3.5 py-2 text-slate-600 hover:bg-slate-200 transition"
                                    >
                                        <Plus class="w-4 h-4" />
                                    </button>
                                </div>
                                <span class="text-xs font-medium text-slate-500">
                                    Total: <strong class="text-secondary-900 font-bold">{{ formatRupiah(material.price * quantity) }}</strong>
                                </span>
                            </div>

                            <div class="flex gap-3">
                                <button
                                    @click="addToCart"
                                    type="button"
                                    class="flex-1 flex items-center justify-center gap-2 py-4 bg-primary hover:bg-primary-600 text-white font-headline font-bold text-sm rounded-xl shadow-lg shadow-primary/25 transition active:scale-98"
                                >
                                    <ShoppingCart class="w-4 h-4" />
                                    <span>Masukkan ke Keranjang</span>
                                </button>
                            </div>
                        </div>

                        <!-- Guarantees list -->
                        <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-3 text-[11px] text-slate-500">
                            <div class="flex items-center gap-2">
                                <Truck class="w-4 h-4 text-primary shrink-0" />
                                <span>Pengiriman armada toko mandiri</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <ShieldCheck class="w-4 h-4 text-tertiary-600 shrink-0" />
                                <span>Garansi material asli kualitas pabrik</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Materials -->
            <div v-if="relatedMaterials && relatedMaterials.length > 0" class="space-y-4">
                <h3 class="font-headline font-bold text-lg text-secondary-900">
                    Material Sejenis Lainnya
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <Link
                        v-for="rel in relatedMaterials"
                        :key="rel.id"
                        :href="route('catalog.show', rel.slug)"
                        class="bg-white rounded-2xl border border-slate-200 p-4 hover:shadow-lg transition space-y-2 group"
                    >
                        <div class="h-32 bg-slate-100 rounded-xl overflow-hidden flex items-center justify-center">
                            <img v-if="rel.image_path" :src="rel.image_path" :alt="rel.name" class="w-full h-full object-cover group-hover:scale-105 transition" />
                            <Building2 v-else class="w-10 h-10 text-slate-300" />
                        </div>
                        <h4 class="font-headline font-bold text-xs text-secondary-900 line-clamp-1 group-hover:text-primary transition">
                            {{ rel.name }}
                        </h4>
                        <span class="font-headline font-bold text-sm text-primary block">
                            {{ formatRupiah(rel.price) }}
                        </span>
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
