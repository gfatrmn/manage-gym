@extends('admin.layout')

@section('content')

<style>
    .dashboard-page {
        padding: 1rem 2rem;
    }

    .dashboard-heading {
        border-bottom: 1px solid rgba(255,255,255,.06);
        padding-bottom: 1.2rem;
        margin-bottom: 2rem;
    }

    .dashboard-subtitle {
        font-size: .75rem;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: rgba(255,255,255,.4);
        margin-bottom: .7rem;
        font-weight: 600;
    }

    .dashboard-title {
        font-size: clamp(1.7rem, 3vw, 2.4rem);
        font-weight: 700;
        color: #fff;
        margin: 0;
    }

    .dashboard-description {
        color: rgba(255,255,255,.55);
        margin-top: .6rem;
        max-width: 760px;
        line-height: 1.7;
        font-size: .95rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.8rem;
    }

    .stats-card {
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
        border-radius: 1rem;
        padding: 1rem 1.2rem;
    }

    .stats-label {
        font-size: .7rem;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: .12em;
        margin-bottom: .45rem;
    }

    .stats-value {
        font-size: 1.6rem;
        font-weight: 700;
        color: #fff;
    }

    .form-card,
    .table-card {
        background: rgba(255,255,255,.025);
        border: 1px solid rgba(255,255,255,.06);
        border-radius: 1.3rem;
    }

    .section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: .2rem;
    }

    .section-subtitle {
        color: rgba(255,255,255,.55);
        font-size: .9rem;
    }

    .form-label {
        color: rgba(255,255,255,.72);
        font-size: .88rem;
        font-weight: 600;
    }

    .form-control,
    .form-select {
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.08);
        color: #fff;
        border-radius: .8rem;
        padding: .8rem 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        background: rgba(255,255,255,.08);
        color: #fff;
        border-color: #ef4444;
        box-shadow: none;
    }

    .form-control::placeholder {
        color: rgba(255,255,255,.35);
    }

    .product-table-wrap {
        overflow-x: auto;
    }

    .product-table {
        width: 100%;
        min-width: 1100px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .product-table thead th {
        background: rgba(255,255,255,.04);
        color: rgba(255,255,255,.75);
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,.06);
        white-space: nowrap;
    }

    .product-table tbody tr {
        transition: .2s ease;
        border-bottom: 1px solid rgba(255,255,255,.04);
    }

    .product-table tbody tr:hover {
        background: rgba(255,255,255,.025);
    }

    .product-table td {
        padding: 1rem;
        color: rgba(255,255,255,.9);
        vertical-align: middle;
    }

    .product-name {
        font-weight: 700;
        color: #fff;
    }

    .product-small {
        color: rgba(255,255,255,.45);
        font-size: .82rem;
        margin-top: .2rem;
    }

    .table-price {
        font-weight: 700;
        color: #fff;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        padding: .45rem .8rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 700;
    }

    .badge-suplemen {
        background: rgba(59,130,246,.15);
        color: #60a5fa;
    }

    .badge-vitamin {
        background: rgba(245,158,11,.15);
        color: #fbbf24;
    }

    .badge-active {
        background: rgba(34,197,94,.15);
        color: #4ade80;
    }

    .badge-inactive {
        background: rgba(107,114,128,.18);
        color: #d1d5db;
    }

    .action-group {
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
    }

    .btn-action {
        border: none;
        border-radius: 999px;
        padding: .45rem 1rem;
        font-size: .78rem;
        font-weight: 600;
        transition: .2s ease;
    }

    .btn-edit {
        background: rgba(245,158,11,.15);
        color: #fbbf24;
    }

    .btn-edit:hover {
        background: #f59e0b;
        color: #fff;
    }

    .btn-delete {
        background: rgba(239,68,68,.15);
        color: #f87171;
    }

    .btn-delete:hover {
        background: #ef4444;
        color: #fff;
    }

    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .product-table {
            min-width: 1000px;
        }
    }
</style>

