@extends('layouts.main')

@section('style')
<style>
    .budgets-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .budgets-wrapper::-webkit-scrollbar {
        display: none;
    }

    .budget-item {
        flex: 0 0 auto;
        width: 150px;
        text-align: center;
    }

    .budget-item img {
        width: 150px;
        height: 150px;
        background-color: white;
        border-radius: 10%;
        padding: 5px;
        object-fit: cover; /* Ini akan memastikan gambar menutupi area dengan baik */
    }

    .budget-item p {
        font-size: 0.7rem;
        margin-top: 5px;
    }

    .wrapper {
        max-width: 480px;
        margin: 0 auto;
        background-color: white;
        padding-bottom: 60px;
    }

    .progress {
        height: 5px;
    }

    .budget-transaction-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .budget-transaction-list .transaction-card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }

    .budget-transaction-list .transaction-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }

    .budget-item.active img {
        border: 2px solid #007bff;
    }
</style>
@endsection

@section('content')
<div class="container wrapper">
    <section class="budgets mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Anggaran</h6>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                Tambah Anggaran
            </button>
        </div>
        <div class="budgets-wrapper">
            <div class="d-flex">
                <div class="budget-item me-3 {{ !$budgetId ? 'active' : '' }}">
                    <a href="{{ route('budgets.index') }}" class="text-decoration-none">
                        <img src="https://via.placeholder.com/60/4CAF50/FFFFFF?text=$" alt="Semua" class="w-100">
                        <p class="mb-0 text-dark">Semua</p>
                    </a>
                </div>
                @foreach($budgets as $budget)
                <div class="budget-item me-3 position-relative {{ $budgetId == $budget->id ? 'active' : '' }}">
                    <a href="{{ route('budgets.index', ['budget_id' => $budget->id]) }}" class="text-decoration-none">
                        <img src="{{ $budget->category->image ?? 'https://via.placeholder.com/60' }}" alt="{{ $budget->name }}" class="w-100">
                        <p class="mb-0 text-dark">{{ $budget->name }}</p>
                        <p class="mb-0 text-muted">Rp {{ number_format($budget->amount, 0, ',', '.') }}</p>
                        <p class="mb-0 text-muted" style="font-size: 0.7rem;">
                            Till: {{ \Carbon\Carbon::parse($budget->due_date)->format('d M Y') }}
                        </p>
                        <p class="mb-0 text-muted" style="font-size: 0.7rem;">
                            {{ $budget->status == 'on_budget' ? 'On Budget' : 'Off Budget' }}
                        </p>
                        @php
                            $totalSpent = $budget->budgetTransactions->sum('amount');
                            $percentage = $budget->amount > 0 ? ($totalSpent / $budget->amount) * 100 : 0;
                        @endphp
                        <div class="progress mt-1">
                            <div class="progress-bar {{ $percentage > 100 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ min($percentage, 100) }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="mb-0 text-muted" style="font-size: 0.6rem;">
                            Rp {{ number_format($budget->budgetTransactions->sum('amount'), 0, ',', '.') }} / Rp {{ number_format($budget->amount + $budget->budgetTransactions->sum('amount'), 0, ',', '.') }}
                        </p>
                    </a>
                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="position-absolute" style="top: 0; right: 0;">
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

    <section class="budget-transactions mb-4">
        <h6 class="mb-3">
            @if($budgetId)
                Transaksi Anggaran: {{ $budgets->find($budgetId)->name }}
            @else
                Semua Transaksi Anggaran
            @endif
        </h6>
        <div class="budget-transaction-list">
            @forelse($budgetTransactions as $transaction)
                <div class="transaction-card card">
                    <div class="card-body p-2">
                        <div class="d-flex">
                            <img src="{{ $transaction->budget->category->image ?? 'https://via.placeholder.com/80' }}" class="transaction-thumbnail me-3" alt="{{ $transaction->budget->category->name }}">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $transaction->description }}</h6>
                                <p class="text-muted mb-0" style="font-size: 0.8rem">
                                    {{ $transaction->budget->category->name }} • {{ $transaction->budget->name }} • {{ $transaction->transaction_date instanceof \Carbon\Carbon ? $transaction->transaction_date->format('d M Y') : date('d M Y', strtotime($transaction->transaction_date)) }}
                                </p>
                            </div>
                            <div>
                                <form action="{{ route('budget-transactions.destroy', $transaction) }}" method="POST" class="delete-budget-transaction-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Belum ada transaksi anggaran.</p>
            @endforelse
        </div>
    </section>
</div>

<!-- Modal Tambah Anggaran -->
<div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBudgetModalLabel">Tambah Anggaran Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('budgets.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Anggaran</label>
                        <input type="text" class="form-control" id="name" name="name" required>
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
                        <label for="amount" class="form-label">Jumlah Anggaran</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Tanggal Jatuh Tempo</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required>
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
    // Konfirmasi penghapusan anggaran
    document.querySelectorAll('.budget-item form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus anggaran ini?')) {
                this.submit();
            }
        });
    });

    // Konfirmasi penghapusan transaksi anggaran
    document.querySelectorAll('.delete-budget-transaction-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus transaksi anggaran ini?')) {
                this.submit();
            }
        });
    });
});
</script>
@endsection