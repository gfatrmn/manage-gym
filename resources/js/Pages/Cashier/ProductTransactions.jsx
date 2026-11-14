import React, { useMemo, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Minus, Plus, Printer, Search, ShoppingBag, ShoppingCart, Trash2 } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    Input,
    InputLabel,
    Select,
    Textarea,
} from '../../Components/UI';
import { formatRupiah } from '../../lib/format';

export default function ProductTransactions({
    products = [],
    productTransactions = [],
    members = [],
    selectedMember = null,
    selectedCustomerName = '',
    selectedMonth = '',
}) {
    const [search, setSearch] = useState('');
    const [monthFilter, setMonthFilter] = useState(selectedMonth || '');
    const [cart, setCart] = useState({});
    const [gymMemberId, setGymMemberId] = useState(selectedMember?.id ? String(selectedMember.id) : '');
    const [customerName, setCustomerName] = useState(selectedCustomerName || '');
    const [paymentMethod, setPaymentMethod] = useState('cash');
    const [paidAmount, setPaidAmount] = useState('');
    const [notes, setNotes] = useState('');
    const [processing, setProcessing] = useState(false);

    const filteredProducts = useMemo(() => {
        const q = search.trim().toLowerCase();
        if (!q) return products;
        return products.filter(
            (p) =>
                p.name.toLowerCase().includes(q) ||
                (p.category || '').toLowerCase().includes(q) ||
                (p.brand || '').toLowerCase().includes(q)
        );
    }, [products, search]);

    const addToCart = (product) => {
        if (!product.is_active || product.stock <= 0) return;
        setCart((prev) => {
            const current = prev[product.id] || 0;
            const next = Math.min(product.stock, current + 1);
            return { ...prev, [product.id]: next };
        });
    };

    const updateQty = (productId, nextQty, maxStock) => {
        setCart((prev) => {
            if (nextQty <= 0) {
                const clone = { ...prev };
                delete clone[productId];
                return clone;
            }
            return { ...prev, [productId]: Math.min(maxStock, nextQty) };
        });
    };

    const cartItems = useMemo(() => {
        return Object.entries(cart)
            .map(([id, qty]) => {
                const product = products.find((p) => p.id === Number(id));
                if (!product) return null;
                return { ...product, qty, subtotal: product.price * qty };
            })
            .filter(Boolean);
    }, [cart, products]);

    const totalAmount = cartItems.reduce((sum, item) => sum + item.subtotal, 0);
    const effectivePaid = paymentMethod === 'qris' ? totalAmount : Number(paidAmount || 0);
    const change = paymentMethod === 'cash' ? Math.max(0, effectivePaid - totalAmount) : 0;

    const handleMemberSelect = (val) => {
        setGymMemberId(val);
        if (val) {
            const m = members.find((item) => String(item.id) === val);
            if (m) setCustomerName(m.full_name);
        }
    };

    const submitCheckout = (e) => {
        e.preventDefault();
        if (cartItems.length === 0) return;

        const quantities = {};
        const product_ids = [];
        cartItems.forEach((item) => {
            product_ids.push(item.id);
            quantities[item.id] = item.qty;
        });

        setProcessing(true);
        router.post(
            '/cashier/transactions',
            {
                transaction_group: 'product_sale',
                gym_member_id: gymMemberId || null,
                customer_name: customerName,
                payment_method: paymentMethod,
                paid_amount: effectivePaid,
                product_ids,
                quantities,
                notes,
            },
            {
                onFinish: () => setProcessing(false),
                onSuccess: () => {
                    setCart({});
                    setPaidAmount('');
                    setNotes('');
                },
            }
        );
    };

    const filterMonth = (m) => {
        setMonthFilter(m);
        router.get('/cashier/transactions/products', { month: m }, { preserveState: true });
    };

    return (
        <AppLayout
            title="POS Penjualan Produk"
            subtitle="Checkout multi-item suplemen, minuman, & perlengkapan dengan validasi stok otomatis"
            panel="cashier"
            active="product-transactions"
        >
            <div className="grid gap-6 lg:grid-cols-3">
                {/* Product Catalog */}
                <Card className="lg:col-span-2">
                    <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 className="flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                            <ShoppingBag className="h-4 w-4 text-rose-500" />
                            Katalog Produk
                        </h2>
                        <div className="relative w-full sm:w-64">
                            <Search className="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400" />
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Cari produk / kategori..."
                                className="pl-9"
                            />
                        </div>
                    </div>

                    {filteredProducts.length === 0 ? (
                        <EmptyState icon={ShoppingBag} title="Produk tidak ditemukan" />
                    ) : (
                        <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                            {filteredProducts.map((p) => {
                                const inCart = cart[p.id] || 0;
                                const unavailable = !p.is_active || p.stock <= 0;
                                return (
                                    <div
                                        key={p.id}
                                        className={`flex flex-col justify-between rounded-2xl border p-4 transition ${
                                            inCart > 0
                                                ? 'border-rose-500 bg-rose-500/5'
                                                : 'border-slate-200 dark:border-slate-800'
                                        } ${unavailable ? 'opacity-50' : ''}`}
                                    >
                                        <div>
                                            <div className="flex items-start justify-between gap-2">
                                                <Badge variant="info">{p.category}</Badge>
                                                <Badge variant={p.stock <= 5 ? 'warning' : 'neutral'}>
                                                    Stok: {p.stock}
                                                </Badge>
                                            </div>
                                            <h3 className="mt-2 font-bold text-slate-900 dark:text-white">
                                                {p.name}
                                            </h3>
                                            <p className="text-xs text-slate-500">{p.brand || p.unit}</p>
                                        </div>

                                        <div className="mt-4 flex items-center justify-between gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                                            <span className="text-sm font-extrabold text-rose-600 dark:text-rose-400">
                                                {p.price_formatted}
                                            </span>
                                            <Button
                                                size="xs"
                                                disabled={unavailable || inCart >= p.stock}
                                                onClick={() => addToCart(p)}
                                            >
                                                <Plus className="h-3.5 w-3.5" />
                                                {inCart > 0 ? `(${inCart})` : 'Tambah'}
                                            </Button>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </Card>

                {/* Cart & Checkout */}
                <Card>
                    <h2 className="mb-4 flex items-center gap-2 text-base font-bold text-slate-900 dark:text-white">
                        <ShoppingCart className="h-4 w-4 text-rose-500" />
                        Keranjang Belanja ({cartItems.length})
                    </h2>

                    {cartItems.length === 0 ? (
                        <p className="rounded-xl border border-dashed border-slate-300 p-6 text-center text-xs text-slate-500 dark:border-slate-700">
                            Pilih produk di katalog untuk mulai checkout.
                        </p>
                    ) : (
                        <div className="mb-4 space-y-2.5">
                            {cartItems.map((item) => (
                                <div
                                    key={item.id}
                                    className="flex items-center justify-between gap-2 rounded-xl bg-slate-100 p-2.5 text-xs dark:bg-slate-800"
                                >
                                    <div className="min-w-0">
                                        <p className="truncate font-bold text-slate-900 dark:text-white">
                                            {item.name}
                                        </p>
                                        <p className="text-slate-500">
                                            {formatRupiah(item.price)} × {item.qty} ={' '}
                                            <strong>{formatRupiah(item.subtotal)}</strong>
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-1">
                                        <button
                                            type="button"
                                            onClick={() => updateQty(item.id, item.qty - 1, item.stock)}
                                            className="rounded-lg bg-white p-1 text-slate-700 hover:bg-slate-200 dark:bg-slate-700 dark:text-white"
                                        >
                                            <Minus className="h-3.5 w-3.5" />
                                        </button>
                                        <span className="w-6 text-center font-bold">{item.qty}</span>
                                        <button
                                            type="button"
                                            onClick={() => updateQty(item.id, item.qty + 1, item.stock)}
                                            className="rounded-lg bg-white p-1 text-slate-700 hover:bg-slate-200 dark:bg-slate-700 dark:text-white"
                                        >
                                            <Plus className="h-3.5 w-3.5" />
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => updateQty(item.id, 0, item.stock)}
                                            className="ml-1 rounded-lg p-1 text-rose-500 hover:bg-rose-500/10"
                                        >
                                            <Trash2 className="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    <form onSubmit={submitCheckout} className="space-y-3 border-t border-slate-200 pt-4 dark:border-slate-800">
                        <div>
                            <InputLabel>Member (Opsional)</InputLabel>
                            <Select value={gymMemberId} onChange={(e) => handleMemberSelect(e.target.value)}>
                                <option value="">-- Tamu Non-Member --</option>
                                {members.map((m) => (
                                    <option key={m.id} value={m.id}>
                                        {m.full_name} ({m.checkin_code})
                                    </option>
                                ))}
                            </Select>
                        </div>
                        <div>
                            <InputLabel required={!gymMemberId}>Nama Pembeli</InputLabel>
                            <Input
                                value={customerName}
                                onChange={(e) => setCustomerName(e.target.value)}
                                placeholder="Nama pembeli"
                            />
                        </div>
                        <div>
                            <InputLabel required>Metode Pembayaran</InputLabel>
                            <Select value={paymentMethod} onChange={(e) => setPaymentMethod(e.target.value)}>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                            </Select>
                        </div>

                        <div className="rounded-xl bg-slate-900 p-3 text-white dark:bg-slate-950">
                            <div className="flex justify-between text-xs text-slate-400">
                                <span>Total Checkout</span>
                                <span className="text-base font-extrabold text-white">
                                    {formatRupiah(totalAmount)}
                                </span>
                            </div>
                        </div>

                        {paymentMethod === 'cash' && (
                            <div>
                                <InputLabel required>Uang Diterima (Rp)</InputLabel>
                                <Input
                                    type="number"
                                    value={paidAmount}
                                    onChange={(e) => setPaidAmount(e.target.value)}
                                    placeholder={String(totalAmount)}
                                />
                                <p className="mt-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    Kembalian: {formatRupiah(change)}
                                </p>
                            </div>
                        )}

                        <div>
                            <InputLabel>Catatan</InputLabel>
                            <Textarea rows={2} value={notes} onChange={(e) => setNotes(e.target.value)} />
                        </div>

                        <Button
                            type="submit"
                            disabled={processing || cartItems.length === 0}
                            className="w-full"
                            size="lg"
                        >
                            {processing ? 'Memproses...' : 'Checkout Sekarang'}
                        </Button>
                    </form>
                </Card>
            </div>

            {/* Monthly Sales History */}
            <Card className="mt-6">
                <div className="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h3 className="text-base font-bold text-slate-900 dark:text-white">
                        Riwayat Penjualan Produk ({monthFilter})
                    </h3>
                    <Input
                        type="month"
                        value={monthFilter}
                        onChange={(e) => filterMonth(e.target.value)}
                        className="w-44"
                    />
                </div>
                {productTransactions.length === 0 ? (
                    <p className="py-6 text-center text-xs text-slate-500">
                        Belum ada transaksi produk pada bulan ini.
                    </p>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                    <th className="pb-3 pr-4">Invoice</th>
                                    <th className="pb-3 pr-4">Pembeli</th>
                                    <th className="pb-3 pr-4">Produk</th>
                                    <th className="pb-3 pr-4">Qty</th>
                                    <th className="pb-3 pr-4">Total</th>
                                    <th className="pb-3 pr-4">Status</th>
                                    <th className="pb-3 text-right">Struk</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {productTransactions.map((t) => (
                                    <tr key={t.id}>
                                        <td className="py-3 pr-4 font-mono text-xs font-bold">{t.invoice}</td>
                                        <td className="py-3 pr-4 font-semibold">{t.customer_name}</td>
                                        <td className="py-3 pr-4">{t.product_name}</td>
                                        <td className="py-3 pr-4">{t.quantity}</td>
                                        <td className="py-3 pr-4 font-bold">{t.amount_formatted}</td>
                                        <td className="py-3 pr-4">
                                            <Badge variant={t.payment_status === 'verified' ? 'success' : 'warning'}>
                                                {t.payment_status}
                                            </Badge>
                                        </td>
                                        <td className="py-3 text-right">
                                            <Link
                                                href={`/cashier/receipts/${t.invoice}/print`}
                                                className="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700"
                                            >
                                                <Printer className="h-3.5 w-3.5" />
                                                Cetak
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </Card>
        </AppLayout>
    );
}

