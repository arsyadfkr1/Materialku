<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import {
    Truck,
    MapPin,
    Clock,
    ShieldCheck,
    CheckCircle2,
    AlertCircle,
    ChevronDown,
    ChevronUp,
    Phone,
    ArrowRight,
    Search,
    Package,
    Building2,
    Calendar,
    HelpCircle,
    Check,
    BadgePercent,
    ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
    setting: Object,
});

// --- State Mini Cek Ongkir Interaktif ---
const selectedDistrict = ref('');
const districts = [
    { name: 'Tanjung Karang / Enggal (Bandar Lampung)', zone: 'Zona 1', distance: '3 km', pickup: 35000, engkel: 60000, sameday: true, freeMin: 1500000 },
    { name: 'Kedaton / Way Halim (Bandar Lampung)', zone: 'Zona 1', distance: '4 km', pickup: 35000, engkel: 65000, sameday: true, freeMin: 1500000 },
    { name: 'Sukarame / Korpri (Bandar Lampung)', zone: 'Zona 1', distance: '4.5 km', pickup: 35000, engkel: 65000, sameday: true, freeMin: 1500000 },
    { name: 'Rajabasa / Unila (Bandar Lampung)', zone: 'Zona 2', distance: '7 km', pickup: 50000, engkel: 80000, sameday: true, freeMin: 2500000 },
    { name: 'Kemiling / Langkapura (Bandar Lampung)', zone: 'Zona 2', distance: '8 km', pickup: 55000, engkel: 85000, sameday: true, freeMin: 2500000 },
    { name: 'Teluk Betung / Garuntang (Bandar Lampung)', zone: 'Zona 2', distance: '8.5 km', pickup: 55000, engkel: 85000, sameday: true, freeMin: 2500000 },
    { name: 'Jati Agung / ITERA (Lampung Selatan)', zone: 'Zona 2', distance: '9 km', pickup: 60000, engkel: 95000, sameday: true, freeMin: 2500000 },
    { name: 'Natar (Lampung Selatan)', zone: 'Zona 2', distance: '12 km', pickup: 75000, engkel: 110000, sameday: true, freeMin: 3000000 },
    { name: 'Tanjung Bintang (Lampung Selatan)', zone: 'Zona 3', distance: '18 km', pickup: 110000, engkel: 165000, sameday: true, freeMin: 4000000 },
    { name: 'Gedong Tataan (Pesawaran)', zone: 'Zona 3', distance: '19 km', pickup: 120000, engkel: 175000, sameday: true, freeMin: 4000000 },
    { name: 'Kota Metro', zone: 'Luar Zona', distance: '42 km', pickup: 250000, engkel: 380000, sameday: false, freeMin: 6000000 },
    { name: 'Pringsewu', zone: 'Luar Zona', distance: '38 km', pickup: 240000, engkel: 360000, sameday: false, freeMin: 6000000 },
];

