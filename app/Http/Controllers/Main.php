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
            'datatables' => true,
            'tasks' => $this->_get_tasks(),
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
            'text_username.min' => 'O campo deve conter no mínimo :min caracteres',
            'text_password.min' => 'O campo deve conter no mínimo :max caracteres'
        ]);

        $username = $request->input('text_username');
        $password = $request->input('text_password');

        $model = new UserModel();
        $user = $model->where('username', '=', $username)
            ->whereNull('deleted_at')
            ->first();

        if ($user) {

            // verifiy the password
            if (password_verify($password, $user->password)) {
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

    public function logout()
    {
        session()->forget('username');
        return redirect()->route('login');
    }

    public function new_task()
    {
        $data = [
            'title' => $this->title
        ];

        return view('new_task_frm', $data);
    }

    public function new_task_submit(Request $request)
    {
        $request->validate([
            'text_task_name' => 'required|min:3|max:200',
            'text_task_description' => 'required|min:3|max:1000'
        ], [
            'text_task_name.required' => 'O campo é obrigatório',
            'text_task_name.min' => 'O campo deve conter no mínimo :min caracteres',
            'text_task_name.max' => 'O campo deve conter no máximo :max caracteres',

            'text_task_description.required' => 'O campo é obrigatório',
            'text_task_description.min' => 'O campo deve conter no mínimo :min caracteres',
            'text_task_description.max' => 'O campo deve conter no máximo :max caracteres',

        ]);

        $task_name = $request->input('text_task_name');
        $task_description = $request->input('text_task_description');

        $model = new TaskModel();
        $task = $model
            ->where('id_user', '=', session()->get('id'))
            ->where('task_name', '=', $task_name)
            ->whereNull('deleted_at')
            ->first();

        if ($task) {
            return redirect()->route('new_task')->with('task_error', 'Já existe uma tarefa com esse nome');
        }

        $model->id_user = session()->get('id');
        $model->task_name = $task_name;
        $model->task_description = $task_description;
        $model->task_status = 'new';
        $model->created_at = date('Y-m-d H:i:s');
        $model->save();

        return redirect()->route('index');
    }

    // private methods

    private function _get_tasks()
    {
        $model = new TaskModel();

        $tasks = $model->where('id_user', '=', session()->get('id'))
            ->whereNull('deleted_at')
            ->get();
        $collection = [];
        foreach ($tasks as $task) {

            $link_edit = '<a href="' . route('edit_task', ['id' => $task->id]) . '" class="btn btn-secondary m-1"><i class="bi bi-pencil-square"></i></a>';
            $link_delete = '<a href="' . route('delete_task', ['id' => $task->id]) . '" class="btn btn-danger"><i class="bi bi-trash"></i></a>';

            $collection[] = [
                'task_name' => $task->task_name,
                'task_status' => $this->status_name($task->task_status),
                'task_actions' => $link_edit . $link_delete
            ];
        }

        return $collection;
    }

    private function status_name($status)
    {
        $status_collection = [
            'new' => 'Nova',
            'in_progress', 'Em progresso',
            'cancelled', 'Cancelada',
            'completed', 'Concluída'
        ];


        if(key_exists($status, $status_collection)){
            return $status_collection[$status];
        }
        
    }
}
