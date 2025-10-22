<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class Main extends Controller
{

    public $title = 'Gestor de Tarefas';

    public function index()
    {
        $data = [
            'title' => 'Gestor de Tarefas',
            'datatables' => true
        ];

        if (!empty(session('search'))) {
            $data['search'] = session('search');
            $data['tasks'] = $this->_get_tasks(session('tasks'));

            session()->forget('search');
            session()->forget('tasks');

        } else if (session('filter')) {

            $data['filter'] = session('filter');
            $data['tasks'] = $this->_get_tasks(session('tasks'));

            session()->forget('filter');
            session()->forget('tasks');
        } else {
            $model = new TaskModel();
            $tasks = $model->where('id_user', '=', session('id'))
                ->whereNull('deleted_at')
                ->get();
            $data['tasks'] = $this->_get_tasks($tasks);
        }



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
            return redirect()
            ->route('new_task')
            ->withInput()
            ->with('task_error', 'Já existe uma tarefa com esse nome');
        }

        $model->id_user = session()->get('id');
        $model->task_name = $task_name;
        $model->task_description = $task_description;
        $model->task_status = 'new';
        $model->created_at = date('Y-m-d H:i:s');
        $model->save();

        return redirect()->route('index');
    }

    public function edit_task($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $error) {
            return redirect()->route('index');
        }

        $model = new TaskModel();
        $task = $model->where('id', '=', $id)->first();

        if (empty($task)) {
            return redirect()->route('index');
        }

        $data = [
            'title' => $this->title,
            'task' => $task
        ];

        return view('edit_task_frm', $data);
    }

    public function edit_task_submit(Request $request)
    {
        $request->validate([
            'text_task_name' => 'required|min:3|max:200',
            'text_task_description' => 'required|min:3|max:1000',
            'text_task_status' => 'required'
        ], [
            'text_task_name.required' => 'O campo é obrigatório',
            'text_task_name.min' => 'O campo deve conter no mínimo :min caracteres',
            'text_task_name.max' => 'O campo deve conter no máximo :max caracteres',

            'text_task_description.required' => 'O campo é obrigatório',
            'text_task_description.min' => 'O campo deve conter no mínimo :min caracteres',
            'text_task_description.max' => 'O campo deve conter no máximo :max caracteres',

            'text_task_status.required' => 'O campo é obrigatório',
        ]);

        $task_id = Crypt::decrypt($request->input('task_id'));
        $task_name = $request->input('text_task_name');
        $task_description = $request->input('text_task_description');
        $task_status = $request->input('text_task_status');

        $model = new TaskModel();
        $exists = $model->where('id_user', '=', session()->get('id'))
            ->where('task_name', '=', $task_name)
            ->where('id', '!=', $task_id)
            ->whereNull('deleted_at')
            ->first();

        if ($exists) {
            return redirect()->route('edit_task', ['id' => Crypt::encrypt($task_id)])
                ->with('task_error', 'Já existe uma tarefa com esse nome');
        }


        $model->where('id', '=', $task_id)
            ->update([
                'task_name' => $task_name,
                'task_description' => $task_description,
                'task_status' => $task_status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return redirect()->route('index');
    }

    public function delete_task($id)
    {

        try {
            $id = Crypt::decrypt($id);
        } catch (\Exception $error) {
            return redirect()->route('index');
        }

        $model = new TaskModel();
        $task = $model->where('id', '=', $id)->first();

        if (empty($task)) {
            return redirect()->route('index');
        }

        $data = [
            'title' => $this->title,
            'task' => $task
        ];

        return view('delete_task', $data);
    }

    public function delete_task_confirm($id)
    {

        $id_task = null;
        try {
            $id_task = Crypt::decrypt($id);
        } catch (\Exception $error) {
            return redirect()->route('index');
        }


        $model = new TaskModel();
        $model->where('id', '=', $id_task)->update([
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->route('index');
    }

    // search tasks

    public function search_submit(Request $request)
    {
        $search = $request->input('text_search');

        $model = new TaskModel();
        if ($search == '') {
            $tasks = $model->where('id_user', '=', session("id"))
                ->whereNull('deleted_at')
                ->get();
        } else {
            $tasks = $model
                ->where('id_user', '=', session("id"))
                ->whereNull('deleted_at')
                ->where(function ($query) use ($search) {
                    $query->where('task_name', 'like', '%' . $search . '%')
                        ->orWhere('task_description', 'like', '%' . $search . '%');
                })
                ->get();
        }

        session()->put('tasks', $tasks);
        session()->put('search', $search);

        return redirect()->route("index");
    }

    public function filter($status)
    {
        try {
            $status = Crypt::decrypt($status);
        } catch (\Exception $error) {
            return redirect()->route('index');
        }

        $model = new TaskModel();
        if ($status == 'all') {
            $tasks = $model->where("id_user", '=', session('id'))
                ->whereNull('deleted_at')
                ->get();
        } else {
            $tasks = $model->where("id_user", '=', session('id'))
                ->where("task_status", '=', $status)
                ->whereNull('deleted_at')
                ->get();
        }

        session()->put('tasks', $tasks);
        session()->put('filter', $status);

        return redirect()->route("index");
    }


    // private methods

    private function _get_tasks($tasks)
    {

        $collection = [];
        foreach ($tasks as $task) {

            $link_edit = '<a href="' . route('edit_task', ['id' => Crypt::encrypt($task->id)]) . '" class="btn btn-secondary m-1"><i class="bi bi-pencil-square"></i></a>';
            $link_delete = '<a href="' . route('delete_task', ['id' => Crypt::encrypt($task->id)]) . '" class="btn btn-danger"><i class="bi bi-trash"></i></a>';

            $collection[] = [
                'task_name' => '<span class="task-title">' . $task->task_name . '</span><br><small class="opacity-50">' . $task->task_description .  '</small>',
                'task_status' => $this->_status_name($task->task_status),
                'task_actions' => $link_edit . $link_delete
            ];
        }

        return $collection;
    }

    private function _status_name($status)
    {
        $status_collection = [
            'new' => 'Nova',
            'in_progress' => 'Em progresso',
            'cancelled' => 'Cancelada',
            'completed' => 'Concluída'
        ];


        if (key_exists($status, $status_collection)) {
            return '<span class="' . $this->_status_badge($status) . '">' . $status_collection[$status] . '</span>';
        }
    }

    private function _status_badge($status)
    {

        $status_collection = [
            'new' => 'badge bg-primary p-2',
            'in_progress' => 'badge bg-success p-2',
            'cancelled' => 'badge bg-danger p-2',
            'completed' => 'badge bg-secondary p-2'
        ];

         if (key_exists($status, $status_collection)) {
            return $status_collection[$status];
        }

    }
}
