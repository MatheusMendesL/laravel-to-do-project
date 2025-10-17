<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use App\Models\UserModel;
use Illuminate\Http\Request;

class Main extends Controller
{

    public $title = 'Gestor de Tarefas';

    public function index()
    {
        $data = [
            'title' => 'Gestor de tarefas',
            'tasks' => $this->_get_tasks()
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

    public function login_submit(Request $request)
    {
        $request->validate([
            'text_username' => 'required|min:3',
            'text_password' => 'required|min:3'
        ], [
            'text_username.required' => 'O campo é obrigatório',
            'text_password.required' => 'O campo é obrigatório',
            'text_username.min' => 'O campo deve conter no mínimo 3 caracteres',
            'text_password.min' => 'O campo deve conter no mínimo 3 caracteres'
        ]);

        $username = $request->input('text_username');
        $password = $request->input('text_password');

        $model = new UserModel();
        $user = $model->where('username', '=', $username)
        ->whereNull('deleted_at')
        ->first();

        if($user){

            // verifiy the password
            if(password_verify($password, $user->password)){
                $session_data = [
                    'id' => $user->id,
                    'username' => $user->username
                ];

                session()->put($session_data);

                redirect()->route('index');

            }
        }

        return redirect()->route('login')->with('login_error', 'Login inválido');
    }

    public function logout(){
        session()->forget('username');
        return redirect()->route('login');
    }

    public function new_task(){
        $data = [
            'title' => $this->title
        ];

        return view('new_task_frm', $data);
    }

    public function new_task_submit(){
        echo 'teste';
    }

    // private methods

    private function _get_tasks(){
        $model = new TaskModel();
        return $model->where('id_user', '=', session()->get('id'))
        ->whereNull('deleted_at')
        ->get();
    }
}
