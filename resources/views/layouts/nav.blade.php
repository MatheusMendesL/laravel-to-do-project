<div class="bg-black text-white mb-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col p-3">
                <h3 class="text-primary">{{ $title }}</h3>
            </div>
            <div class="col p-3 text-end">
                 <i class="bi bi-person me-2"></i><span class="me-3">{{session()->get('username')}} </span>
                 <a href="{{ route('logout') }}" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right me-2"></i>Sair</a>
            </div>
        </div>
    </div>
</div>