<div class="dashboard-page">

    {{-- HEADER --}}
    <div class="dashboard-heading d-flex justify-content-between align-items-end flex-wrap gap-3">
        <div>
            <div class="dashboard-subtitle">
                ARENA GYM · PRODUCT MANAGEMENT
            </div>

            <h1 class="dashboard-title">
                Master Produk Suplemen & Vitamin
            </h1>
        </div>

        <button class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
            <i class="fas fa-box me-2"></i>
            Total {{ $products->count() }} Produk
        </button>
    </div>

    {{-- STATS --}}
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-label">Total Produk</div>
            <div class="stats-value">
                {{ $products->count() }}
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-label">Produk Aktif</div>
            <div class="stats-value text-success">
                {{ $products->where('is_active', 1)->count() }}
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-label">Vitamin</div>
            <div class="stats-value text-warning">
                {{ $products->where('category', 'vitamin')->count() }}
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-label">Stok Menipis</div>
            <div class="stats-value text-danger">
                {{ $products->where('stock', '<', 3)->count() }}
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <div class="form-card p-4 mb-4">

        <div class="mb-4">
            <div class="section-title">Tambah Produk Baru</div>
            <div class="section-subtitle">
                Tambahkan produk suplemen atau vitamin baru ke sistem kasir Arena Gym.
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}">
            @csrf

            <div class="row g-3">

                <div class="col-lg-4">
                    <label class="form-label">Nama Produk</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           placeholder="Contoh: Whey Protein"
                           required>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="suplemen">Suplemen</option>
                        <option value="vitamin">Vitamin</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

                <div class="col-lg-4">
                    <label class="form-label">Brand</label>
                    <input type="text"
                           name="brand"
                           class="form-control"
                           placeholder="Contoh: Optimum Nutrition">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">SKU</label>
                    <input type="text"
                           name="sku"
                           class="form-control"
                           placeholder="SKU Produk">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Harga</label>
                    <input type="number"
                           name="price"
                           class="form-control"
                           placeholder="350000"
                           required>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Stok</label>
                    <input type="number"
                           name="stock"
                           class="form-control"
                           placeholder="0"
                           required>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Satuan</label>
                    <input type="text"
                           name="unit"
                           class="form-control"
                           value="pcs"
                           required>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Deskripsi</label>
                    <input type="text"
                           name="description"
                           class="form-control"
                           placeholder="Opsional">
                </div>

                <div class="col-12 text-end">
                    <button type="submit"
                            class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="fas fa-plus me-2"></i>
                        Simpan Produk
                    </button>
                </div>

            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="table-card p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="section-title">Daftar Produk</div>
            </div>
        </div>

        <div class="product-table-wrap">

            <table class="product-table">

                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Brand</th>
                        <th>SKU</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($products as $product)
                        <tr>

                            <td>
                                <div class="product-name">
                                    {{ $product->name }}
                                </div>

                                <div class="product-small">
                                    Produk Arena Gym
                                </div>
                            </td>

                            <td>
                                <span class="badge-soft {{ $product->category === 'vitamin' ? 'badge-vitamin' : 'badge-suplemen' }}">
                                    {{ ucfirst($product->category) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge-soft {{ $product->is_active ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>
                                {{ $product->brand ?: '-' }}
                            </td>

                            <td>
                                {{ $product->sku ?: '-' }}
                            </td>

                            <td class="table-price">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ number_format($product->stock, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $product->unit }}
                            </td>

                            <td style="max-width:220px;">
                                <span class="product-small">
                                    {{ $product->description ?: '-' }}
                                </span>
                            </td>

                            <td>
                                <div class="action-group">

                                    <button
                                        class="btn-action btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editProductModal{{ $product->id }}">
                                        Edit
                                    </button>

                                    <form method="POST"
                                          action="{{ route('admin.products.destroy', $product) }}"
                                          onsubmit="return confirm('Hapus produk ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn-action btn-delete">
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10"
                                class="text-center py-5 text-secondary">
                                Belum ada produk tersedia.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

    {{-- MODAL EDIT PRODUCT --}}
@foreach ($products as $product)

<div class="modal fade"
     id="editProductModal{{ $product->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content bg-dark text-white border-0 shadow-lg"
             style="border-radius: 1.3rem;">

            <form method="POST"
                  action="{{ route('admin.products.update', $product) }}">

                @csrf
                @method('PUT')

                <div class="modal-header border-bottom border-secondary border-opacity-25">
                    <h5 class="modal-title fw-bold">
                        Edit Produk
                    </h5>

                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ $product->name }}"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Kategori</label>

                            <select name="category"
                                    class="form-select"
                                    required>

                                <option value="suplemen"
                                    @selected($product->category === 'suplemen')>
                                    Suplemen
                                </option>

                                <option value="vitamin"
                                    @selected($product->category === 'vitamin')>
                                    Vitamin
                                </option>

                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status</label>

                            <select name="is_active"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    @selected($product->is_active)>
                                    Aktif
                                </option>

                                <option value="0"
                                    @selected(!$product->is_active)>
                                    Nonaktif
                                </option>

                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Brand</label>

                            <input type="text"
                                   name="brand"
                                   class="form-control"
                                   value="{{ $product->brand }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">SKU</label>

                            <input type="text"
                                   name="sku"
                                   class="form-control"
                                   value="{{ $product->sku }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Satuan</label>

                            <input type="text"
                                   name="unit"
                                   class="form-control"
                                   value="{{ $product->unit }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Harga</label>

                            <input type="number"
                                   name="price"
                                   class="form-control"
                                   value="{{ $product->price }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Stok</label>

                            <input type="number"
                                   name="stock"
                                   class="form-control"
                                   value="{{ $product->stock }}"
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>

                            <textarea name="description"
                                      class="form-control"
                                      rows="4">{{ $product->description }}</textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer border-top border-secondary border-opacity-25">

                    <button type="button"
                            class="btn btn-outline-light rounded-pill px-4"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-danger rounded-pill px-4 fw-bold">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endforeach

@endsection
