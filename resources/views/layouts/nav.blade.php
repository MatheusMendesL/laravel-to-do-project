<div class="bg-black text-white mb-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col p-3">
                <h3 class="text-primary">{{ $title }}</h3>
            </div>
            <div class="col p-3 text-end">
                 {{session()->get('username')}} <button class="btn btn-outline-danger mx-3" onclick="window.location.href='/logout'">Sair</button>
            </div>
        </div>
    </div>
</div>