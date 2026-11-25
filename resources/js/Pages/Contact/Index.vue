<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
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
    Instagram,
    Facebook,
} from 'lucide-vue-next';

const props = defineProps({
    setting: Object,
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
    <Head title="Hubungi Toko – MATERIALKU" />
    <PublicLayout>
        <!-- ===== HERO SECTION ===== -->
        <section class="bg-gradient-to-br from-orange-50 via-white to-slate-50 py-16 border-b border-slate-100">
            <div class="max-w-5xl mx-auto px-4 text-center">
                <span class="inline-block text-primary font-semibold text-sm uppercase tracking-widest mb-3">
                    Kami Siap Membantu
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-4">
                    Hubungi Toko Kami
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
                        <h3 class="font-bold text-slate-800 text-lg mb-1">Alamat Toko</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">
                            {{ setting?.store_address ?? 'Jl. Soekarno-Hatta No. 88, By Pass, Kota Bandar Lampung' }}
                        </p>
                    </div>
                    <a
                        href="https://maps.google.com/?q=Jl.+Soekarno-Hatta+No.+88+Bandar+Lampung"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 text-primary text-sm font-semibold hover:underline mt-auto"
                    >
                        Lihat di Google Maps
                        <ArrowRight class="w-4 h-4" />
                    </a>
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
                                    <p class="text-xs text-slate-500 leading-none mb-1">WhatsApp</p>
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

        <!-- ===== GOOGLE MAPS EMBED ===== -->
        <section class="max-w-5xl mx-auto px-4 pb-14">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-7 pt-7 pb-4">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <MapPin class="w-5 h-5 text-primary" />
                        Lokasi Toko
                    </h2>
                    <p class="text-slate-500 text-sm mt-1">
                        {{ setting?.store_address ?? 'Jl. Soekarno-Hatta No. 88, By Pass, Kota Bandar Lampung' }}
                    </p>
                </div>
                <div class="relative w-full h-72 md:h-96">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63436.49055984248!2d105.19003!3d-5.38966!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40db4f70f4aef5%3A0x82cb5a5e4bb49a9f!2sBandar%20Lampung!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="absolute inset-0"
                    ></iframe>
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
                        class="w-full flex items-center gap-4 px-6 py-5 text-left hover:bg-slate-50 transition"
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
