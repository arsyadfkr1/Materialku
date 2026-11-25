<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import {
    MapPin,
    Clock,
    Phone,
    MessageCircle,
    ChevronDown,
    ChevronUp,
    Mail,
    Store,
    HelpCircle,
    CheckCircle2,
    ArrowRight,
    FileText,
    Banknote,
    XCircle,
    Ruler,
    ShoppingCart,
    Navigation,
    Layers,
    ZoomIn,
    Compass,
    Building2,
    Truck,
} from 'lucide-vue-next';

const props = defineProps({
    setting: Object,
});

// --- Leaflet Web GIS State ---
const mapContainer = ref(null);
let map = null;
let storeMarker = null;
let circleZone1 = null;
let circleZone2 = null;

// Koordinat Fisik TB. Mandiri Sejahtera Abadi (By Pass Soekarno-Hatta, Bandar Lampung)
const storeCoords = [-5.3850, 105.2850];

const showZone1 = ref(true);
const showZone2 = ref(true);

const initWebGis = () => {
    if (!mapContainer.value) return;

    // Inisialisasi Peta Leaflet
    map = L.map(mapContainer.value, {
        center: storeCoords,
        zoom: 12,
        zoomControl: true,
        scrollWheelZoom: true,
    });

    // Layer Peta Dasar: OpenStreetMap Standard
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Web GIS MATERIALKU',
    }).addTo(map);

    // Kustom Pin Marker HTML/Tailwind
    const customIcon = L.divIcon({
        className: 'custom-gis-marker',
        html: `
            <div style="position: relative; display: flex; align-items: center; justify-content: center; width: 44px; height: 44px;">
                <div style="position: absolute; width: 40px; height: 40px; background: rgba(249, 115, 22, 0.35); border-radius: 9999px; animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;"></div>
                <div style="position: relative; width: 36px; height: 36px; background: #ea580c; color: white; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3); border: 2.5px solid white; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    🏢
                </div>
            </div>
        `,
        iconSize: [44, 44],
        iconAnchor: [22, 22],
        popupAnchor: [0, -20],
    });

    // Marker Lokasi Toko
    storeMarker = L.marker(storeCoords, { icon: customIcon }).addTo(map);

    const storeName = props.setting?.store_name || 'TB. Mandiri Sejahtera Abadi';
    const storeAddress = props.setting?.store_address || 'Jl. Soekarno-Hatta No. 88, By Pass, Kota Bandar Lampung';

    const popupHtml = `
        <div style="font-family: inherit; font-size: 12px; color: #1e293b; max-width: 240px; padding: 4px;">
            <div style="display: inline-block; background: #ffedd5; color: #c2410c; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 9999px; text-transform: uppercase; margin-bottom: 6px;">
                🏢 PUSAT GUDANG & TOKO FISIK
            </div>
            <h4 style="font-weight: 800; font-size: 14px; margin: 0 0 4px 0; color: #0f172a;">${storeName}</h4>
            <p style="margin: 0 0 8px 0; font-size: 11px; color: #475569; line-height: 1.4;">${storeAddress}</p>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px; font-size: 10px; margin-bottom: 8px;">
                <p style="margin: 0 0 2px 0;"><strong>Jam Operasional:</strong> 07.00 - 17.00 WIB</p>
                <p style="margin: 0;"><strong>Kapasitas Stok:</strong> 500+ Ton Material SNI</p>
            </div>
            <a href="https://maps.google.com/?q=${encodeURIComponent(storeAddress)}" target="_blank" style="display: block; text-align: center; background: #ea580c; color: white; padding: 6px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 11px;">
                Buka Rute Google Maps ↗
            </a>
        </div>
    `;

    storeMarker.bindPopup(popupHtml).openPopup();

    // Layer Analisis Spasial GIS: Buffer Zona 1 (Radius 5 km)
    circleZone1 = L.circle(storeCoords, {
        color: '#10b981',
        fillColor: '#10b981',
        fillOpacity: 0.16,
        radius: 5000,
        weight: 2,
        dashArray: '6, 6',
    }).addTo(map);

    circleZone1.bindTooltip(
        '<b>Zona 1 (Radius 5 km)</b><br>Tanjung Karang, Way Halim, Sukarame, Kedaton.<br><i>Pengiriman Sameday Pickup & Promo Bebas Ongkir</i>',
        { sticky: true }
    );

    // Layer Analisis Spasial GIS: Buffer Zona 2 (Radius 12 km)
    circleZone2 = L.circle(storeCoords, {
        color: '#f97316',
        fillColor: '#f97316',
        fillOpacity: 0.08,
        radius: 12000,
        weight: 2,
        dashArray: '8, 8',
    }).addTo(map);

    circleZone2.bindTooltip(
        '<b>Zona 2 (Radius 12 km)</b><br>Rajabasa, Natar, Jati Agung / ITERA, Teluk Betung.<br><i>Pengiriman Armada Truk Engkel Material Berat</i>',
        { sticky: true }
    );
};

