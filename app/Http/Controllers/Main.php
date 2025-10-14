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
        $data = [
            'title' => 'Gestor de tarefas'
        ];

        return view('main', $data);
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
        // submit
    }

    public function logout(){
        session()->forget('username');
        return redirect()->route('login');
    }
}