const selectedDistrictData = computed(() => {
    if (!selectedDistrict.value) return null;
    return districts.find(d => d.name === selectedDistrict.value) || null;
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(val);
};

// --- State Accordion FAQ ---
const activeFaq = ref(1);
const toggleFaq = (id) => {
    activeFaq.value = activeFaq.value === id ? null : id;
};

const faqs = [
    {
        id: 1,
        question: 'Apakah bisa bayar ongkos kirim dan pesanan di tempat (COD)?',
        answer: 'Untuk ongkir dapat dibayarkan tunai ke supir armada saat pesanan tiba. Namun untuk barang bernilai tinggi atau material potong/custom (seperti hebel, baja ringan, atau keramik volume besar), pemesanan dikonfirmasi melalui transfer resmi ke rekening toko sebelum armada berangkat.'
    },
    {
        id: 2,
        question: 'Bagaimana jika saat perjalanan pengiriman tiba-tiba hujan deras?',
        answer: 'Seluruh armada toko kami (Pickup maupun Truk) telah dilengkapi dengan terpal kedap air berlapis ganda dan tali pengikat baja/webbing. Material sensitif air seperti semen zak, kalsium, dan cat dipastikan tiba di lokasi proyek dalam kondisi kering dan utuh sempurna.'
    },
    {
        id: 3,
        question: 'Bisa minta pengiriman tiba pada jam tertentu saat mandor/tukang ada di lokasi?',
        answer: 'Tentu bisa! Saat konfirmasi pemesanan via WhatsApp, informasikan estimasi jam kedatangan yang Anda inginkan (misal: "Kirim hari Sabtu pukul 09.00 - 11.00 WIB"). Koordinator armada kami akan menjadwalkan rute agar tiba sesuai waktu kesiapan tukang di lapangan.'
    },
    {
        id: 4,
        question: 'Apakah bisa mengirim besi beton batangan 12 meter utuh tanpa ditekuk?',
        answer: 'Bisa. Kami memiliki armada Truk Cold Diesel Panjang dan Tronton khusus yang dapat mengangkut besi beton 12 meter utuh lurus tanpa perlu dibengkokkan, sehingga kualitas struktur tulangan bangunan Anda tetap optimal sesuai standar konstruksi SNI.'
    },
    {
        id: 5,
        question: 'Bagaimana jika jalan perumahan saya sempit atau terdapat portal pembatas?',
        answer: 'Mohon informasikan lebar jalan dan tinggi portal kepada admin toko saat pemesanan. Kami akan langsung mengalihkan rute menggunakan Pickup Bak Terbuka yang lincah masuk ke gang perumahan sempit lebar 2,5 - 3 meter dan aman melewati portal rendah.'
    },
    {
        id: 6,
        question: 'Apakah pembeli mendapatkan nota asli fisik toko bertanda tangan basah?',
        answer: 'Ya. Supir toko kami selalu membawa 2 rangkap Surat Jalan dan Nota Resmi Toko bertanda tangan basah dan cap toko. Satu rangkap asli untuk pembeli (sebagai bukti garansi & pembukuan proyek), dan satu rangkap kembali ke kasir toko.'
    }
];
</script>

<template>
    <PublicLayout :setting="setting">
        <Head title="Informasi Pengiriman & Tarif Ongkir Armada - MATERIALKU" />

        <!-- Breadcrumb & Header Hero -->
        <section class="bg-white border-b border-slate-200 py-10 sm:py-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
                    <Link :href="route('home')" class="hover:text-primary transition font-medium">Beranda</Link>
                    <span>/</span>
                    <span class="text-secondary-900 font-semibold">Informasi Pengiriman</span>
                </nav>

                <div class="max-w-3xl">
                    <span class="bg-primary-50 text-primary-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider font-headline">
                        LOGISTIK ARMADA MANDIRI TOKO
                    </span>
                    <h1 class="font-headline font-extrabold text-3xl sm:text-4xl text-secondary-900 mt-3 tracking-tight">
                        Informasi Pengiriman, Tarif Ongkir & Ketentuan Logistik
                    </h1>
                    <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                        Pengiriman material bangunan berat dan cairan ditangani langsung oleh armada mandiri toko tanpa kurir pihak ketiga untuk menjamin keutuhan semen, cat, dan keramik sampai ke lokasi proyek Anda.
                    </p>
                </div>

                <!-- 3 Top Value Props -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 pt-8 border-t border-slate-100">
                    <div class="flex items-center gap-3.5 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="w-10 h-10 rounded-xl bg-tertiary-100 text-tertiary-700 flex items-center justify-center shrink-0">
                            <ShieldCheck class="w-5 h-5" />
                        </div>
                        <div>
                            <h4 class="font-headline font-bold text-xs text-secondary-900">Garansi Utuh di Tempat</h4>
                            <p class="text-[11px] text-slate-500">Ganti baru jika keramik retak atau sak sobek</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3.5 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center shrink-0">
                            <Truck class="w-5 h-5" />
                        </div>
                        <div>
                            <h4 class="font-headline font-bold text-xs text-secondary-900">100% Armada Toko Sendiri</h4>
                            <p class="text-[11px] text-slate-500">Supir & kenek toko terlatih muatan material</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3.5 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                            <Clock class="w-5 h-5" />
                        </div>
                        <div>
                            <h4 class="font-headline font-bold text-xs text-secondary-900">Pengiriman Sameday</h4>
                            <p class="text-[11px] text-slate-500">Order sebelum 14.00 WIB langsung berangkat</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 1: Cek Estimasi Ongkir Cepat & Tabel Zonasi -->
        <section class="py-14 bg-[#F8FAFC] border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <span class="text-primary font-bold text-xs uppercase tracking-widest font-headline">
                        AREA LAYANAN & ESTIMASI BIAYA
                    </span>
                    <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 mt-1">
                        Zonasi Wilayah & Skema Tarif Ongkir
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2">
                        Tarif pengiriman dihitung adil berdasarkan zona jarak tempuh dari pangkalan gudang pusat kami di Bandar Lampung.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Mini Simulator Cek Ongkir Kecamatan -->
                    <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-5">
                            <MapPin class="w-5 h-5 text-primary" />
                            <h3 class="font-headline font-bold text-base text-secondary-900">
                                Simulasi Cek Ongkir Wilayah
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-secondary-800 mb-1.5">
                                    Pilih Kecamatan / Wilayah Proyek Anda:
                                </label>
                                <select
                                    v-model="selectedDistrict"
                                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-secondary-900"
                                >
                                    <option value="">-- Pilih Wilayah / Kecamatan --</option>
                                    <option v-for="item in districts" :key="item.name" :value="item.name">
                                        {{ item.name }} ({{ item.distance }})
                                    </option>
                                </select>
                            </div>

                            <!-- Result Box -->
                            <div v-if="selectedDistrictData" class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                                    <span class="text-xs text-slate-500">Klasifikasi Zona:</span>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-primary-100 text-primary-800">
                                        {{ selectedDistrictData.zone }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-600">Estimasi Jarak Gudang:</span>
                                    <span class="font-bold text-secondary-900">{{ selectedDistrictData.distance }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-600">Estimasi Ongkir Pickup (s/d 1.5 Ton):</span>
                                    <span class="font-bold text-secondary-900">{{ formatRupiah(selectedDistrictData.pickup) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-600">Estimasi Ongkir Truk Engkel (s/d 4 Ton):</span>
                                    <span class="font-bold text-secondary-900">{{ formatRupiah(selectedDistrictData.engkel) }}</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-tertiary-50 border border-tertiary-200/60 text-[11px] text-tertiary-800 font-medium flex items-start gap-2">
                                    <BadgePercent class="w-4 h-4 shrink-0 text-tertiary-600 mt-0.5" />
                                    <span>
                                        <strong>Gratis Ongkir</strong> berlaku untuk pembelanjaan proyek mulai dari <strong>{{ formatRupiah(selectedDistrictData.freeMin) }}</strong>!
                                    </span>
                                </div>

                                <a
                                    :href="`https://wa.me/${setting?.store_whatsapp || '6281234567890'}?text=${encodeURIComponent(`Halo Admin MATERIALKU, saya mau konfirmasi pengiriman material ke daerah ${selectedDistrictData.name}. Apakah jadwal armada hari ini masih tersedia?`)}`"
                                    target="_blank"
                                    class="w-full mt-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-primary-600 text-white rounded-xl text-xs font-bold transition shadow-sm"
                                >
                                    <span>Konfirmasi Alamat via WhatsApp</span>
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </a>
                            </div>

                            <div v-else class="text-center py-6 text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                                Silakan pilih wilayah di atas untuk melihat estimasi ongkos kirim dan rekomendasi kendaraan.
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Zonasi Wilayah Lengkap -->
                    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-headline font-bold text-base text-secondary-900">
                                    Daftar Skema Zonasi Wilayah Lampung
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Berlaku untuk seluruh kategori material bahan bangunan</p>
                            </div>
                            <span class="text-[11px] font-bold px-2.5 py-1 bg-slate-100 text-secondary-800 rounded-lg">
                                Update Tarif 2026
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-headline font-bold">
                                    <tr>
                                        <th class="p-3.5">Zona</th>
                                        <th class="p-3.5">Radius</th>
                                        <th class="p-3.5">Area Cakupan</th>
                                        <th class="p-3.5">Tarif Armada</th>
                                        <th class="p-3.5">Syarat Free Ongkir</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="p-3.5 font-bold text-primary">Zona 1</td>
                                        <td class="p-3.5 font-medium">0 - 5 km</td>
                                        <td class="p-3.5">Tanjung Karang, Kedaton, Way Halim, Sukarame, Enggal</td>
                                        <td class="p-3.5">
                                            <div class="font-bold text-secondary-900">Rp 35.000</div>
                                            <div class="text-[10px] text-slate-500">Pickup Bak</div>
                                        </td>
                                        <td class="p-3.5 text-tertiary-700 font-bold">Min. Rp 1.500.000</td>
                                    </tr>
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="p-3.5 font-bold text-primary">Zona 2</td>
                                        <td class="p-3.5 font-medium">5 - 15 km</td>
                                        <td class="p-3.5">Rajabasa, Kemiling, Teluk Betung, Jati Agung (ITERA), Natar Selatan</td>
                                        <td class="p-3.5">
                                            <div class="font-bold text-secondary-900">Rp 50.000 - Rp 95.000</div>
                                            <div class="text-[10px] text-slate-500">Pickup / Engkel</div>
                                        </td>
                                        <td class="p-3.5 text-tertiary-700 font-bold">Min. Rp 2.500.000</td>
                                    </tr>
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="p-3.5 font-bold text-primary">Zona 3</td>
                                        <td class="p-3.5 font-medium">15 - 25 km</td>
                                        <td class="p-3.5">Natar Utara (Bandara), Tanjung Bintang, Gedong Tataan (Pesawaran), Katibung</td>
                                        <td class="p-3.5">
                                            <div class="font-bold text-secondary-900">Rp 100.000 - Rp 180.000</div>
                                            <div class="text-[10px] text-slate-500">Tergantung tonase</div>
                                        </td>
                                        <td class="p-3.5 text-tertiary-700 font-bold">Min. Rp 4.000.000</td>
                                    </tr>
                                    <tr class="hover:bg-slate-50/80 transition bg-slate-50/50">
                                        <td class="p-3.5 font-bold text-secondary-900">Luar Zona</td>
                                        <td class="p-3.5 font-medium">> 25 km</td>
                                        <td class="p-3.5">Kota Metro, Pringsewu, Kalianda, Kotabumi, Lampung Tengah</td>
                                        <td class="p-3.5">
                                            <div class="font-bold text-primary">Nego Tarif Khusus</div>
                                            <div class="text-[10px] text-slate-500">Sesuai jarak riil</div>
                                        </td>
                                        <td class="p-3.5 text-slate-500 font-medium">Order Partai Besar Proyek</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: Pilihan Armada Mandiri Toko & Akses Jalan -->
        <section class="py-16 bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
                    <div>
                        <span class="text-primary font-bold text-xs uppercase tracking-widest font-headline">
                            SPESIFIKASI ARMADA & AKSES JALAN
                        </span>
                        <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 mt-1">
                            Pilihan Kendaraan Pengantar Material
                        </h2>
                    </div>
                    <p class="text-xs text-slate-500 max-w-md">
                        Kendaraan toko disesuaikan secara cerdas berdasarkan total berat material dan kondisi akses jalan menuju lokasi rumah/proyek Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Armada 1: Pickup -->
                    <div class="p-6 rounded-2xl bg-white border-2 border-primary/30 shadow-md flex flex-col justify-between relative">
                        <span class="absolute -top-3 right-6 bg-primary text-white text-[10px] font-extrabold px-3 py-0.5 rounded-full uppercase tracking-wider">
                            Paling Lincah Masuk Gang
                        </span>
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-primary text-white flex items-center justify-center">
                                    <Truck class="w-6 h-6" />
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-md bg-primary-50 text-primary-800">
                                    Kapasitas: 1.5 Ton
                                </span>
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1">
                                Pickup Bak Terbuka
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Solusi terbaik untuk kebutuhan renovasi mendesak, bahan cairan, atau rumah di area jalan padat penduduk.
                            </p>
                            <div class="space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-3">
                                <div class="font-semibold text-secondary-800">Muatan yang Cocok:</div>
                                <ul class="space-y-1 text-slate-500 text-[11px]">
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Cat tembok 5L - 20L, waterproofing</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Semen s/d 20 sak (1.000 kg)</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Keramik s/d 40 dus</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Pipa PVC, kran sanitari, & perkakas</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-primary-800 font-bold flex items-center gap-1.5">
                            <CheckCircle2 class="w-4 h-4 text-primary shrink-0" />
                            <span>Akses jalan lebar minimal 2.5 meter & bebas portal</span>
                        </div>
                    </div>

                    <!-- Armada 2: Truk Engkel -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary flex items-center justify-center">
                                    <Truck class="w-6 h-6" />
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-md bg-slate-100 text-secondary-800">
                                    Kapasitas: 4 Ton
                                </span>
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1">
                                Truk Engkel 4 Roda
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Paling populer untuk renovasi rumah total, pengiriman pasir curah, bata ringan hebel, dan puluhan sak semen.
                            </p>
                            <div class="space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-3">
                                <div class="font-semibold text-secondary-800">Muatan yang Cocok:</div>
                                <ul class="space-y-1 text-slate-500 text-[11px]">
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Semen PCC s/d 80 sak</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Keramik lantai s/d 120 dus</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Bata ringan hebel 4 - 6 kubik (palet)</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Pasir cor & batu split 1 colt bak</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-secondary-800 font-bold flex items-center gap-1.5">
                            <CheckCircle2 class="w-4 h-4 text-tertiary-600 shrink-0" />
                            <span>Jalan perumahan lebar minimal 3.5 meter</span>
                        </div>
                    </div>

                    <!-- Armada 3: Truk Tronton / CDD -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-secondary-800 flex items-center justify-center">
                                    <Truck class="w-6 h-6" />
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-md bg-slate-100 text-secondary-800">
                                    Kapasitas: 10 Ton
                                </span>
                            </div>
                            <h3 class="font-headline font-bold text-base text-secondary-900 mb-1">
                                Truk Cold Diesel / Tronton
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Dikhususkan untuk suplai proyek konstruksi gedung, ruko, perumahan cluster, serta pengiriman besi batangan panjang.
                            </p>
                            <div class="space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-3">
                                <div class="font-semibold text-secondary-800">Muatan yang Cocok:</div>
                                <ul class="space-y-1 text-slate-500 text-[11px]">
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Besi beton ulir 12m utuh (tanpa ditekuk)</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Baja ringan kanal C 6 meter utuh</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Semen sak grosir 200 - 250 sak</li>
                                    <li class="flex items-center gap-1.5"><Check class="w-3.5 h-3.5 text-tertiary-600 shrink-0" /> Wiremesh & bondek cor dak</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-600 font-bold flex items-center gap-1.5">
                            <CheckCircle2 class="w-4 h-4 text-slate-400 shrink-0" />
                            <span>Jalan utama / boulevard proyek minimal 5 meter</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: SLA Jadwal Pengiriman & Kebijakan Bongkar Muat -->
        <section class="py-16 bg-[#F8FAFC] border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Card 1: Jadwal & SLA -->
                    <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary flex items-center justify-center shrink-0">
                                <Clock class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-headline font-bold text-lg text-secondary-900">
                                    Jadwal Kirim & Jam Operasional
                                </h3>
                                <p class="text-xs text-slate-500">Ketepatan waktu untuk mendukung jadwal tukang bangunan</p>
                            </div>
                        </div>

                        <div class="space-y-3.5 text-xs text-slate-600">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                                <span class="px-2 py-0.5 rounded bg-primary text-white font-extrabold text-[10px] shrink-0 mt-0.5">SAMEDAY</span>
                                <div>
                                    <p class="font-bold text-secondary-900">Batas Order Sameday (Pukul 14.00 WIB)</p>
                                    <p class="text-slate-500 mt-0.5">Pesanan yang diverifikasi sebelum pukul 14.00 WIB dikirim pada hari yang sama.</p>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                                <span class="px-2 py-0.5 rounded bg-slate-800 text-white font-extrabold text-[10px] shrink-0 mt-0.5">BOOKING</span>
                                <div>
                                    <p class="font-bold text-secondary-900">Jadwal Kirim Terencana (Booking Hari & Jam)</p>
                                    <p class="text-slate-500 mt-0.5">Anda dapat menentukan tanggal dan estimasi jam tiba agar mandor proyek siap menyambut.</p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-100">
                                <p class="font-bold text-secondary-900 mb-1">Jam Operasional Truk & Pengantaran:</p>
                                <ul class="space-y-1 text-slate-500">
                                    <li>• <strong>Senin - Sabtu:</strong> 08.00 - 17.00 WIB</li>
                                    <li>• <strong>Minggu:</strong> 08.30 - 14.00 WIB (Khusus pesanan terkonfirmasi H-1)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Aturan Bongkar Muat (Unloading Policy) -->
                    <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-tertiary-50 text-tertiary-700 flex items-center justify-center shrink-0">
                                <Package class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-headline font-bold text-lg text-secondary-900">
                                    Ketentuan Bongkar Muat Barang
                                </h3>
                                <p class="text-xs text-slate-500">Aturan penurunan semen, keramik, pasir, dan cat</p>
                            </div>
                        </div>

                        <div class="space-y-3.5 text-xs text-slate-600">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                <p class="font-bold text-secondary-900 mb-1">1. Titik Standar Penurunan Material</p>
                                <p class="text-slate-500 leading-relaxed">
                                    Barang diturunkan di titik terdekat yang dapat diakses kendaraan (depan gerbang/pagar rumah, garasi/carport, atau bibir jalan proyek).
                                </p>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                <p class="font-bold text-secondary-900 mb-1">2. Panggul Naik ke Lantai 2 / Masuk Gang Jauh</p>
                                <p class="text-slate-500 leading-relaxed">
                                    Jika material harus digotong naik ke lantai 2 atau dipanggul masuk gang lebih dari 15 meter, mohon konfirmasi terlebih dahulu dengan tim admin agar diatur bantuan kenek panggul tambahan.
                                </p>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                <p class="font-bold text-secondary-900 mb-1">3. Kesiapan Izin & Akses Lokasi</p>
                                <p class="text-slate-500 leading-relaxed">
                                    Pembeli diharapkan memastikan portal perumahan dapat dibuka dan ijin keamanan lingkungan setempat telah siap saat truk tiba.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 4: Garansi Kerusakan & Retur Pengiriman -->
        <section class="py-14 bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-secondary-900 to-secondary-800 rounded-3xl p-8 sm:p-10 text-white shadow-xl">
                    <div class="max-w-3xl">
                        <span class="bg-primary text-white text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            JAMINAN KEPUASAN BELANJA
                        </span>
                        <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-white mt-3">
                            Garansi Keutuhan Barang Saat Tiba
                        </h2>
                        <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                            Kami memahami risiko material bangunan seperti keramik yang bisa retak atau sak semen yang rentan robek. Toko kami menerapkan SOP pemeriksaan langsung bersama supir:
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15">
                                <div class="text-primary-300 font-extrabold text-lg mb-1">01. Periksa</div>
                                <p class="text-xs text-slate-200">Cek kondisi fisik barang bersama supir sebelum menandatangani nota serah terima.</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15">
                                <div class="text-primary-300 font-extrabold text-lg mb-1">02. Catat</div>
                                <p class="text-xs text-slate-200">Bila ada dus keramik pecah atau sak semen sobek, supir langsung menandai di nota retur.</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15">
                                <div class="text-primary-300 font-extrabold text-lg mb-1">03. Ganti</div>
                                <p class="text-xs text-slate-200">Toko langsung mengirimkan barang pengganti baru pada trip armada berikutnya gratis!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 5: FAQ Interaktif Accordion -->
        <section class="py-16 bg-[#F8FAFC] border-b border-slate-200">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-primary font-bold text-xs uppercase tracking-widest font-headline">
                        PERTANYAAN UMUM
                    </span>
                    <h2 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900 mt-1">
                        Frequently Asked Questions (FAQ) Pengiriman
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-2">
                        Jawaban atas pertanyaan seputar armada, waktu pengantaran, dan perlindungan material bangunan Anda.
                    </p>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="faq in faqs"
                        :key="faq.id"
                        class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-xs transition"
                    >
                        <button
                            @click="toggleFaq(faq.id)"
                            class="w-full p-5 text-left flex items-center justify-between gap-4 hover:bg-slate-50 transition"
                        >
                            <span class="font-headline font-bold text-sm sm:text-base text-secondary-900">
                                {{ faq.question }}
                            </span>
                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                                <ChevronUp v-if="activeFaq === faq.id" class="w-4 h-4 text-primary" />
                                <ChevronDown v-else class="w-4 h-4" />
                            </div>
                        </button>
                        <div
                            v-if="activeFaq === faq.id"
                            class="px-5 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50"
                        >
                            {{ faq.answer }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 6: CTA Bottom Banner -->
        <section class="py-14 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="p-8 sm:p-10 rounded-3xl bg-primary-50 border border-primary/20 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="space-y-2">
                        <span class="text-primary font-bold text-xs uppercase tracking-wider">
                            BUTUH PENGIRIMAN VOLUME BESAR ATAU LOKASI KHUSUS?
                        </span>
                        <h3 class="font-headline font-extrabold text-xl sm:text-2xl text-secondary-900">
                            Konsultasikan Rute & Jadwal Pengiriman dengan Tim Logistik Kami
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 max-w-xl">
                            Hubungi koordinator armada kami via WhatsApp untuk menanyakan ketersediaan armada, jadwal rute hari ini, atau negosiasi ongkos kirim proyek.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 shrink-0 w-full sm:w-auto">
                        <a
                            :href="`https://wa.me/${setting?.store_whatsapp || '6281234567890'}?text=${encodeURIComponent('Halo Tim Logistik MATERIALKU, saya mau konsultasi jadwal rute pengiriman dan cek ongkir untuk lokasi proyek saya...')}`"
                            target="_blank"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-primary hover:bg-primary-600 text-white rounded-xl text-xs sm:text-sm font-bold transition shadow-sm"
                        >
                            <Phone class="w-4 h-4" />
                            <span>Chat Logistik via WhatsApp</span>
                        </a>
                        <Link
                            :href="route('catalog.index')"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white border border-slate-300 hover:border-primary text-secondary-900 hover:text-primary rounded-xl text-xs sm:text-sm font-bold transition shadow-xs"
                        >
                            <Package class="w-4 h-4" />
                            <span>Mulai Belanja di Katalog</span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
