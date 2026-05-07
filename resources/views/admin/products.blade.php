@extends('admin.layout')

@section('content')
    <style>
        .product-grid {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .product-card {
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            background: var(--surface);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-height: 260px;
        }

        .product-card .product-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .product-card .product-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            align-items: center;
        }

        .product-card .product-meta .badge {
            font-size: 0.82rem;
            padding: 0.55rem 0.75rem;
        }

        .product-card dl {
            margin: 0;
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .product-card dt {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 0.15rem;
        }

        .product-card dd {
            margin: 0;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .product-card .product-actions {
            margin-top: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .product-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .product-card dl {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="topbar-card p-4 mb-4">
        <div class="section-label">Produk</div>
        <h1 class="display-6 fw-bold mt-2 mb-2">Master Produk Suplemen & Vitamin</h1>
        <p class="muted-copy mb-0">Tampilan produk ini dibuat ulang agar sesuai persis dengan contoh UI.</p>
    </div>

    <div class="panel-card p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">Tambah Produk Baru</h2>
                <div class="small muted-copy">Tambahkan produk yang akan tersedia untuk penjualan kasir.</div>
            </div>
            <span class="badge text-bg-secondary rounded-pill py-2 px-3">{{ $products->count() }} produk</span>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" class="row g-3">
            @csrf

            <div class="col-12 col-xl-5">
                <label class="form-label fw-semibold">Nama Produk</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Whey Protein 1kg" required>
            </div>
            <div class="col-6 col-xl-2">
                <label class="form-label fw-semibold">Kategori</label>
                <select name="category" class="form-select" required>
                    <option value="suplemen" @selected(old('category', 'suplemen') === 'suplemen')>Suplemen</option>
                    <option value="vitamin" @selected(old('category') === 'vitamin')>Vitamin</option>
                </select>
            </div>
            <div class="col-6 col-xl-2">
                <label class="form-label fw-semibold">Status</label>
                <select name="is_active" class="form-select" required>
                    <option value="1" @selected(old('is_active', '1') == '1')>Aktif</option>
                    <option value="0" @selected(old('is_active') == '0')>Nonaktif</option>
                </select>
            </div>
            <div class="col-12 col-xl-3">
                <label class="form-label fw-semibold">Brand</label>
                <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="Contoh: Optimum Nutrition">
            </div>
            <div class="col-12 col-xl-3">
                <label class="form-label fw-semibold">SKU</label>
                <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="Opsional">
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <label class="form-label fw-semibold">Harga</label>
                <input type="number" min="1" name="price" class="form-control" value="{{ old('price') }}" placeholder="350000" required>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <label class="form-label fw-semibold">Stok</label>
                <input type="number" min="0" name="stock" class="form-control" value="{{ old('stock', 0) }}" required>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <label class="form-label fw-semibold">Satuan</label>
                <input type="text" name="unit" class="form-control" value="{{ old('unit', 'pcs') }}" placeholder="pcs / botol" required>
            </div>
            <div class="col-12 col-xl-4">
                <label class="form-label fw-semibold">Deskripsi</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Opsional, misalnya rasa atau ukuran">
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-dark rounded-pill px-4">Simpan Produk</button>
            </div>
        </form>
    </div>

    <div class="panel-card p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">Daftar Produk</h2>
                <div class="small muted-copy">Tampilan kartu produk dibuat ulang sesuai contoh.</div>
            </div>
            <span class="badge text-bg-secondary rounded-pill py-2 px-3">{{ $products->count() }} produk</span>
        </div>

        @if ($products->isEmpty())
            <div class="product-empty">Belum ada produk suplemen atau vitamin yang tersimpan.</div>
        @else
            <div class="product-grid">
                @foreach ($products as $product)
                    <article class="product-card">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                            <div>
                                <div class="product-meta mb-2">
                                    <span class="badge text-bg-{{ $product->category === 'vitamin' ? 'warning' : 'primary' }}">{{ ucfirst($product->category) }}</span>
                                    <span class="badge text-bg-{{ $product->is_active ? 'success' : 'secondary' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </div>
                                <h3 class="product-title">{{ $product->name }}</h3>
                                <div class="small muted-copy">{{ $product->brand ?: 'Brand tidak diisi' }} · {{ $product->sku ?: 'Tanpa SKU' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fs-5 fw-bold">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
                                <div class="small muted-copy">{{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit }}</div>
                            </div>
                        </div>

                        <dl>
                            <div>
                                <dt>Harga</dt>
                                <dd>Rp{{ number_format($product->price, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt>Stok</dt>
                                <dd>{{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit }}</dd>
                            </div>
                            <div class="col-12">
                                <dt>Deskripsi</dt>
                                <dd class="small text-muted">{{ $product->description ?: 'Tidak ada deskripsi' }}</dd>
                            </div>
                        </dl>

                        <div class="product-actions">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">Edit</button>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Hapus</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    @foreach ($products as $product)
        <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Produk {{ $product->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="category" class="form-select" required>
                                        <option value="suplemen" @selected($product->category === 'suplemen')>Suplemen</option>
                                        <option value="vitamin" @selected($product->category === 'vitamin')>Vitamin</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="is_active" class="form-select" required>
                                        <option value="1" @selected($product->is_active)>Aktif</option>
                                        <option value="0" @selected(! $product->is_active)>Nonaktif</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Brand</label>
                                    <input type="text" name="brand" class="form-control" value="{{ $product->brand }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">SKU</label>
                                    <input type="text" name="sku" class="form-control" value="{{ $product->sku }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Satuan</label>
                                    <input type="text" name="unit" class="form-control" value="{{ $product->unit }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Harga</label>
                                    <input type="number" min="1" name="price" class="form-control" value="{{ $product->price }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stok</label>
                                    <input type="number" min="0" name="stock" class="form-control" value="{{ $product->stock }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-dark">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
