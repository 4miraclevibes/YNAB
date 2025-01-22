@extends('layouts.main')

@section('style')
<style>
    .goal-card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .goal-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .progress {
        height: 10px;
    }
    .transaction-list {
        max-height: 200px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="container">
    <h1 class="mb-4">Tujuan Keuangan Anda</h1>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addGoalModal">
        Tambah Tujuan Baru
    </button>

    <div class="row">
        @forelse($goals as $goal)
            <div class="col-12 mb-3">
                <div class="card goal-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $goal->name }}</h5>
                        <p class="card-text">Target: Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                        <p class="card-text">Terkumpul: Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</p>
                        <p class="card-text">Tenggat: {{ \Carbon\Carbon::parse($goal->deadline)->locale('id')->isoFormat('D MMMM Y') }}</p>
                        <p class="card-text">Gaji Bulanan: Rp {{ number_format($goal->monthly_income, 0, ',', '.') }}</p>
                        <p class="card-text">Rencana Tabungan (25%): Rp {{ number_format($goal->monthly_savings, 0, ',', '.') }}</p>
                        
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: {{ ($goal->current_amount / $goal->target_amount) * 100 }}%"></div>
                        </div>
                        
                        @php
                            $today = new DateTime();
                            $deadline = new DateTime($goal->deadline);
                            $daysLeft = $today->diff($deadline)->days;
                            $monthsLeft = ceil($daysLeft / 30);
                            $remainingAmount = $goal->target_amount - $goal->current_amount;
                            $dailyRecommendation = $daysLeft > 0 ? ceil($goal->target_amount / $daysLeft) : 0;
                            $monthlyRecommendation = $goal->monthly_savings;
                        @endphp
                        
                        @if($daysLeft > 0 && $remainingAmount > 0)
                            <div class="mb-3">
                                <strong>Rekomendasi tabungan:</strong><br>
                                • Rp {{ number_format($dailyRecommendation, 0, ',', '.') }} per hari<br>
                                • Rp {{ number_format($monthlyRecommendation, 0, ',', '.') }} per bulan<br>
                                <small class="text-muted">untuk {{ $monthsLeft }} bulan ({{ $daysLeft }} hari) tersisa</small>
                            </div>
                        @elseif($remainingAmount <= 0)
                            <div class="mb-3">
                                <strong class="text-success">Selamat! Target sudah tercapai.</strong>
                            </div>
                        @else
                            <div class="mb-3">
                                <strong class="text-danger">Tenggat waktu sudah lewat.</strong>
                            </div>
                        @endif

                        <button class="btn btn-sm btn-success mb-2" data-bs-toggle="modal" data-bs-target="#addTransactionModal" data-goal-id="{{ $goal->id }}">
                            Tambah Transaksi
                        </button>
                        <form action="{{ route('goal.destroy', $goal->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger mb-2" onclick="return confirm('Apakah Anda yakin ingin menghapus tujuan ini?')">Hapus Tujuan</button>
                        </form>
                        
                        <h6 class="mt-3">Riwayat Transaksi:</h6>
                        <div class="transaction-list">
                            @forelse($goal->goalTransactions as $transaction)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                                    <span>{{ $transaction->transaction_date }}</span>
                                    <form action="{{ route('goal-transaction.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p>Belum ada transaksi.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>Anda belum memiliki tujuan keuangan. Mulailah dengan menambahkan tujuan baru!</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Tujuan -->
<div class="modal fade" id="addGoalModal" tabindex="-1" aria-labelledby="addGoalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGoalModalLabel">Tambah Tujuan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('goal.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Tujuan</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="target_amount" class="form-label">Target Jumlah</label>
                        <input type="number" class="form-control" id="target_amount" name="target_amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="monthly_income" class="form-label">Gaji Per Bulan</label>
                        <input type="number" class="form-control" id="monthly_income" name="monthly_income" required placeholder="Masukkan gaji per bulan">
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
                <h5 class="modal-title" id="addTransactionModalLabel">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('goal-transaction.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="goal_id" name="goal_id">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                        <input type="date" class="form-control" id="transaction_date" name="transaction_date" required>
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
    var addTransactionModal = document.getElementById('addTransactionModal')
    addTransactionModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget
        var goalId = button.getAttribute('data-goal-id')
        var modalBodyInput = addTransactionModal.querySelector('.modal-body input[name="goal_id"]')
        modalBodyInput.value = goalId
    })
</script>
@endsection
