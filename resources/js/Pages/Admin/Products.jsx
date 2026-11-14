import React, { useState } from 'react';
import { router, useForm } from '@inertiajs/react';
import { Edit3, FolderPlus, Package, Plus, Tags, Trash2 } from 'lucide-react';
import AppLayout from '../../Layouts/AppLayout';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    FieldError,
    Input,
    InputLabel,
    Modal,
    Select,
    StatCard,
    Textarea,
} from '../../Components/UI';

export default function Products({ products = [], categories = [] }) {
    const [showProductModal, setShowProductModal] = useState(false);
    const [editingProduct, setEditingProduct] = useState(null);
    const [showCategoryModal, setShowCategoryModal] = useState(false);

    const productForm = useForm({
        name: '',
        category_id: categories[0]?.id || '',
        brand: '',
        sku: '',
        price: '',
        stock: 0,
        unit: 'pcs',
        description: '',
        is_active: true,
    });

    const categoryForm = useForm({
        name: '',
        description: '',
    });

    const openCreateProduct = () => {
        setEditingProduct(null);
        productForm.setData({
            name: '',
            category_id: categories[0]?.id || '',
            brand: '',
            sku: '',
            price: '',
            stock: 10,
            unit: 'pcs',
            description: '',
            is_active: true,
        });
        setShowProductModal(true);
    };

    const openEditProduct = (p) => {
        setEditingProduct(p);
        productForm.setData({
            name: p.name || '',
            category_id: p.category_id || '',
            brand: p.brand || '',
            sku: p.sku || '',
            price: p.price || '',
            stock: p.stock ?? 0,
            unit: p.unit || 'pcs',
            description: p.description || '',
            is_active: Boolean(p.is_active),
        });
        setShowProductModal(true);
    };

    const submitProduct = (e) => {
        e.preventDefault();
        if (editingProduct) {
            productForm.put(`/admin/products/${editingProduct.id}`, {
                onSuccess: () => setShowProductModal(false),
            });
        } else {
            productForm.post('/admin/products', {
                onSuccess: () => setShowProductModal(false),
            });
        }
    };

    const submitCategory = (e) => {
        e.preventDefault();
        categoryForm.post('/admin/categories', {
            onSuccess: () => categoryForm.reset(),
        });
    };

    const totalStock = products.reduce((sum, p) => sum + Number(p.stock || 0), 0);

    return (
        <AppLayout
            title="Master Produk & Kategori"
            subtitle="Kelola inventaris suplemen, minuman, dan perlengkapan fitness"
            panel="admin"
            active="products"
        >
            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <StatCard title="Total Produk" value={products.length} icon={Package} accent="rose" />
                <StatCard title="Total Stok Unit" value={totalStock} icon={Package} accent="emerald" />
                <StatCard title="Kategori Produk" value={categories.length} icon={Tags} accent="sky" />
            </div>

            <Card>
                <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <h2 className="text-base font-bold text-slate-900 dark:text-white">
                        Daftar Produk
                    </h2>
                    <div className="flex flex-wrap gap-2">
                        <Button variant="secondary" size="sm" onClick={() => setShowCategoryModal(true)}>
                            <FolderPlus className="h-4 w-4" />
                            Kelola Kategori ({categories.length})
                        </Button>
                        <Button size="sm" onClick={openCreateProduct}>
                            <Plus className="h-4 w-4" />
                            Tambah Produk
                        </Button>
                    </div>
                </div>

                {products.length === 0 ? (
                    <EmptyState icon={Package} title="Belum ada produk" />
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead>
                                <tr className="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:text-slate-400">
                                    <th className="pb-3 pr-4">Produk</th>
                                    <th className="pb-3 pr-4">Kategori</th>
                                    <th className="pb-3 pr-4">SKU</th>
                                    <th className="pb-3 pr-4">Harga</th>
                                    <th className="pb-3 pr-4">Stok</th>
                                    <th className="pb-3 pr-4">Status</th>
                                    <th className="pb-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 dark:divide-slate-800">
                                {products.map((p) => (
                                    <tr key={p.id}>
                                        <td className="py-3 pr-4">
                                            <p className="font-semibold text-slate-900 dark:text-white">{p.name}</p>
                                            <p className="text-xs text-slate-500 dark:text-slate-400">{p.brand || '-'}</p>
                                        </td>
                                        <td className="py-3 pr-4">
                                            <Badge variant="info">{p.category_name}</Badge>
                                        </td>
                                        <td className="py-3 pr-4 font-mono text-xs">{p.sku || '-'}</td>
                                        <td className="py-3 pr-4 font-semibold">{p.price_formatted}</td>
                                        <td className="py-3 pr-4">
                                            <Badge variant={p.stock <= 5 ? 'warning' : 'neutral'}>
                                                {p.stock} {p.unit}
                                            </Badge>
                                        </td>
                                        <td className="py-3 pr-4">
                                            <Badge variant={p.is_active ? 'success' : 'danger'}>
                                                {p.is_active ? 'Aktif' : 'Nonaktif'}
                                            </Badge>
                                        </td>
                                        <td className="py-3 text-right">
                                            <div className="inline-flex gap-1">
                                                <Button variant="ghost" size="xs" onClick={() => openEditProduct(p)}>
                                                    <Edit3 className="h-4 w-4 text-sky-600" />
                                                </Button>
                                                <Button
                                                    variant="ghost"
                                                    size="xs"
                                                    onClick={() => {
                                                        if (confirm(`Hapus produk "${p.name}"?`)) {
                                                            router.delete(`/admin/products/${p.id}`);
                                                        }
                                                    }}
                                                >
                                                    <Trash2 className="h-4 w-4 text-rose-600" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </Card>

            {/* Product Modal */}
            <Modal
                open={showProductModal}
                onClose={() => setShowProductModal(false)}
                title={editingProduct ? 'Edit Produk' : 'Tambah Produk Baru'}
            >
                <form onSubmit={submitProduct} className="space-y-4">
                    <div>
                        <InputLabel required>Nama Produk</InputLabel>
                        <Input
                            value={productForm.data.name}
                            onChange={(e) => productForm.setData('name', e.target.value)}
                            error={productForm.errors.name}
                        />
                        <FieldError message={productForm.errors.name} />
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel required>Kategori</InputLabel>
                            <Select
                                value={productForm.data.category_id}
                                onChange={(e) => productForm.setData('category_id', e.target.value)}
                            >
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.name}
                                    </option>
                                ))}
                            </Select>
                        </div>
                        <div>
                            <InputLabel>Brand</InputLabel>
                            <Input
                                value={productForm.data.brand}
                                onChange={(e) => productForm.setData('brand', e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="grid gap-3 sm:grid-cols-3">
                        <div>
                            <InputLabel required>Harga (Rp)</InputLabel>
                            <Input
                                type="number"
                                value={productForm.data.price}
                                onChange={(e) => productForm.setData('price', e.target.value)}
                            />
                            <FieldError message={productForm.errors.price} />
                        </div>
                        <div>
                            <InputLabel required>Stok</InputLabel>
                            <Input
                                type="number"
                                value={productForm.data.stock}
                                onChange={(e) => productForm.setData('stock', e.target.value)}
                            />
                        </div>
                        <div>
                            <InputLabel required>Satuan</InputLabel>
                            <Input
                                value={productForm.data.unit}
                                onChange={(e) => productForm.setData('unit', e.target.value)}
                            />
                        </div>
                    </div>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <div>
                            <InputLabel>SKU (Opsional)</InputLabel>
                            <Input
                                value={productForm.data.sku}
                                onChange={(e) => productForm.setData('sku', e.target.value)}
                            />
                        </div>
                        <div className="flex items-end pb-2">
                            <label className="inline-flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-200">
                                <input
                                    type="checkbox"
                                    checked={Boolean(productForm.data.is_active)}
                                    onChange={(e) => productForm.setData('is_active', e.target.checked)}
                                    className="h-4 w-4 rounded border-slate-300 text-rose-600"
                                />
                                Produk Aktif Dijual
                            </label>
                        </div>
                    </div>
                    <div>
                        <InputLabel>Deskripsi</InputLabel>
                        <Textarea
                            rows={2}
                            value={productForm.data.description}
                            onChange={(e) => productForm.setData('description', e.target.value)}
                        />
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button variant="secondary" onClick={() => setShowProductModal(false)}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={productForm.processing}>
                            {editingProduct ? 'Simpan Perubahan' : 'Simpan Produk'}
                        </Button>
                    </div>
                </form>
            </Modal>

            {/* Category Modal */}
            <Modal
                open={showCategoryModal}
                onClose={() => setShowCategoryModal(false)}
                title="Kelola Kategori Produk"
            >
                <form onSubmit={submitCategory} className="mb-5 space-y-3 border-b border-slate-200 pb-4 dark:border-slate-800">
                    <div>
                        <InputLabel required>Nama Kategori Baru</InputLabel>
                        <Input
                            value={categoryForm.data.name}
                            onChange={(e) => categoryForm.setData('name', e.target.value)}
                            placeholder="Contoh: Suplemen, Apparel, Minuman"
                        />
                        <FieldError message={categoryForm.errors.name} />
                    </div>
                    <Button type="submit" size="sm" disabled={categoryForm.processing}>
                        <Plus className="h-4 w-4" />
                        Tambah Kategori
                    </Button>
                </form>

                <div className="space-y-2">
                    {categories.map((cat) => (
                        <div
                            key={cat.id}
                            className="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2 text-sm dark:border-slate-800"
                        >
                            <div>
                                <p className="font-semibold text-slate-900 dark:text-white">{cat.name}</p>
                                <p className="text-xs text-slate-500">{cat.products_count || 0} produk</p>
                            </div>
                            <Button
                                variant="ghost"
                                size="xs"
                                onClick={() => {
                                    if (confirm(`Hapus kategori "${cat.name}"?`)) {
                                        router.delete(`/admin/categories/${cat.id}`);
                                    }
                                }}
                            >
                                <Trash2 className="h-4 w-4 text-rose-600" />
                            </Button>
                        </div>
                    ))}
                </div>
            </Modal>
        </AppLayout>
    );
}

