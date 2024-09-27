<nav class="navbar border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="bi bi-cash-coin" style="font-size: 2rem;"></i>
        </a>
        <ul class="navbar-nav d-flex flex-row">
            @guest
                <li class="nav-item me-3">
                    <a class="nav-link bg-primary text-white rounded-pill px-3 py-2" href="{{ route('register') }}">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link bg-success text-white rounded-pill px-3 py-2" href="{{ route('login') }}">Masuk</a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link bg-danger text-white rounded-pill px-3 py-2 me-2" href="{{ route('refresh') }}" onclick="return confirm('Apakah Anda yakin ingin mengrefresh data?')">Fresh Data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link bg-success text-white rounded-pill px-3 py-2" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        Keluar
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            @endguest
        </ul>
    </div>
</nav>

@section('style')
<style>
    .navbar {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
    }
    .nav-link {
        color: #333333;
    }
</style>
@endsection