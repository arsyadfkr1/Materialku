<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Package,
    Clock,
    Truck,
    CheckCircle2,
    XCircle,
    Search,
    Filter,
    MessageCircle,
    MessageSquare,
    ExternalLink,
    ChevronRight,
    Eye,
    AlertCircle,
    FileText,
    User,
    Phone,
    MapPin,
    DollarSign,
    RotateCcw,
    X,
    Calendar,
    ArrowUpRight,
    ShoppingBag,
    BadgePercent,
    Building2,
    Check
} from 'lucide-vue-next';

const props = defineProps({
    orders: Object,
    filters: Object,
    stats: Object,
    setting: Object,
});

const page = usePage();

// Filter State
const search = ref(props.filters?.search || '');
const activeStatus = ref(props.filters?.status || 'all');

// Detail Modal State
const selectedOrder = ref(null);
const isModalOpen = ref(false);

const updateForm = useForm({
    status: '',
    admin_note: '',
});

// Format Rupiah
const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

// Format Tanggal
const formatDate = (isoStr) => {
    if (!isoStr) return '-';
    const date = new Date(isoStr);
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
};

// Status Styling Helper
const getStatusMeta = (status) => {
    switch (status) {
        case 'Menunggu Konfirmasi':
            return {
                bg: 'bg-amber-50',
                text: 'text-amber-700',
                border: 'border-amber-200',
                badge: 'bg-amber-100 text-amber-800 border-amber-200',
                dot: 'bg-amber-500',
                icon: Clock,
            };
        case 'Diproses':
            return {
                bg: 'bg-blue-50',
                text: 'text-blue-700',
                border: 'border-blue-200',
                badge: 'bg-blue-100 text-blue-800 border-blue-200',
                dot: 'bg-blue-500',
                icon: Package,
            };
        case 'Dikirim':
            return {
                bg: 'bg-purple-50',
                text: 'text-purple-700',
                border: 'border-purple-200',
                badge: 'bg-purple-100 text-purple-800 border-purple-200',
                dot: 'bg-purple-500',
                icon: Truck,
            };
        case 'Selesai':
            return {
                bg: 'bg-emerald-50',
                text: 'text-emerald-700',
                border: 'border-emerald-200',
                badge: 'bg-emerald-100 text-emerald-800 border-emerald-200',
                dot: 'bg-emerald-500',
                icon: CheckCircle2,
            };
        case 'Dibatalkan':
            return {
                bg: 'bg-red-50',
                text: 'text-red-700',
                border: 'border-red-200',
                badge: 'bg-red-100 text-red-800 border-red-200',
                dot: 'bg-red-500',
                icon: XCircle,
            };
        default:
            return {
                bg: 'bg-slate-50',
                text: 'text-slate-700',
                border: 'border-slate-200',
                badge: 'bg-slate-100 text-slate-800 border-slate-200',
                dot: 'bg-slate-500',
                icon: Clock,
            };
    }
};

// Filter Execution
const applyFilter = (status) => {
    activeStatus.value = status;
    router.get(
        route('dashboard'),
        {
            status: status === 'all' ? null : status,
            search: search.value || null,
        },
        { preserveState: true, replace: true }
    );
};

const handleSearch = () => {
    router.get(
        route('dashboard'),
        {
            status: activeStatus.value === 'all' ? null : activeStatus.value,
            search: search.value || null,
        },
        { preserveState: true, replace: true }
    );
};

const resetFilter = () => {
    search.value = '';
    activeStatus.value = 'all';
    router.get(route('dashboard'), {}, { preserveState: true, replace: true });
};

// Modal Handlers
const openDetail = (order) => {
    selectedOrder.value = order;
    updateForm.status = order.status;
    updateForm.admin_note = order.admin_note || '';
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedOrder.value = null;
    updateForm.reset();
};

const submitStatus = () => {
    if (!selectedOrder.value) return;

    updateForm.patch(route('admin.orders.update-status', selectedOrder.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            if (selectedOrder.value) {
                selectedOrder.value.status = updateForm.status;
                selectedOrder.value.admin_note = updateForm.admin_note;
            }
            closeModal();
        },
    });
};

