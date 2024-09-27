@extends('layouts.main')

@section('style')
<style>
    .categories-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .categories-wrapper::-webkit-scrollbar {
        display: none;
    }

    .category-item {
        flex: 0 0 auto;
        width: 80px;
        text-align: center;
    }

    .category-item img {
        width: 80px;
        height: 80px;
        background-color: white;
        border-radius: 10%;
        padding: 5px;
        object-fit: cover; /* Ini akan memastikan gambar menutupi area dengan baik */
    }

    .category-item p {
        font-size: 0.7rem;
        margin-top: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .transaction-card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }

    .transaction-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }

    .wrapper {
        max-width: 480px;
        margin: 0 auto;
        background-color: white;
        padding-bottom: 60px;
    }

    .transaction-list {
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
    }

    .transaction-list::-webkit-scrollbar {
        width: 6px;
    }

    .transaction-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .transaction-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .transaction-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .category-item.active img {
        border: 2px solid #007bff;
    }
</style>
@endsection

@section('content')
<div class="container wrapper">
    <section class="banner mb-3">
        <img src="https://via.placeholder.com/480x200/4CAF50/FFFFFF?text=Hai+{{ Auth::user()->name }}" alt="Banner" class="w-100 rounded">
    </section>

    <section class="categories mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Kategori</h6>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                Tambah Kategori
            </button>
        </div>
        <div class="categories-wrapper">
            <div class="d-flex">
                <div class="category-item me-3 {{ !$categoryId ? 'active' : '' }}">
                    <a href="{{ route('transactions.index') }}" class="text-decoration-none">
                        <img src="https://via.placeholder.com/60/4CAF50/FFFFFF?text=$" alt="Semua" class="w-100 card">
                        <p class="mb-0 text-dark">Semua</p>
                    </a>
                </div>
                @foreach($categories as $category)
                <div class="category-item me-3 position-relative {{ $categoryId == $category->id ? 'active' : '' }}">
                    <a href="{{ route('transactions.index', ['category_id' => $category->id]) }}" class="text-decoration-none">
                        <img src="{{ $category->image ?? 'https://via.placeholder.com/60' }}" alt="{{ $category->name }}" class="w-100 card">
                        <p class="mb-0 text-dark">{{ $category->name }}</p>
                    </a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="position-absolute" style="top: 0; right: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm p-0" style="width: 20px; height: 20px; line-height: 1; font-size: 12px;">
                            <i class="bi bi-x"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="transactions">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">
                @if($categoryId)
                    Transaksi {{ $categories->find($categoryId)->name }}
                @else
                    Semua Transaksi
                @endif
            </h6>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                Tambah Transaksi
            </button>
        </div>
        <div class="transaction-list" style="max-height: 400px; overflow-y: auto;">
            @foreach($transactions as $transaction)
            <div class="transaction-card card mb-2">
                <div class="card-body p-2">
                    <div class="d-flex">
                        <img src="{{ $transaction->category->image ?? 'https://via.placeholder.com/80' }}" class="transaction-thumbnail me-3" alt="{{ $transaction->category->name }}">
                        <div class="flex-grow-1 d-flex flex-column justify-content-between">
                            <h6 class="mb-0">{{ $transaction->description ?? 'Tidak ada deskripsi' }}</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem">
                                {{ $transaction->category->name }} • {{ $transaction->account->account_name }} <br>{{ $transaction->transaction_date instanceof \Carbon\Carbon ? $transaction->transaction_date->format('d M Y') : date('d M Y', strtotime($transaction->transaction_date)) }}
                            </p>
                            <p class="fw-bold mb-0"><span class="text-{{ $transaction->type == 'income' ? 'success' : 'danger' }}">{{ $transaction->type == 'income' ? 'In' : 'Ex' }}</span> Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">URL Gambar Kategori</label>
                        <input type="url" class="form-control" id="image" name="image" placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTransactionModalLabel">Tambah Transaksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="account_id" class="form-label">Akun</label>
                        <select class="form-select" id="account_id" name="account_id" required>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" id="transaction_date" value="{{ now()->format('Y-m-d') }}" name="transaction_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe Transaksi</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Validasi Anggaran</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="on_budget" id="onBudgetYes" value="1">
                                <label class="form-check-label" for="onBudgetYes">Ya</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="on_budget" id="onBudgetNo" value="0" checked>
                                <label class="form-check-label" for="onBudgetNo">Tidak</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script lama untuk konfirmasi penghapusan kategori
    document.querySelectorAll('.category-item form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
                this.submit();
            }
        });
    });

    // Script baru untuk mengelola visibilitas opsi "Validasi Anggaran"
    const typeSelect = document.getElementById('type');
    const onBudgetContainer = document.querySelector('.mb-3:has([name="on_budget"])');

    function toggleOnBudgetVisibility() {
        if (typeSelect.value === 'expense') {
            onBudgetContainer.style.display = 'block';
        } else {
            onBudgetContainer.style.display = 'none';
            document.getElementById('onBudgetNo').checked = true;
        }
    }

    typeSelect.addEventListener('change', toggleOnBudgetVisibility);
    toggleOnBudgetVisibility(); // Run once on page load
});
</script>
@endsection
