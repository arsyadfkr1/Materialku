<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import {
    CheckCircle2,
    MessageSquare,
    Copy,
    Check,
    Clock,
    Truck,
    MapPin,
    AlertCircle,
    ArrowLeft,
    Building2,
    ShieldCheck
} from 'lucide-vue-next';

const props = defineProps({
    order: Object,
    isAuthorized: Boolean,
    whatsappUrl: String,
    whatsappMessage: String,
    setting: Object,
});

const isCopied = ref(false);

const copyOrderText = () => {
    if (props.whatsappMessage) {
        navigator.clipboard.writeText(props.whatsappMessage);
        isCopied.value = true;
        setTimeout(() => {
            isCopied.value = false;
        }, 3000);
    }
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'Diproses':
            return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'Dikirim':
            return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'Selesai':
            return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'Dibatalkan':
            return 'bg-red-100 text-red-800 border-red-200';
        default:
            return 'bg-amber-50 text-amber-700 border-amber-200';
    }
};
</script>

<template>
    <PublicLayout :setting="setting">
        <Head :title="`Ringkasan Pesanan #${order?.order_code} - MATERIALKU`" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Top Status Alert Box -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
                <!-- Success Header -->
                <div class="text-center space-y-3 pb-6 border-b border-slate-100">
                    <div class="w-16 h-16 bg-emerald-50 text-tertiary-600 rounded-full flex items-center justify-center mx-auto shadow-sm">
                        <CheckCircle2 class="w-10 h-10" />
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border" :class="getStatusBadge(order?.status)">
                        <Clock class="w-3.5 h-3.5" />
                        <span>Status: {{ order?.status }}</span>
                    </div>
                    <h1 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900">
                        Pesanan Berhasil Disimpan!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto">
                        Kode unik pesanan Anda: <strong class="text-secondary-900 font-mono text-base px-2 py-0.5 bg-slate-100 rounded-md">{{ order?.order_code }}</strong>.
                        Silakan lanjutkan konfirmasi ke WhatsApp admin toko.
                    </p>
                </div>

                <!-- WhatsApp Action Buttons Row -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 text-center space-y-4">
                    <h3 class="font-headline font-bold text-sm text-secondary-900">
                        Langkah Selanjutnya: Buka WhatsApp Toko
                    </h3>
                    <p class="text-xs text-slate-600 max-w-md mx-auto">
                        Klik tombol di bawah untuk langsung membuka chat WhatsApp dengan format pesanan yang sudah terisi otomatis.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 bg-tertiary-600 hover:bg-tertiary-500 text-white font-headline font-bold text-sm rounded-xl shadow-lg shadow-tertiary-600/25 transition transform hover:-translate-y-0.5"
                        >
                            <MessageSquare class="w-5 h-5" />
                            <span>Lanjutkan ke WhatsApp Toko</span>
                        </a>

                        <button
                            @click="copyOrderText"
                            type="button"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white hover:bg-slate-100 text-secondary-800 border border-slate-300 font-headline font-bold text-xs rounded-xl transition"
                        >
                            <Check v-if="isCopied" class="w-4 h-4 text-tertiary-600" />
                            <Copy v-else class="w-4 h-4 text-slate-500" />
                            <span>{{ isCopied ? 'Teks Tersalin!' : 'Salin Format Pesanan' }}</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-400 italic">
                        *Jika tombol WhatsApp tidak terbuka otomatis di perangkat Anda, silakan salin teks dan kirim manual ke nomor toko.
                    </p>
                </div>

                <!-- Order Details & Items Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <!-- Column 1: Customer & Delivery Info -->
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3 text-xs">
                        <h4 class="font-headline font-bold text-sm text-secondary-900 pb-2 border-b border-slate-200">
                            Data Pengantaran Armada
                        </h4>
                        <div class="space-y-2">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nama Pemesan:</span>
                                <span class="font-bold text-secondary-900">{{ order?.customer_name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">WhatsApp:</span>
                                <span class="font-bold text-secondary-900 font-mono">+{{ order?.whatsapp }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Alamat Tujuan:</span>
                                <span class="font-medium text-secondary-800">{{ order?.address }}</span>
                            </div>
                            <div v-if="order?.customer_note">
                                <span class="text-slate-400 block text-[11px]">Catatan Khusus:</span>
                                <span class="font-medium text-slate-700 italic">{{ order?.customer_note }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Waktu Pengajuan:</span>
                                <span class="text-slate-600">{{ formatDate(order?.created_at) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Order Summary & Snapshot -->
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3 text-xs flex flex-col justify-between">
                        <div>
                            <h4 class="font-headline font-bold text-sm text-secondary-900 pb-2 border-b border-slate-200">
                                Rincian Material (Snapshot)
                            </h4>
                            <div class="space-y-2.5 pt-2 max-h-56 overflow-y-auto pr-1">
                                <div
                                    v-for="(item, idx) in order?.items"
                                    :key="item.id"
                                    class="flex justify-between items-start text-xs pb-2 border-b border-slate-200/60 last:border-0"
                                >
                                    <div>
                                        <span class="font-bold text-secondary-900 block leading-snug">
                                            {{ idx + 1 }}. {{ item.name_snapshot }}
                                        </span>
                                        <span class="text-[11px] text-slate-500">
                                            {{ item.quantity }} {{ item.unit_snapshot }} × {{ formatRupiah(item.price_snapshot) }}
                                        </span>
                                    </div>
                                    <span class="font-bold text-secondary-900">
                                        {{ formatRupiah(item.subtotal) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 space-y-1">
                            <div class="flex justify-between text-slate-600">
                                <span>Ongkos Kirim Armada:</span>
                                <span class="font-semibold text-primary">Dikonfirmasi via WA</span>
                            </div>
                            <div class="flex justify-between items-baseline pt-1">
                                <span class="font-bold text-sm text-secondary-900">Total Biaya Material:</span>
                                <span class="font-headline font-extrabold text-xl text-primary">
                                    {{ formatRupiah(order?.material_total) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back to Home Button -->
                <div class="pt-4 flex justify-between items-center text-xs">
                    <Link :href="route('home')" class="text-slate-600 hover:text-primary font-bold flex items-center gap-1.5 transition">
                        <ArrowLeft class="w-4 h-4" />
                        <span>Kembali ke Beranda</span>
                    </Link>
                    <Link :href="route('catalog.index')" class="text-primary font-bold hover:underline">
                        Buka Katalog Material Lainnya →
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
