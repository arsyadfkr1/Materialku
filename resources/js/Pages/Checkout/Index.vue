<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useCartStore } from '@/Stores/cartStore';
import {
    ShoppingCart,
    Truck,
    ShieldCheck,
    MessageSquare,
    AlertCircle,
    ArrowRight,
    Trash2,
    Plus,
    Minus,
    Calculator
} from 'lucide-vue-next';

const props = defineProps({
    setting: Object,
});

const cart = useCartStore();

// Generate unique idempotency key for this checkout attempt
const generateUUID = () => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = (Math.random() * 16) | 0,
            v = c === 'x' ? r : (r & 0x3) | 0x8;
        return v.toString(16);
    });
};

const form = useForm({
    customer_name: '',
    whatsapp: '',
    address: '',
    customer_note: '',
    idempotency_key: generateUUID(),
    items: [],
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const submitCheckout = () => {
    if (cart.items.length === 0) {
        alert('Keranjang Anda kosong.');
        return;
    }

    form.items = cart.items.map((item) => ({
        material_id: item.id,
        quantity: item.quantity,
        calculation_snapshot: item.calculation_snapshot || null,
    }));

    form.post(route('checkout.store'), {
        onSuccess: () => {
            // Clear cart from client storage upon successful order creation
            cart.clearCart();
        },
    });
};
</script>

<template>
    <PublicLayout :setting="setting">
        <Head title="Checkout Pesanan Tamu - MATERIALKU" />

        <div class="bg-white border-b border-slate-200 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <div class="flex items-center gap-2 text-xs font-bold text-primary uppercase tracking-wider mb-1">
                        <Link :href="route('home')" class="hover:underline">Beranda</Link>
                        <span>/</span>
                        <span>Checkout</span>
                    </div>
                    <h1 class="font-headline font-extrabold text-2xl sm:text-3xl text-secondary-900">
                        Checkout Pesanan Tamu
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Tanpa perlu membuat akun. Cukup isi nama, nomor WhatsApp aktif, dan alamat pengantaran armada.
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Empty Cart State -->
            <div v-if="cart.items.length === 0" class="bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-xl mx-auto space-y-4">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                    <ShoppingCart class="w-8 h-8" />
                </div>
                <h3 class="font-headline font-bold text-lg text-secondary-900">
                    Keranjang Belanja Kosong
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Anda belum memilih material apapun untuk dipesan. Silakan kunjungi katalog atau gunakan kalkulator material terlebih dahulu.
                </p>
                <div class="pt-2">
                    <Link
                        :href="route('catalog.index')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-headline font-bold text-xs rounded-xl hover:bg-primary-600 transition"
                    >
                        <span>Buka Katalog Material</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>

            <!-- Checkout Form & Items Review -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Customer Form -->
                <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-xl bg-primary-50 text-primary flex items-center justify-center">
                            <Truck class="w-4 h-4" />
                        </div>
                        <div>
                            <h2 class="font-headline font-bold text-base text-secondary-900">
                                Data Penerima & Pengantaran Armada
                            </h2>
                            <p class="text-xs text-slate-400">Pastikan nomor WhatsApp aktif untuk konfirmasi toko</p>
                        </div>
                    </div>

                    <!-- General Error Alert -->
                    <div v-if="form.errors.checkout_error" class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs flex items-center gap-2">
                        <AlertCircle class="w-4 h-4 shrink-0" />
                        <span>{{ form.errors.checkout_error }}</span>
                    </div>

                    <form @submit.prevent="submitCheckout" class="space-y-4">
                        <!-- Customer Name -->
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Nama Lengkap Penerima <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.customer_name"
                                placeholder="Contoh: Budi Santoso / Pak Hendra"
                                class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20 py-2.5"
                                required
                            />
                            <p v-if="form.errors.customer_name" class="text-red-500 text-[11px] mt-1">
                                {{ form.errors.customer_name }}
                            </p>
                        </div>

                        <!-- WhatsApp Number -->
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Nomor WhatsApp Aktif <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    v-model="form.whatsapp"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20 py-2.5"
                                    required
                                />
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Tautan WhatsApp dan konfirmasi pesanan akan diarahkan ke nomor ini.
                            </p>
                            <p v-if="form.errors.whatsapp" class="text-red-500 text-[11px] mt-1">
                                {{ form.errors.whatsapp }}
                            </p>
                        </div>

                        <!-- Full Delivery Address -->
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Alamat Lengkap Pengiriman Armada Toko <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="form.address"
                                rows="3"
                                placeholder="Tuliskan nama jalan, nomor rumah/bangunan, patokan dekat, kelurahan, dan kota..."
                                class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20 p-2.5"
                                required
                            ></textarea>
                            <p v-if="form.errors.address" class="text-red-500 text-[11px] mt-1">
                                {{ form.errors.address }}
                            </p>
                        </div>

                        <!-- Customer Note -->
                        <div>
                            <label class="block font-headline font-bold text-xs text-secondary-900 mb-1">
                                Catatan Khusus untuk Toko & Sopir (Opsional)
                            </label>
                            <textarea
                                v-model="form.customer_note"
                                rows="2"
                                placeholder="Contoh: Tolong kirim pagi sebelum jam 10. Jalan agak sempit bisa pakai pickup."
                                class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-primary focus:ring-primary/20 p-2.5"
                            ></textarea>
                        </div>

                        <!-- Security Notice -->
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-start gap-2.5 text-xs text-slate-600">
                            <ShieldCheck class="w-4 h-4 text-tertiary-600 shrink-0 mt-0.5" />
                            <div>
                                <span class="font-bold text-secondary-900">Alur Pesanan Aman:</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Setelah menekan tombol di samping, pesanan Anda akan tersimpan di database toko dengan kode pesanan unik. Anda akan diarahkan ke ringkasan dan tombol resmi WhatsApp.
                                </p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right: Order Items & Submit -->
                <div class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border-2 border-slate-200 shadow-md space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <h3 class="font-headline font-bold text-base text-secondary-900">
                            Item Belanja ({{ cart.totalCount }})
                        </h3>
                        <Link :href="route('catalog.index')" class="text-xs font-bold text-primary hover:underline">
                            + Tambah Produk
                        </Link>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        <div
                            v-for="item in cart.items"
                            :key="item.id"
                            class="p-3 rounded-xl border border-slate-100 bg-slate-50 flex items-start justify-between gap-3 text-xs"
                        >
                            <div class="space-y-1">
                                <h4 class="font-bold text-secondary-900 leading-snug">
                                    {{ item.name }}
                                </h4>
                                <div class="text-slate-500 text-[11px]">
                                    {{ item.quantity }} {{ item.sale_unit }} × {{ formatRupiah(item.price) }}
                                </div>
                                <div v-if="item.calculation_snapshot" class="text-[10px] text-primary font-semibold">
                                    💡 Dari kalkulator proyek
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="font-bold text-secondary-900 block">
                                    {{ formatRupiah(item.price * item.quantity) }}
                                </span>
                                <button
                                    @click="cart.removeItem(item.id)"
                                    class="text-slate-400 hover:text-red-500 text-[11px] transition mt-1"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Price Calculations -->
                    <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Bahan Material:</span>
                            <span class="font-bold text-secondary-900">{{ formatRupiah(cart.totalPrice) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Ongkir Armada Toko:</span>
                            <span class="text-primary font-bold">Dikonfirmasi via WA</span>
                        </div>
                        <div class="pt-3 border-t border-slate-200 flex justify-between items-baseline">
                            <span class="font-headline font-bold text-sm text-secondary-900">Total Material:</span>
                            <span class="font-headline font-extrabold text-2xl text-primary">
                                {{ formatRupiah(cart.totalPrice) }}
                            </span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        @click="submitCheckout"
                        :disabled="form.processing"
                        type="button"
                        class="w-full flex items-center justify-center gap-2 py-4 bg-primary hover:bg-primary-600 text-white font-headline font-bold text-sm rounded-xl shadow-lg shadow-primary/25 transition disabled:opacity-50"
                    >
                        <MessageSquare class="w-4 h-4" />
                        <span v-if="form.processing">Menyimpan Pesanan...</span>
                        <span v-else>Simpan & Lanjutkan ke WhatsApp</span>
                    </button>

                    <p class="text-[11px] text-center text-slate-400 italic">
                        *Pembayaran manual setelah konfirmasi kesepakatan ongkir dengan admin toko.
                    </p>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
