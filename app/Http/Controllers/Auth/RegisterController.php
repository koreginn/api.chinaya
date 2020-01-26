<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class RegisterController extends Controller
{

    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function create(Request $request) {
        $account = DB::table('users')
            ->where('email', '=', $request['email'])->get();
        if (count($account)) {
            return Response::json(['status' => 'Error'], 200);
        } else {
            $user = User::create([
                'email' => $request['email'],
                'surname' => $request['surname'],
                'name' => $request['name'],
                'last_name' => $request['last_name'],
                'password' => Hash::make($request['password']),
                'api_token' => Str::random(50),
                'id_access_code' => '1',
                'bonus' => '0'
            ]);
        }
    }
}
