<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Main extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'olá laravel 10',
            'description' => 'aprendendo laravel'
        ];

        return view('main', $data);
    }

    public function users()
    {
        /* Uma forma para fazer o select */
        /* $users = DB::select('SELECT * FROM users');
        dd($users[0]); */


        /* Com query builder */
        /* $users = DB::table('users')->get();
        dd($users); */

        /* Para array associativo */
        /* $users = DB::table('users')->get()->toArray();
        dd($users); */

        /* Eloquent ORM */
        $model = new UserModel();
        $users = $model->all();

        foreach($users as $user){
            echo $user->username . '<br>';
        }
    }
}