// WhatsApp direct chat generator
const getWhatsAppLink = (order) => {
    if (!order || !order.whatsapp) return '#';
    let cleanPhone = order.whatsapp.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '62' + cleanPhone.substring(1);
    }

    const storeName = props.setting?.store_name || 'TB. Mandiri Sejahtera Abadi';
    const msg = `Halo Bpk/Ibu ${order.customer_name},\n\nKami dari admin *${storeName}* mengonfirmasi pesanan Anda dengan Kode Pesanan: *${order.order_code}* (Status: *${order.status}*).\n\nTotal belanja material: *${formatRupiah(order.material_total)}*.\nAlamat Pengiriman: ${order.address}\n\nAda yang bisa kami bantu mengenai jadwal armada pengiriman atau metode pembayaran? Terima kasih!`;

    return `https://wa.me/${cleanPhone}?text=${encodeURIComponent(msg)}`;
};

const statusOptions = [
    'Menunggu Konfirmasi',
    'Diproses',
    'Dikirim',
    'Selesai',
    'Dibatalkan',
];
</script>

<template>
    <Head title="Admin Dashboard – Manajemen Pesanan MATERIALKU" />

    <AuthenticatedLayout>
        <!-- Header Section -->
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-orange-100 text-primary uppercase tracking-wider">
                            Panel Operasional Toko
                        </span>
                        <span class="text-xs text-slate-400">•</span>
                        <span class="text-xs text-slate-500 font-medium">TB. Mandiri Sejahtera Abadi</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                        Manajemen Pesanan Masuk
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        :href="route('home')"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:border-primary text-slate-700 hover:text-primary rounded-xl text-xs font-bold transition shadow-xs"
                    >
                        <span>Lihat Web Toko</span>
                        <ExternalLink class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        </template>

        <div class="py-8 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Flash Success Notification -->
                <div
                    v-if="$page.props.flash?.success"
                    class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-emerald-800 text-sm font-semibold shadow-xs"
                >
                    <div class="flex items-center gap-2">
                        <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0" />
                        <span>{{ $page.props.flash.success }}</span>
                    </div>
                </div>

                <!-- 1. KPI STATS CARDS -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Total Pesanan -->
                    <div
                        @click="applyFilter('all')"
                        class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs hover:border-slate-400 transition cursor-pointer"
                        :class="activeStatus === 'all' ? 'ring-2 ring-slate-800' : ''"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase">Total Pesanan</span>
                            <div class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center">
                                <ShoppingBag class="w-4 h-4 text-slate-700" />
                            </div>
                        </div>
                        <p class="text-2xl font-black text-slate-900">{{ stats?.total || 0 }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">Semua status</p>
                    </div>

                    <!-- Menunggu Konfirmasi -->
                    <div
                        @click="applyFilter('Menunggu Konfirmasi')"
                        class="bg-white rounded-2xl p-4 border border-amber-200 shadow-xs hover:border-amber-400 transition cursor-pointer bg-amber-50/20"
                        :class="activeStatus === 'Menunggu Konfirmasi' ? 'ring-2 ring-amber-500' : ''"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-amber-700 uppercase">Menunggu</span>
                            <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center">
                                <Clock class="w-4 h-4 text-amber-600" />
                            </div>
                        </div>
                        <p class="text-2xl font-black text-amber-600">{{ stats?.menunggu_konfirmasi || 0 }}</p>
                        <p class="text-[11px] text-amber-600 font-semibold mt-1">⚡ Perlu tindakan</p>
                    </div>

                    <!-- Diproses -->
                    <div
                        @click="applyFilter('Diproses')"
                        class="bg-white rounded-2xl p-4 border border-blue-200 shadow-xs hover:border-blue-400 transition cursor-pointer bg-blue-50/20"
                        :class="activeStatus === 'Diproses' ? 'ring-2 ring-blue-500' : ''"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-blue-700 uppercase">Diproses</span>
                            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                <Package class="w-4 h-4 text-blue-600" />
                            </div>
                        </div>
                        <p class="text-2xl font-black text-blue-600">{{ stats?.diproses || 0 }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">Siapkan barang</p>
                    </div>

                    <!-- Dikirim -->
                    <div
                        @click="applyFilter('Dikirim')"
                        class="bg-white rounded-2xl p-4 border border-purple-200 shadow-xs hover:border-purple-400 transition cursor-pointer bg-purple-50/20"
                        :class="activeStatus === 'Dikirim' ? 'ring-2 ring-purple-500' : ''"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-purple-700 uppercase">Dikirim</span>
                            <div class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center">
                                <Truck class="w-4 h-4 text-purple-600" />
                            </div>
                        </div>
                        <p class="text-2xl font-black text-purple-600">{{ stats?.dikirim || 0 }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">Armada jalan</p>
                    </div>

                    <!-- Selesai -->
                    <div
                        @click="applyFilter('Selesai')"
                        class="bg-white rounded-2xl p-4 border border-emerald-200 shadow-xs hover:border-emerald-400 transition cursor-pointer bg-emerald-50/20"
                        :class="activeStatus === 'Selesai' ? 'ring-2 ring-emerald-500' : ''"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-emerald-700 uppercase">Selesai</span>
                            <div class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <CheckCircle2 class="w-4 h-4 text-emerald-600" />
                            </div>
                        </div>
                        <p class="text-2xl font-black text-emerald-600">{{ stats?.selesai || 0 }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">Pesanan tuntas</p>
                    </div>

                    <!-- Omzet Selesai -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-4 text-white shadow-xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-300 uppercase">Omzet Selesai</span>
                            <div class="w-7 h-7 bg-white/10 rounded-lg flex items-center justify-center">
                                <DollarSign class="w-4 h-4 text-orange-400" />
                            </div>
                        </div>
                        <p class="text-lg font-black text-white truncate">{{ formatRupiah(stats?.total_revenue || 0) }}</p>
                        <p class="text-[10px] text-slate-400 mt-1">Transaksi lunas</p>
                    </div>
                </div>

                <!-- 2. FILTER & SEARCH TOOLBAR -->
                <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5 shadow-xs space-y-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        
                        <!-- Status Tabs -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button
                                @click="applyFilter('all')"
                                :class="activeStatus === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                            >
                                <span>Semua Status</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white/20">{{ stats?.total || 0 }}</span>
                            </button>

                            <button
                                @click="applyFilter('Menunggu Konfirmasi')"
                                :class="activeStatus === 'Menunggu Konfirmasi' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                            >
                                <span>Menunggu</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-200 text-amber-900">{{ stats?.menunggu_konfirmasi || 0 }}</span>
                            </button>

                            <button
                                @click="applyFilter('Diproses')"
                                :class="activeStatus === 'Diproses' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-800 hover:bg-blue-100'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                            >
                                <span>Diproses</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-200 text-blue-900">{{ stats?.diproses || 0 }}</span>
                            </button>

                            <button
                                @click="applyFilter('Dikirim')"
                                :class="activeStatus === 'Dikirim' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-800 hover:bg-purple-100'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                            >
                                <span>Dikirim</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-purple-200 text-purple-900">{{ stats?.dikirim || 0 }}</span>
                            </button>

                            <button
                                @click="applyFilter('Selesai')"
                                :class="activeStatus === 'Selesai' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                            >
                                <span>Selesai</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-200 text-emerald-900">{{ stats?.selesai || 0 }}</span>
                            </button>

                            <button
                                @click="applyFilter('Dibatalkan')"
                                :class="activeStatus === 'Dibatalkan' ? 'bg-red-600 text-white shadow-xs' : 'bg-red-50 text-red-800 hover:bg-red-100'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                            >
                                <span>Batal</span>
                                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-red-200 text-red-900">{{ stats?.dibatalkan || 0 }}</span>
                            </button>
                        </div>

                        <!-- Search Form -->
                        <form @submit.prevent="handleSearch" class="flex items-center gap-2 w-full lg:w-auto">
                            <div class="relative flex-1 lg:w-72">
                                <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Cari kode, nama, no WA..."
                                    class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition"
                                />
                            </div>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-xl text-xs font-bold transition shadow-xs"
                            >
                                Cari
                            </button>
                            <button
                                v-if="search || activeStatus !== 'all'"
                                type="button"
                                @click="resetFilter"
                                class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition"
                                title="Reset Filter"
                            >
                                <RotateCcw class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 3. ORDERS TABLE -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-4 px-6">Kode & Tanggal</th>
                                    <th class="py-4 px-6">Pelanggan</th>
                                    <th class="py-4 px-6">Rincian Material</th>
                                    <th class="py-4 px-6">Total Belanja</th>
                                    <th class="py-4 px-6">Status Pesanan</th>
                                    <th class="py-4 px-6 text-right">Aksi Cepat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                <tr
                                    v-for="order in orders?.data"
                                    :key="order.id"
                                    class="hover:bg-slate-50/80 transition"
                                >
                                    <!-- Kode & Tanggal -->
                                    <td class="py-4 px-6 align-top">
                                        <p class="font-bold text-slate-900 font-mono text-sm tracking-tight">
                                            {{ order.order_code }}
                                        </p>
                                        <p class="text-[11px] text-slate-400 flex items-center gap-1 mt-1">
                                            <Calendar class="w-3 h-3 text-slate-400" />
                                            <span>{{ formatDate(order.created_at) }}</span>
                                        </p>
                                    </td>

                                    <!-- Pelanggan -->
                                    <td class="py-4 px-6 align-top max-w-xs">
                                        <p class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <User class="w-3.5 h-3.5 text-slate-400" />
                                            <span>{{ order.customer_name }}</span>
                                        </p>
                                        <p class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-1">
                                            <Phone class="w-3 h-3 text-slate-400" />
                                            <span>{{ order.whatsapp }}</span>
                                        </p>
                                        <p class="text-[11px] text-slate-400 truncate mt-1 flex items-start gap-1" :title="order.address">
                                            <MapPin class="w-3 h-3 text-slate-400 shrink-0 mt-0.5" />
                                            <span class="truncate">{{ order.address }}</span>
                                        </p>
                                    </td>

                                    <!-- Rincian Material -->
                                    <td class="py-4 px-6 align-top">
                                        <div class="space-y-1">
                                            <div
                                                v-for="(item, idx) in order.items?.slice(0, 2)"
                                                :key="idx"
                                                class="flex items-center gap-2 text-slate-700"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full bg-primary shrink-0"></span>
                                                <span class="font-medium truncate max-w-xs">{{ item.name_snapshot }}</span>
                                                <span class="text-slate-400 text-[11px] shrink-0 font-bold">({{ item.quantity }} {{ item.unit_snapshot }})</span>
                                            </div>
                                            <p v-if="order.items?.length > 2" class="text-[11px] text-primary font-semibold pl-3.5">
                                                +{{ order.items.length - 2 }} material lainnya
                                            </p>
                                        </div>
                                    </td>

                                    <!-- Total Belanja -->
                                    <td class="py-4 px-6 align-top whitespace-nowrap">
                                        <p class="font-extrabold text-slate-900 text-sm">
                                            {{ formatRupiah(order.material_total) }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                            {{ order.items?.length || 0 }} jenis item
                                        </p>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="py-4 px-6 align-top whitespace-nowrap">
                                        <div
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-[11px] font-bold"
                                            :class="getStatusMeta(order.status).badge"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full" :class="getStatusMeta(order.status).dot"></span>
                                            <span>{{ order.status }}</span>
                                        </div>
                                        <p v-if="order.admin_note" class="text-[10px] text-slate-400 italic truncate max-w-[160px] mt-1" :title="order.admin_note">
                                            "{{ order.admin_note }}"
                                        </p>
                                    </td>

                                    <!-- Aksi Cepat -->
                                    <td class="py-4 px-6 align-top text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Tombol WhatsApp Pelanggan -->
                                            <a
                                                :href="getWhatsAppLink(order)"
                                                target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold transition shadow-xs"
                                                title="Hubungi WhatsApp Pelanggan"
                                            >
                                                <MessageCircle class="w-3.5 h-3.5 text-emerald-600" />
                                                <span class="hidden sm:inline">Chat WA</span>
                                            </a>

                                            <!-- Tombol Detail / Edit Status -->
                                            <button
                                                @click="openDetail(order)"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-xs"
                                            >
                                                <Eye class="w-3.5 h-3.5" />
                                                <span>Detail</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty State -->
                                <tr v-if="!orders?.data || orders.data.length === 0">
                                    <td colspan="6" class="py-16 text-center">
                                        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-400">
                                            <Package class="w-8 h-8" />
                                        </div>
                                        <h3 class="font-bold text-slate-700 text-base mb-1">Belum Ada Pesanan</h3>
                                        <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">
                                            Tidak ada data pesanan yang sesuai dengan filter atau kata kunci pencarian Anda.
                                        </p>
                                        <button
                                            v-if="search || activeStatus !== 'all'"
                                            @click="resetFilter"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition"
                                        >
                                            <RotateCcw class="w-3.5 h-3.5" />
                                            <span>Reset Semua Filter</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="orders?.links && orders.links.length > 3"
                        class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500"
                    >
                        <span>
                            Menampilkan {{ orders.from || 0 }} - {{ orders.to || 0 }} dari {{ orders.total || 0 }} pesanan
                        </span>
                        <div class="flex items-center gap-1">
                            <template v-for="(link, i) in orders.links" :key="i">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1.5 rounded-lg border font-semibold transition"
                                    :class="link.active ? 'bg-primary text-white border-primary' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="px-3 py-1.5 text-slate-300 border border-transparent"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 4. MODAL DETAIL & EDIT STATUS PESANAN -->
        <div
            v-if="isModalOpen && selectedOrder"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div class="bg-white rounded-3xl max-w-3xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-sm font-black text-slate-900">{{ selectedOrder.order_code }}</span>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                                :class="getStatusMeta(selectedOrder.status).badge"
                            >
                                <span class="w-1.5 h-1.5 rounded-full" :class="getStatusMeta(selectedOrder.status).dot"></span>
                                {{ selectedOrder.status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">Diajukan pada {{ formatDate(selectedOrder.created_at) }}</p>
                    </div>

                    <button
                        @click="closeModal"
                        class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-6">

                    <!-- Data Pelanggan & Alamat -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 rounded-2xl p-4 border border-slate-200/80">
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Pemesan</h4>
                            <p class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                <User class="w-4 h-4 text-slate-500" />
                                <span>{{ selectedOrder.customer_name }}</span>
                            </p>
                            <p class="text-xs text-slate-600 flex items-center gap-1.5">
                                <Phone class="w-3.5 h-3.5 text-slate-500" />
                                <span>{{ selectedOrder.whatsapp }}</span>
                            </p>
                            <a
                                :href="getWhatsAppLink(selectedOrder)"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-bold hover:underline pt-1"
                            >
                                <MessageCircle class="w-3.5 h-3.5" />
                                <span>Kirim Pesan WhatsApp Langsung</span>
                                <ExternalLink class="w-3 h-3" />
                            </a>
                        </div>

                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Alamat & Catatan</h4>
                            <p class="text-xs text-slate-700 leading-relaxed flex items-start gap-1.5">
                                <MapPin class="w-4 h-4 text-primary shrink-0 mt-0.5" />
                                <span>{{ selectedOrder.address }}</span>
                            </p>
                            <div v-if="selectedOrder.customer_note" class="pt-1">
                                <span class="text-[11px] font-bold text-slate-500">Catatan Khusus Pembeli:</span>
                                <p class="text-xs text-slate-600 italic bg-white p-2 rounded-lg border border-slate-200 mt-1">
                                    "{{ selectedOrder.customer_note }}"
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Item Material yang Dipesan -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                            <Package class="w-4 h-4 text-primary" />
                            <span>Daftar Material yang Dipesan ({{ selectedOrder.items?.length || 0 }} Item)</span>
                        </h4>

                        <div class="border border-slate-200 rounded-2xl overflow-hidden">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="py-2.5 px-4">Nama Produk Material</th>
                                        <th class="py-2.5 px-4 text-center">Jumlah</th>
                                        <th class="py-2.5 px-4 text-right">Harga Satuan</th>
                                        <th class="py-2.5 px-4 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="item in selectedOrder.items" :key="item.id" class="hover:bg-slate-50/50">
                                        <td class="py-3 px-4">
                                            <p class="font-bold text-slate-800">{{ item.name_snapshot }}</p>
                                            <span class="text-[10px] text-slate-400">Satuan jual: {{ item.unit_snapshot }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-center font-bold text-slate-700">
                                            {{ item.quantity }} {{ item.unit_snapshot }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-slate-600">
                                            {{ formatRupiah(item.price_snapshot) }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                            {{ formatRupiah(item.subtotal) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-orange-50/40 border-t border-orange-100">
                                    <tr>
                                        <td colspan="3" class="py-3 px-4 font-bold text-slate-800 text-right">
                                            Total Estimasi Belanja Material:
                                        </td>
                                        <td class="py-3 px-4 font-black text-primary text-right text-sm">
                                            {{ formatRupiah(selectedOrder.material_total) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <p class="text-[11px] text-slate-400 italic mt-2">
                            *Biaya ongkos kirim armada toko disepakati bersama pelanggan secara terpisah melalui WhatsApp.
                        </p>
                    </div>

                    <!-- Form Update Status Pesanan -->
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-4">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <Clock class="w-4 h-4 text-primary" />
                            <span>Perbarui Status & Catatan Toko</span>
                        </h4>

                        <form @submit.prevent="submitStatus" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Status Pesanan Saat Ini:
                                    </label>
                                    <select
                                        v-model="updateForm.status"
                                        class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary"
                                    >
                                        <option v-for="opt in statusOptions" :key="opt" :value="opt">
                                            {{ opt }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Catatan Operasional Admin:
                                    </label>
                                    <input
                                        v-model="updateForm.admin_note"
                                        type="text"
                                        placeholder="Contoh: DP 50% diterima, kirim via Truk BE 1234 XX"
                                        class="w-full text-xs text-slate-800 bg-white border border-slate-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="px-4 py-2 border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-bold transition"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    :disabled="updateForm.processing"
                                    class="px-5 py-2 bg-primary hover:bg-primary/90 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5"
                                >
                                    <Check class="w-4 h-4" />
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Riwayat Status (Audit Trail) -->
                    <div v-if="selectedOrder.status_histories && selectedOrder.status_histories.length > 0">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                            Riwayat Perubahan Status (Audit Trail)
                        </h4>
                        <div class="space-y-2 border-l-2 border-slate-200 ml-2 pl-4">
                            <div
                                v-for="(h, idx) in selectedOrder.status_histories"
                                :key="idx"
                                class="relative text-xs space-y-0.5 pb-2"
                            >
                                <div class="w-2.5 h-2.5 rounded-full bg-primary absolute -left-[21px] top-1"></div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-800">{{ h.to_status }}</span>
                                    <span class="text-[10px] text-slate-400">• {{ formatDate(h.created_at) }}</span>
                                </div>
                                <p v-if="h.note" class="text-slate-500 text-[11px] italic">
                                    "{{ h.note }}"
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <a
                        :href="getWhatsAppLink(selectedOrder)"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 text-xs text-emerald-700 font-bold bg-emerald-100 hover:bg-emerald-200 px-4 py-2 rounded-xl transition"
                    >
                        <MessageCircle class="w-4 h-4 text-emerald-600" />
                        <span>Hubungi Pembeli via WhatsApp</span>
                    </a>

                    <button
                        @click="closeModal"
                        class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition"
                    >
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
