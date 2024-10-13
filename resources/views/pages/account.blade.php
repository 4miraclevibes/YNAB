@extends('layouts.main')

@section('style')
<style>
    .accounts-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .accounts-wrapper::-webkit-scrollbar {
        display: none;
    }

    .account-box {
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .account-item.active .account-box {
        border: 2px solid black;
    }

    .account-item {
        flex: 0 0 auto;
        width: 80px;
        text-align: center;
    }

    .account-item img {
        background-color: white;
        border-radius: 10%;
        padding: 5px;
    }

    .account-item p {
        font-size: 0.7rem;
        margin-top: 5px;
    }

    .wrapper {
        max-width: 480px;
        margin: 0 auto;
        background-color: white;
        padding-bottom: 60px;
    }

    .account-item.active img {
        border: 2px solid #007bff;
    }

    .transaction-list {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container wrapper">
    <section class="accounts mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Akun</h6>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                Tambah Akun
            </button>
        </div>
        <div class="accounts-wrapper">
            <div class="d-flex">
                <div class="account-item me-3 {{ !$accountId ? 'active' : '' }}">
                    <a href="{{ route('accounts.index') }}" class="text-decoration-none">
                        <div class="account-box d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background-color: #4CAF50; color: white; border-radius: 10%;">
                            <span class="fs-4">ALL</span>
                        </div>
                        <p class="mb-0 text-dark">Semua</p>
                    </a>
                </div>
                @foreach($accounts as $account)
                <div class="account-item me-3 position-relative {{ $accountId == $account->id ? 'active' : '' }}">
                    <a href="{{ route('accounts.index', ['account_id' => $account->id]) }}" class="text-decoration-none">
                        <div class="account-box d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background-color: {{ $account->account_type == 'cash' ? '#4CAF50' : ($account->account_type == 'bank' ? '#2196F3' : ($account->account_type == 'credit_card' ? '#FF5722' : ($account->account_type == 'e_wallet' ? '#FFC107' : '#9E9E9E'))) }}; color: white; border-radius: 10%;">
                            <span class="fs-4">{{ strtoupper(substr($account->account_type, 0, 3)) }}</span>
                        </div>
                        <p class="mb-0 text-dark">{{ $account->account_name }}</p>
                        <p class="mb-0 text-muted">Rp {{ number_format($account->balance, 0, ',', '.') }}</p>
                    </a>
                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="position-absolute" style="top: 0; right: 0;">
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

    <section class="transactions mb-4">
        <h6 class="mb-3">
            @if($accountId)
                Transaksi: {{ $accounts->find($accountId)->account_name }}
            @else
                Semua Transaksi
            @endif
        </h6>
        <div class="transaction-list">
            @forelse($transactions as $transaction)
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">{{ $transaction->category->name }}</h6>
                            <p class="card-text text-muted mb-0">{{ $transaction->account->account_name }}</p>
                            <small class="text-muted">{{ $transaction->transaction_date }}</small>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0 {{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type == 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </h6>
                            <small class="text-muted">{{ $transaction->description }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted">Belum ada transaksi.</p>
            @endforelse
        </div>
    </section>
</div>

<!-- Modal Tambah Akun -->
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAccountModalLabel">Tambah Akun Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('accounts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="account_name" class="form-label">Nama Akun</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_type" class="form-label">Tipe Akun</label>
                        <select class="form-select" id="account_type" name="account_type" required>
                            <option value="cash">Kas</option>
                            <option value="bank">Bank</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="e_wallet">E-Wallet</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="balance" class="form-label">Saldo Awal</label>
                        <input type="number" class="form-control" id="balance" name="balance" required>
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
    // Konfirmasi penghapusan akun
    document.querySelectorAll('.account-item form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('Apakah Anda yakin ingin menghapus akun ini?')) {
                this.submit();
            }
        });
    });
});
</script>
@endsection
