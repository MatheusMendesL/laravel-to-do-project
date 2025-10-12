<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Main extends Controller
{

    public $title = 'Gestor de Tarefas';

    public function index()
    {
        echo 'Gestor de tarefas';
    }

    public function login()
    {

        $data = [
            'title' => $this->title
        ];
        return view('login_frm', $data);
    }

    public function login_submit()
    {
        $data = [
            'title' => $this->title
        ];
        session()->put('username', 'admin');
        echo 'logado';
    }

    public function main(){
        $data = [
            'title' => $this->title
        ];

        return view('main', $data);
    }

    public function logout(){
        session()->forget('username');
        return redirect()->route('login');
    }
}
