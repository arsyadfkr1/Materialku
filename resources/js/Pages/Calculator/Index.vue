<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCartStore } from '@/Stores/cartStore';
import {
    Calculator,
    ShoppingCart,
    Info,
    ShieldAlert,
    CheckCircle2,
    RotateCcw,
    Layers,
    Sparkles
} from 'lucide-vue-next';

const props = defineProps({
    ceramicMaterials: Array,
    paintMaterials: Array,
    selectedMaterial: Object,
    setting: Object,
});

const cart = useCartStore();

// Type selection: ceramic or paint
const activeType = ref(props.selectedMaterial?.calculation_type || 'ceramic');

// Ceramic Form
const selectedCeramicId = ref(props.selectedMaterial?.calculation_type === 'ceramic' ? props.selectedMaterial.id : props.ceramicMaterials?.[0]?.id);
const selectedCeramic = computed(() => {
    return (props.ceramicMaterials || []).find((c) => c.id === selectedCeramicId.value) || props.ceramicMaterials?.[0];
});

const cLength = ref(4);
const cWidth = ref(3);
const cWaste = ref(10);

const cArea = computed(() => Math.round(cLength.value * cWidth.value * 100) / 100);
const cWasteRatio = computed(() => cWaste.value / 100);
const cRequiredArea = computed(() => Math.round(cArea.value * (1 + cWasteRatio.value) * 100) / 100);
const cCoverage = computed(() => selectedCeramic.value?.coverage_per_box || 1.44);
const cBoxes = computed(() => {
    if (cCoverage.value <= 0) return 0;
    return Math.ceil(cRequiredArea.value / cCoverage.value);
});
const cSubtotal = computed(() => cBoxes.value * (selectedCeramic.value?.price || 0));

// Paint Form
const selectedPaintId = ref(props.selectedMaterial?.calculation_type === 'paint' ? props.selectedMaterial.id : props.paintMaterials?.[0]?.id);
const selectedPaint = computed(() => {
    return (props.paintMaterials || []).find((p) => p.id === selectedPaintId.value) || props.paintMaterials?.[0];
});

const pLength = ref(10);
const pHeight = ref(3);
const pOpening = ref(0);
const pCoats = ref(2);
const pWaste = ref(10);

const pGrossArea = computed(() => Math.round(pLength.value * pHeight.value * 100) / 100);
const pNetArea = computed(() => Math.max(0, Math.round((pGrossArea.value - pOpening.value) * 100) / 100));
const pSpread = computed(() => selectedPaint.value?.spread_per_liter_per_coat || 10);
const pVolume = computed(() => selectedPaint.value?.package_volume_liters || 5);
const pLitersNeeded = computed(() => {
    if (pSpread.value <= 0) return 0;
    const liters = (pNetArea.value * pCoats.value / pSpread.value) * (1 + pWaste.value / 100);
    return Math.round(liters * 100) / 100;
});
const pCans = computed(() => {
    if (pVolume.value <= 0) return 0;
    return Math.ceil(pLitersNeeded.value / pVolume.value);
});
const pSubtotal = computed(() => pCans.value * (selectedPaint.value?.price || 0));

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

// Add to Cart
const addCeramicToCart = () => {
    if (!selectedCeramic.value) return;
    cart.addItem(selectedCeramic.value, cBoxes.value, {
        type: 'ceramic',
        length: cLength.value,
        width: cWidth.value,
        waste_percent: cWaste.value,
        coverage_per_box: cCoverage.value,
        required_area: cRequiredArea.value,
        boxes: cBoxes.value,
    });
    if (window.materialkuToast) {
        window.materialkuToast(`${cBoxes.value} dus ${selectedCeramic.value.name} ditambahkan ke keranjang!`);
    }
};

const addPaintToCart = () => {
    if (!selectedPaint.value) return;
    cart.addItem(selectedPaint.value, pCans.value, {
        type: 'paint',
        length: pLength.value,
        height: pHeight.value,
        opening_area: pOpening.value,
        coats: pCoats.value,
        waste_percent: pWaste.value,
        liters_needed: pLitersNeeded.value,
        cans: pCans.value,
    });
    if (window.materialkuToast) {
        window.materialkuToast(`${pCans.value} kaleng ${selectedPaint.value.name} ditambahkan ke keranjang!`);
    }
};
</script>

