<footer class="border-top">
    <div class="container">
        <div class="row py-2 justify-content-between">
            <div class="col-2 text-center">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <i class="bi bi-house-door{{ Route::is('home') ? '-fill' : '' }} text-secondary fs-5"></i>
                    <p class="mb-0 small {{ Route::is('home') ? 'text-success' : 'text-secondary' }}">Home</p>
                </a>
            </div>
            <div class="col-2 text-center">
                <a href="{{ route('transactions.index') }}" class="text-decoration-none">
                    <i class="bi bi-wallet{{ Route::is('transactions.index') ? '-fill' : '' }} text-secondary fs-5"></i>
                    <p class="mb-0 small {{ Route::is('transactions.index') ? 'text-success' : 'text-secondary' }}">Transaksi</p>
                </a>
            </div>
            <div class="col-2 text-center">
                <a href="{{ route('goal.index') }}" class="text-decoration-none">
                    <i class="bi bi-clipboard{{ Route::is('goal.index') ? '-fill' : '' }} text-secondary fs-5"></i>
                    <p class="mb-0 small {{ Route::is('goal.index') ? 'text-success' : 'text-secondary' }}">Goal</p>
                </a>
            </div>
            <div class="col-2 text-center">
                <a href="{{ route('budgets.index') }}" class="text-decoration-none">
                    <i class="bi bi-wallet{{ Route::is('budgets.index') ? '-fill' : '' }} fs-5 text-secondary"></i>
                    <p class="mb-0 small {{ Route::is('budgets.index') ? 'text-success' : 'text-secondary' }}">Budget</p>
                </a>
            </div>
            {{-- <div class="col-2 text-center">
                <a href="#" class="text-decoration-none">
                    <i class="bi bi-clock-history fs-5 text-secondary"></i>
                    <p class="mb-0 small text-secondary">Riwayat</p>
                </a>
            </div> --}}
            <div class="col-2 text-center">
                <a href="{{ route('accounts.index') }}" class="text-decoration-none">
                    <i class="bi bi-piggy-bank{{ Route::is('accounts.index') ? '-fill' : '' }} fs-5 text-secondary"></i>
                    <p class="mb-0 small {{ Route::is('accounts.index') ? 'text-success' : 'text-secondary' }}">Account</p>
                </a>
            </div>
        </div>
    </div>
</footer>