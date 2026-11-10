import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useCartStore = defineStore('cart', () => {
    // Load initial cart from localStorage
    const saved = localStorage.getItem('materialku_cart');
    const items = ref(saved ? JSON.parse(saved) : []);

    const saveToStorage = () => {
        localStorage.setItem('materialku_cart', JSON.stringify(items.value));
    };

    const totalCount = computed(() => {
        return items.value.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0);
    });

    const totalPrice = computed(() => {
        return items.value.reduce((sum, item) => sum + ((Number(item.price) || 0) * (Number(item.quantity) || 0)), 0);
    });

    const addItem = (product, quantity = 1, calculationSnapshot = null) => {
        const qty = parseInt(quantity, 10) || 1;
        const existingIndex = items.value.findIndex((i) => i.id === product.id);

        if (existingIndex > -1) {
            items.value[existingIndex].quantity += qty;
            if (calculationSnapshot) {
                items.value[existingIndex].calculation_snapshot = calculationSnapshot;
            }
        } else {
            items.value.push({
                id: product.id,
                name: product.name,
                slug: product.slug,
                price: parseFloat(product.price),
                sale_unit: product.sale_unit,
                image_path: product.image_path,
                quantity: qty,
                calculation_snapshot: calculationSnapshot,
            });
        }
        saveToStorage();
    };

    const updateQuantity = (id, newQty) => {
        const qty = parseInt(newQty, 10);
        const item = items.value.find((i) => i.id === id);
        if (item) {
            if (qty <= 0) {
                removeItem(id);
            } else {
                item.quantity = qty;
                saveToStorage();
            }
        }
    };

    const removeItem = (id) => {
        items.value = items.value.filter((i) => i.id !== id);
        saveToStorage();
    };

    const clearCart = () => {
        items.value = [];
        saveToStorage();
    };

    return {
        items,
        totalCount,
        totalPrice,
        addItem,
        updateQuantity,
        removeItem,
        clearCart,
    };
});