// Toggle Layer GIS
const toggleZone1 = () => {
    showZone1.value = !showZone1.value;
    if (!map || !circleZone1) return;
    if (showZone1.value) {
        circleZone1.addTo(map);
    } else {
        map.removeLayer(circleZone1);
    }
};

const toggleZone2 = () => {
    showZone2.value = !showZone2.value;
    if (!map || !circleZone2) return;
    if (showZone2.value) {
        circleZone2.addTo(map);
    } else {
        map.removeLayer(circleZone2);
    }
};

const focusStore = () => {
    if (!map) return;
    map.setView(storeCoords, 14, { animate: true });
    if (storeMarker) storeMarker.openPopup();
};

const fitAllZones = () => {
    if (!map || !circleZone2) return;
    map.fitBounds(circleZone2.getBounds(), { padding: [30, 30], animate: true });
};

onMounted(() => {
    initWebGis();
});

onBeforeUnmount(() => {
    if (map) {
        map.remove();
        map = null;
    }
});

// --- FAQ State ---
const openFaq = ref(null);

const faqs = [
    {
        id: 1,
        icon: FileText,
        question: 'Apakah ada nota atau faktur resmi dari toko?',
        answer:
            'Ya, setiap transaksi pembelian akan mendapatkan nota resmi dari toko yang mencantumkan rincian barang, jumlah, dan total harga. Nota dapat diberikan secara fisik saat pengiriman atau dikirim via WhatsApp dalam format digital.',
    },
    {
        id: 2,
        icon: Banknote,
        question: 'Apakah perlu DP (uang muka) sebelum barang dikirim?',
        answer:
            'Untuk pesanan di bawah Rp 2.000.000, pembayaran bisa COD (bayar saat barang tiba). Untuk pesanan di atas Rp 2.000.000 atau material potong/custom (seperti hebel, baja ringan, keramik volume besar), diperlukan DP minimal 50% terlebih dahulu sebelum armada berangkat.',
    },
    {
        id: 3,
        icon: XCircle,
        question: 'Bagaimana cara membatalkan pesanan yang sudah diajukan?',
        answer:
            'Pembatalan pesanan dapat dilakukan dengan menghubungi kami via WhatsApp sebelum armada pengiriman berangkat. Jika pesanan sudah dalam proses pengiriman, pembatalan tidak dapat dilakukan. Untuk pesanan yang sudah membayar DP dan dibatalkan sebelum barang disiapkan, DP dapat dikembalikan penuh dalam 1-2 hari kerja.',
    },
    {
        id: 4,
        icon: Ruler,
        question: 'Ruangan saya berbentuk L, bagaimana cara menghitung di kalkulator?',
        answer:
            'Untuk ruangan berbentuk L, hitung dengan membagi ruangan menjadi dua persegi panjang terpisah. Misalnya bagian A (5m × 4m = 20m²) dan bagian B (3m × 2m = 6m²). Masukkan angka pertama ke kalkulator, catat hasilnya, lalu masukkan angka kedua dan tambahkan keduanya. Jika bingung, hubungi kami via WhatsApp dan tim kami siap membantu kalkulasi.',
    },
    {
        id: 5,
        icon: ShoppingCart,
        question: 'Apakah ada minimum pembelian?',
        answer:
            'Tidak ada minimum pembelian untuk pengambilan langsung di toko (self-pickup). Namun untuk layanan pengiriman, minimum pembelian adalah Rp 500.000 untuk Zona 1 (dalam kota Bandar Lampung) dan Rp 1.000.000 untuk Zona 2 (pinggiran kota/luar kota). Hal ini dikarenakan biaya armada dan tenaga angkut yang perlu diperhitungkan.',
    },
];

function toggleFaq(id) {
    openFaq.value = openFaq.value === id ? null : id;
}

// WhatsApp link builder
function waLink(message = '') {
    const number = props.setting?.store_whatsapp ?? '6281234567890';
    const encoded = encodeURIComponent(message || 'Halo, saya ingin bertanya mengenai produk di MATERIALKU.');
    return `https://wa.me/${number}?text=${encoded}`;
}