<template>
    <PublicLayout :setting="setting">
        <Head title="Kalkulator Kebutuhan Material - MATERIALKU" />

        <div class="bg-white border-b border-slate-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-2 text-xs font-bold text-primary uppercase tracking-wider mb-1">
                        <Link :href="route('home')" class="hover:underline">Beranda</Link>
                        <span>/</span>
                        <span>Kalkulator</span>
                    </div>
                    <h1 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900">
                        Kalkulator Kebutuhan Material
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Hitung estimasi jumlah dus keramik atau kaleng cat berdasarkan luas bidang kerja dan standar cadangan toko.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Type Selector Tabs -->
            <div class="flex justify-center mb-10">
                <div class="bg-slate-200/80 p-1.5 rounded-2xl flex max-w-md w-full shadow-inner">
                    <button
                        @click="activeType = 'ceramic'"
                        :class="activeType === 'ceramic' ? 'bg-white text-primary shadow-md font-bold' : 'text-slate-600 hover:text-secondary-900 font-semibold'"
                        class="flex-1 py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm font-headline"
                    >
                        <Layers class="w-4 h-4" />
                        <span>Kalkulator Keramik Lantai</span>
                    </button>
                    <button
                        @click="activeType = 'paint'"
                        :class="activeType === 'paint' ? 'bg-white text-primary shadow-md font-bold' : 'text-slate-600 hover:text-secondary-900 font-semibold'"
                        class="flex-1 py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm font-headline"
                    >
                        <Sparkles class="w-4 h-4" />
                        <span>Kalkulator Cat Dinding</span>
                    </button>
                </div>
            </div>

            <!-- 1. FORM KERAMIK -->
            <div v-if="activeType === 'ceramic'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Form Inputs -->
                <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div>
                        <label class="block font-headline font-bold text-sm text-secondary-900 mb-2">
                            Pilih Produk Keramik
                        </label>
                        <select
                            v-model="selectedCeramicId"
                            class="w-full text-sm font-semibold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20 py-3"
                        >
                            <option v-for="mat in ceramicMaterials" :key="mat.id" :value="mat.id">
                                {{ mat.name }} — {{ formatRupiah(mat.price) }} / {{ mat.sale_unit }} (Cakupan {{ mat.coverage_per_box }} m²/dus)
                            </option>
                        </select>
                        <p v-if="selectedCeramic?.calculation_note" class="text-xs text-slate-500 mt-2 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            💡 Catatan Produk: {{ selectedCeramic.calculation_note }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Panjang Ruang (P)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    v-model.number="cLength"
                                    min="0.5"
                                    step="0.5"
                                    class="w-full pr-8 py-2.5 text-sm font-bold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20"
                                />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">m</span>
                            </div>
                        </div>
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Lebar Ruang (L)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    v-model.number="cWidth"
                                    min="0.5"
                                    step="0.5"
                                    class="w-full pr-8 py-2.5 text-sm font-bold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20"
                                />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">m</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cadangan Waste -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="font-headline font-bold text-xs text-secondary-900">
                                Cadangan Potongan / Waste (%)
                            </label>
                            <span class="text-xs font-bold text-primary">{{ cWaste }}% Tambahan</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2.5">
                            <button
                                type="button"
                                v-for="w in [5, 10, 15]"
                                :key="w"
                                @click="cWaste = w"
                                :class="cWaste === w ? 'bg-primary text-white border-primary shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                class="py-2.5 rounded-xl border font-bold text-xs transition"
                            >
                                {{ w }}% {{ w === 10 ? '(Rekomendasi)' : '' }}
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">
                            *Cadangan 10% direkomendasikan untuk pemotongan sudut dinding dan cadangan renovasi di masa mendatang.
                        </p>
                    </div>
                </div>

                <!-- Right: Breakdown Result Box -->
                <div class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border-2 border-primary/20 shadow-xl space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <span class="font-headline font-bold text-base text-secondary-900">
                            Ringkasan Estimasi
                        </span>
                        <span class="text-[11px] bg-primary-50 text-primary-700 font-bold px-2.5 py-1 rounded-full">
                            Perhitungan Akurat
                        </span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Luas Ruangan Bersih:</span>
                            <span class="font-bold text-secondary-900">{{ cArea }} m²</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Kebutuhan Luas (+ Waste {{ cWaste }}%):</span>
                            <span class="font-bold text-secondary-900">{{ cRequiredArea }} m²</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Cakupan Kemasan Toko:</span>
                            <span class="font-bold text-secondary-900">{{ cCoverage }} m² / dus</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Harga Satuan:</span>
                            <span class="font-bold text-secondary-900">{{ formatRupiah(selectedCeramic?.price) }} / dus</span>
                        </div>

                        <!-- Big Result Display -->
                        <div class="pt-4 border-t border-slate-200 bg-slate-50 p-4 rounded-2xl text-center space-y-1">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">
                                Estimasi Jumlah Beli:
                            </span>
                            <span class="font-headline font-extrabold text-4xl text-primary block">
                                {{ cBoxes }} Dus
                            </span>
                            <span class="text-xs text-slate-400 block">
                                Dibulatkan ke atas sesuai kemasan utuh toko
                            </span>
                        </div>

                        <div class="flex justify-between items-baseline pt-2">
                            <span class="font-bold text-sm text-slate-600">Estimasi Total Biaya:</span>
                            <span class="font-headline font-extrabold text-2xl text-secondary-900">
                                {{ formatRupiah(cSubtotal) }}
                            </span>
                        </div>
                    </div>

                    <!-- Button Add to Cart -->
                    <button
                        @click="addCeramicToCart"
                        type="button"
                        class="w-full flex items-center justify-center gap-2 py-4 bg-primary hover:bg-primary-600 text-white font-headline font-bold text-sm rounded-xl shadow-lg shadow-primary/25 transition transform active:scale-98"
                    >
                        <ShoppingCart class="w-4 h-4" />
                        <span>Masukkan {{ cBoxes }} Dus ke Keranjang</span>
                    </button>

                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-800 space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <Info class="w-3.5 h-3.5" />
                            <span>Batas Tanggung Jawab Estimasi</span>
                        </div>
                        <p>
                            Hasil kalkulator adalah estimasi pembelian, bukan jaminan kecukupan material atau RAB profesional. Ongkir armada toko dikonfirmasi manual via WhatsApp.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. FORM CAT -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Form Inputs -->
                <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div>
                        <label class="block font-headline font-bold text-sm text-secondary-900 mb-2">
                            Pilih Produk Cat Tembok
                        </label>
                        <select
                            v-model="selectedPaintId"
                            class="w-full text-sm font-semibold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20 py-3"
                        >
                            <option v-for="mat in paintMaterials" :key="mat.id" :value="mat.id">
                                {{ mat.name }} — {{ formatRupiah(mat.price) }} / {{ mat.sale_unit }} (Isi {{ mat.package_volume_liters }}L, Daya sebar {{ mat.spread_per_liter_per_coat }} m²/L)
                            </option>
                        </select>
                        <p v-if="selectedPaint?.calculation_note" class="text-xs text-slate-500 mt-2 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            💡 Catatan Produk: {{ selectedPaint.calculation_note }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Panjang Dinding (P)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    v-model.number="pLength"
                                    min="1"
                                    step="0.5"
                                    class="w-full pr-8 py-2.5 text-sm font-bold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20"
                                />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">m</span>
                            </div>
                        </div>
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Tinggi Dinding (T)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    v-model.number="pHeight"
                                    min="1"
                                    step="0.5"
                                    class="w-full pr-8 py-2.5 text-sm font-bold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20"
                                />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">m</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Luas Bukaan (Pintu / Jendela)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    v-model.number="pOpening"
                                    min="0"
                                    step="0.5"
                                    class="w-full pr-8 py-2.5 text-sm font-bold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20"
                                />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">m²</span>
                            </div>
                        </div>
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Jumlah Lapisan Cat
                            </label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button
                                    type="button"
                                    v-for="c in [2, 3]"
                                    :key="c"
                                    @click="pCoats = c"
                                    :class="pCoats === c ? 'bg-primary text-white border-primary shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                    class="py-2.5 rounded-xl border font-bold text-xs transition"
                                >
                                    {{ c }} Lapis
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Breakdown Result Box -->
                <div class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border-2 border-primary/20 shadow-xl space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <span class="font-headline font-bold text-base text-secondary-900">
                            Ringkasan Estimasi
                        </span>
                        <span class="text-[11px] bg-primary-50 text-primary-700 font-bold px-2.5 py-1 rounded-full">
                            Perhitungan Akurat
                        </span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Luas Kotor Dinding:</span>
                            <span class="font-bold text-secondary-900">{{ pGrossArea }} m²</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Luas Bukaan Dikurangi:</span>
                            <span class="font-bold text-secondary-900">{{ pOpening }} m²</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Luas Bersih Pengecatan:</span>
                            <span class="font-bold text-secondary-900">{{ pNetArea }} m²</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Liter Cat Diperlukan:</span>
                            <span class="font-bold text-secondary-900">{{ pLitersNeeded }} Liter</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Kemasan Produk:</span>
                            <span class="font-bold text-secondary-900">{{ pVolume }} Liter / kaleng</span>
                        </div>

                        <!-- Big Result Display -->
                        <div class="pt-4 border-t border-slate-200 bg-slate-50 p-4 rounded-2xl text-center space-y-1">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">
                                Estimasi Jumlah Beli:
                            </span>
                            <span class="font-headline font-extrabold text-4xl text-primary block">
                                {{ pCans }} Kaleng
                            </span>
                            <span class="text-xs text-slate-400 block">
                                Kemasan {{ pVolume }} Liter (Dibulatkan ke atas)
                            </span>
                        </div>

                        <div class="flex justify-between items-baseline pt-2">
                            <span class="font-bold text-sm text-slate-600">Estimasi Total Biaya:</span>
                            <span class="font-headline font-extrabold text-2xl text-secondary-900">
                                {{ formatRupiah(pSubtotal) }}
                            </span>
                        </div>
                    </div>

                    <!-- Button Add to Cart -->
                    <button
                        @click="addPaintToCart"
                        type="button"
                        class="w-full flex items-center justify-center gap-2 py-4 bg-primary hover:bg-primary-600 text-white font-headline font-bold text-sm rounded-xl shadow-lg shadow-primary/25 transition transform active:scale-98"
                    >
                        <ShoppingCart class="w-4 h-4" />
                        <span>Masukkan {{ pCans }} Kaleng ke Keranjang</span>
                    </button>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