const operasionalHours = [
    { day: 'Senin – Jumat', hours: '07.00 – 17.00 WIB', open: true },
    { day: 'Sabtu', hours: '07.00 – 15.00 WIB', open: true },
    { day: 'Minggu & Hari Libur', hours: 'Tutup', open: false },
];
</script>

<template>
    <Head title="Hubungi Toko – Web GIS MATERIALKU" />
    <PublicLayout>
        <!-- ===== HERO SECTION ===== -->
        <section class="bg-gradient-to-br from-orange-50 via-white to-slate-50 py-16 border-b border-slate-100">
            <div class="max-w-5xl mx-auto px-4 text-center">
                <span class="inline-block text-primary font-semibold text-sm uppercase tracking-widest mb-3">
                    Kami Siap Membantu
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-4">
                    Hubungi Toko & Pemetaan Lokasi
                </h1>
                <p class="text-slate-500 text-lg max-w-2xl mx-auto">
                    Ada pertanyaan soal produk, harga grosir, atau pemesanan? Tim
                    <span class="font-semibold text-slate-700">{{ setting?.store_name ?? 'TB. Mandiri Sejahtera Abadi' }}</span>
                    siap melayani Anda.
                </p>
                <!-- CTA WhatsApp besar -->
                <a
                    :href="waLink()"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-8 inline-flex items-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-4 rounded-xl shadow-lg transition-all hover:scale-105 text-lg"
                >
                    <MessageCircle class="w-6 h-6" />
                    Chat via WhatsApp
                </a>
            </div>
        </section>

        <!-- ===== INFO CARDS ===== -->
        <section class="max-w-5xl mx-auto px-4 py-14">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Alamat -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7 flex flex-col gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <MapPin class="w-6 h-6 text-primary" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg mb-1">Alamat Toko & Gudang</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">
                            {{ setting?.store_address ?? 'Jl. Soekarno-Hatta No. 88, By Pass, Kota Bandar Lampung' }}
                        </p>
                    </div>
                    <button
                        @click="focusStore"
                        class="inline-flex items-center gap-1 text-primary text-sm font-semibold hover:underline mt-auto cursor-pointer"
                    >
                        Lihat di Web GIS
                        <ArrowRight class="w-4 h-4" />
                    </button>
                </div>

                <!-- Jam Operasional -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7 flex flex-col gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <Clock class="w-6 h-6 text-blue-600" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg mb-3">Jam Operasional</h3>
                        <ul class="space-y-2">
                            <li
                                v-for="item in operasionalHours"
                                :key="item.day"
                                class="flex justify-between items-center text-sm"
                            >
                                <span class="text-slate-600">{{ item.day }}</span>
                                <span
                                    :class="item.open ? 'text-green-600 font-semibold' : 'text-red-400 font-semibold'"
                                >
                                    {{ item.hours }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Kontak Langsung -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-7 flex flex-col gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <Phone class="w-6 h-6 text-green-600" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg mb-3">Kontak Langsung</h3>
                        <div class="space-y-3">
                            <a
                                :href="waLink()"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex items-center gap-3 p-3 bg-green-50 rounded-xl hover:bg-green-100 transition group"
                            >
                                <MessageCircle class="w-5 h-5 text-green-600 shrink-0" />
                                <div>
                                    <p class="text-xs text-slate-500 leading-none mb-1">WhatsApp CS</p>
                                    <p class="font-semibold text-slate-800 text-sm group-hover:text-green-700">
                                        +{{ setting?.store_whatsapp ?? '6281234567890' }}
                                    </p>
                                </div>
                            </a>
                            <a
                                :href="`tel:+${setting?.store_whatsapp ?? '6281234567890'}`"
                                class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition group"
                            >
                                <Phone class="w-5 h-5 text-slate-500 shrink-0" />
                                <div>
                                    <p class="text-xs text-slate-400 leading-none mb-1">Telepon</p>
                                    <p class="font-semibold text-slate-700 text-sm">
                                        +{{ setting?.store_whatsapp ?? '6281234567890' }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== WEB GIS INTERAKTIF LEAFLET ===== -->
        <section class="max-w-5xl mx-auto px-4 pb-14">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- GIS Header & Layer Controls -->
                <div class="p-6 sm:p-7 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-slate-50/60">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-orange-100 text-primary uppercase tracking-wider">
                                Web GIS Pemetaan
                            </span>
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs text-slate-500 font-medium">OpenStreetMap & Leaflet.js</span>
                        </div>
                        <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                            <Compass class="w-5 h-5 text-primary" />
                            Peta Spasial & Radius Jangkauan Armada
                        </h2>
                        <p class="text-slate-500 text-xs mt-1">
                            Pusat Gudang: <span class="font-semibold text-slate-700">{{ setting?.store_address ?? 'Jl. Soekarno-Hatta No. 88, By Pass, Kota Bandar Lampung' }}</span>
                        </p>
                    </div>

                    <!-- GIS Interactive Toolbar -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Toggle Zona 1 -->
                        <button
                            type="button"
                            @click="toggleZone1"
                            :class="showZone1 ? 'bg-emerald-50 text-emerald-800 border-emerald-300 font-bold' : 'bg-slate-100 text-slate-400 border-slate-200'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
                        >
                            <span class="w-2.5 h-2.5 rounded-full" :class="showZone1 ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                            <span>Zona 1 (5 km)</span>
                        </button>

                        <!-- Toggle Zona 2 -->
                        <button
                            type="button"
                            @click="toggleZone2"
                            :class="showZone2 ? 'bg-orange-50 text-orange-800 border-orange-300 font-bold' : 'bg-slate-100 text-slate-400 border-slate-200'"
                            class="px-3 py-1.5 rounded-xl border text-xs transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
                        >
                            <span class="w-2.5 h-2.5 rounded-full" :class="showZone2 ? 'bg-orange-500' : 'bg-slate-300'"></span>
                            <span>Zona 2 (12 km)</span>
                        </button>

                        <!-- Fokus Toko -->
                        <button
                            type="button"
                            @click="focusStore"
                            class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-2xs cursor-pointer"
                        >
                            <Navigation class="w-3.5 h-3.5" />
                            <span>Titik Toko</span>
                        </button>

                        <!-- Fit Bounds Seluruh Zona -->
                        <button
                            type="button"
                            @click="fitAllZones"
                            class="px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-2xs cursor-pointer"
                        >
                            <ZoomIn class="w-3.5 h-3.5" />
                            <span>Semua Zona</span>
                        </button>
                    </div>
                </div>

                <!-- Leaflet Map Container -->
                <div class="relative w-full h-[450px] sm:h-[520px] bg-slate-100 z-0">
                    <div ref="mapContainer" class="w-full h-full z-0"></div>

                    <!-- GIS Legend Overlay (Pojok Bawah Kiri) -->
                    <div class="absolute bottom-4 left-4 z-[400] bg-white/95 backdrop-blur-md p-3.5 rounded-2xl border border-slate-200/90 shadow-lg max-w-xs text-xs space-y-2 pointer-events-auto">
                        <p class="font-bold text-slate-900 flex items-center gap-1.5 text-[11px] uppercase tracking-wider">
                            <Layers class="w-3.5 h-3.5 text-primary" />
                            <span>Legenda Analisis Zonasi</span>
                        </p>
                        <div class="space-y-1.5 text-[11px]">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500/20 border-2 border-emerald-500 shrink-0"></span>
                                <span class="text-slate-700"><strong>Zona 1 (Radius 5 km):</strong> Pickup Sameday</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-orange-500/20 border-2 border-orange-500 shrink-0"></span>
                                <span class="text-slate-700"><strong>Zona 2 (Radius 12 km):</strong> Truk Engkel Berat</span>
                            </div>
                            <div class="flex items-center gap-2 pt-0.5">
                                <span class="text-sm shrink-0">🏢</span>
                                <span class="text-slate-700"><strong>Gudang & Toko:</strong> Stok 500+ Ton</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== QUICK WHATSAPP TOPICS ===== -->
        <section class="bg-gradient-to-r from-orange-500 to-orange-600 py-12">
            <div class="max-w-5xl mx-auto px-4">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white mb-2">Chat Langsung dengan Tim Kami</h2>
                    <p class="text-orange-100 text-sm">Pilih topik, langsung terhubung ke WhatsApp kami</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <a
                        :href="waLink('Halo, saya ingin menanyakan harga dan ketersediaan produk.')"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-white/10 hover:bg-white/20 border border-white/30 rounded-xl p-4 text-center text-white transition-all hover:scale-105 cursor-pointer"
                    >
                        <Store class="w-7 h-7 mx-auto mb-2 opacity-90" />
                        <p class="text-sm font-semibold">Cek Harga & Stok</p>
                    </a>
                    <a
                        :href="waLink('Halo, saya ingin menanyakan cara pemesanan material.')"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-white/10 hover:bg-white/20 border border-white/30 rounded-xl p-4 text-center text-white transition-all hover:scale-105 cursor-pointer"
                    >
                        <ShoppingCart class="w-7 h-7 mx-auto mb-2 opacity-90" />
                        <p class="text-sm font-semibold">Cara Pemesanan</p>
                    </a>
                    <a
                        :href="waLink('Halo, saya ingin menghitung kebutuhan material untuk proyek saya.')"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-white/10 hover:bg-white/20 border border-white/30 rounded-xl p-4 text-center text-white transition-all hover:scale-105 cursor-pointer"
                    >
                        <Ruler class="w-7 h-7 mx-auto mb-2 opacity-90" />
                        <p class="text-sm font-semibold">Konsultasi Proyek</p>
                    </a>
                    <a
                        :href="waLink('Halo, saya ingin menanyakan mengenai pengiriman material.')"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bg-white/10 hover:bg-white/20 border border-white/30 rounded-xl p-4 text-center text-white transition-all hover:scale-105 cursor-pointer"
                    >
                        <CheckCircle2 class="w-7 h-7 mx-auto mb-2 opacity-90" />
                        <p class="text-sm font-semibold">Info Pengiriman</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== FAQ TRANSAKSI ===== -->
        <section class="max-w-5xl mx-auto px-4 py-16">
            <div class="text-center mb-10">
                <span class="inline-block text-primary font-semibold text-sm uppercase tracking-widest mb-3">
                    Pertanyaan Umum
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-3">
                    FAQ Pemesanan & Transaksi
                </h2>
                <p class="text-slate-500 max-w-xl mx-auto text-sm">
                    Jawaban atas pertanyaan seputar nota, pembayaran, pembatalan pesanan, dan konsultasi material.
                </p>
            </div>

            <div class="space-y-3">
                <div
                    v-for="faq in faqs"
                    :key="faq.id"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
                >
                    <button
                        @click="toggleFaq(faq.id)"
                        class="w-full flex items-center gap-4 px-6 py-5 text-left hover:bg-slate-50 transition cursor-pointer"
                    >
                        <div class="w-9 h-9 bg-orange-50 rounded-lg flex items-center justify-center shrink-0">
                            <component :is="faq.icon" class="w-5 h-5 text-primary" />
                        </div>
                        <span class="font-semibold text-slate-800 flex-1 text-sm md:text-base">
                            {{ faq.question }}
                        </span>
                        <ChevronDown
                            v-if="openFaq !== faq.id"
                            class="w-5 h-5 text-slate-400 shrink-0 transition"
                        />
                        <ChevronUp
                            v-else
                            class="w-5 h-5 text-primary shrink-0 transition"
                        />
                    </button>
                    <div v-if="openFaq === faq.id" class="px-6 pb-5">
                        <div class="pl-13 ml-[52px] border-l-2 border-orange-100 pl-4">
                            <p class="text-slate-600 text-sm leading-relaxed">{{ faq.answer }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Link ke FAQ Pengiriman -->
            <div class="mt-8 p-5 bg-blue-50 rounded-2xl border border-blue-100 flex flex-col md:flex-row items-start md:items-center gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                    <HelpCircle class="w-5 h-5 text-blue-600" />
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-slate-800 text-sm">Pertanyaan seputar pengiriman & ongkos kirim?</p>
                    <p class="text-slate-500 text-sm mt-0.5">Lihat FAQ lengkap di halaman Informasi Pengiriman kami.</p>
                </div>
                <Link
                    :href="route('shipping.info')"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition shrink-0"
                >
                    Lihat FAQ Pengiriman
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </section>

        <!-- ===== CTA BOTTOM ===== -->
        <section class="bg-slate-800 py-14">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-white mb-3">Belum menemukan jawaban?</h2>
                <p class="text-slate-400 mb-8">
                    Hubungi kami langsung via WhatsApp — tim kami merespons dalam hitungan menit di jam operasional toko.
                </p>
                <a
                    :href="waLink('Halo, saya memiliki pertanyaan yang belum ada di FAQ.')"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-4 rounded-xl shadow-lg transition-all hover:scale-105 text-base"
                >
                    <MessageCircle class="w-5 h-5" />
                    Tanya via WhatsApp Sekarang
                </a>
                <p class="text-slate-500 text-xs mt-4">
                    Senin–Jumat 07.00–17.00 WIB • Sabtu 07.00–15.00 WIB
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
