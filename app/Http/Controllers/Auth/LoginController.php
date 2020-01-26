<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Mail;
use DateTime;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    public function login(Request $request) {

        $credentials = [
            'email' => $request['email'],
            'password' => $request['password'],
        ];

        if (Auth::attempt($credentials)) {
            return response()->json(['api_token' => Auth::user()->api_token, 'access' => Auth::user()->id_access_code], 200);
        } else {
            return response()->json(['status' => 'error'], 200);
        }
    }

    protected function userInfo(Request $request) {
        $user = DB::table('users')
            ->join('access_code', 'users.id_access_code', '=', 'access_code.id')
            ->where('api_token', '=', $request['api_token'])->get();
        return $user;
    }

    protected function userInfoForShop(Request $request) {
        $user = DB::table('users')
            ->where('api_token', '=', $request['api_token'])->get();
        $delivery = DB::table('users')
            ->join('temporary_data', 'users.id', '=', 'temporary_data.id_user')
            ->where('api_token', '=', $request['api_token'])->get();

        $searchUserJson = json_decode($user , true);

        $date = new DateTime();

        if ($searchUserJson[0]['last_bonus'] != null) {
            if ($searchUserJson[0]['last_bonus'] != $date->format("Y")) {
                if ($searchUserJson[0]['birth_date'] == $date->format("Y-m-d")) {
                    $addBonusValue = $searchUserJson[0]['bonus'] + 100; 
                    
                    DB::table('users')
                        ->where('api_token', '=', $request['api_token'])
                        ->update(array('bonus' => $addBonusValue, 'last_bonus' => $date->format("Y")));
                    
                }
            }
        }

        if ($searchUserJson[0]['last_bonus'] == null) {
            if ($searchUserJson[0]['birth_date'] == $date->format("Y-m-d")) {
                $addBonusValue = $searchUserJson[0]['bonus'] + 100;  
                
                DB::table('users')
                        ->where('api_token', '=', $request['api_token'])
                        ->update(array('bonus' => $addBonusValue, 'last_bonus' => $date->format("Y")));
            }
        }

        $user = DB::table('users')
            ->where('api_token', '=', $request['api_token'])->get();

        $searchUserJson = json_decode($user , true);

        $orders = DB::table('orders')
            ->where('id_user', '=', $searchUserJson[0]['id'])->orderBy('id_order', 'DESC')->get();
        $goodsInBasket = DB::table('basket')
            ->where('id_user', '=', $searchUserJson[0]['id'])->get();

        $date = new DateTime();
        
        if (count($goodsInBasket) == 1) {
            $jsonBasket = json_decode($goodsInBasket , true);
            $goodsInBasket = DB::table('basket_goods')
                ->where('id_number_order', '=', $jsonBasket[0]['id'])
                ->get()->count();
            return response()->json(['user' => $user, 'delivery' => $delivery, 'orders' => $orders, 'goodsInBasket' => $goodsInBasket], 200);
        } else {
            return response()->json(['user' => $user, 'delivery' => $delivery, 'orders' => $orders, 'goodsInBasket' => 0], 200);
        }
    }

    protected function changePassword(Request $request) {

        $credentials = [
            'email' => $request['email'],
            'password' => $request['password_old'],
        ];

        if (Auth::attempt($credentials)) {
            DB::table('users')
            ->where('password', '=', Auth::user()->password)
            ->update(array('password' => Hash::make($request['password_new'])));
            return Response::json(['status' => 'Successfully'], 200);
        } else {
            return response()->json(['status' => 'Error'], 200);
        }
    }

    protected function changeEmail(Request $request) {
        $testEmail = DB::table('users')
            ->where('email', '=', $request['email_old'])->get();
        if (count($testEmail) == 1) {
            DB::table('users')
                ->where('email', '=', $request['email_old'])
                ->update(array('email' => $request['email_new']));
            return Response::json(['status' => 'Successfully'], 200);
        } else {
            return Response::json(['status' => 'Error'], 200);
        }
    }

    protected function changeDeliveryData(Request $request) {
        $searchUser = DB::table('users')
            ->where('email', '=', $request['email'])->get();
        $searchUserJson = json_decode($searchUser , true);

        $testDataUser  = DB::table('temporary_data')
            ->where('id_user', '=', $searchUserJson[0]['id'])->get();

        if (count($testDataUser) == 0) {
            DB::table('temporary_data')->insert([
                'city' => $request['cityUser'],
                'address' => $request['addressUser'],
                'region' => $request['regionUser'],
                'flat' => $request['flatUser'],
                'index' => $request['indexUser'],
                'id_user' => $searchUserJson[0]['id'],
            ]);
            return Response::json(['status' => 'Successfully'], 200);
        } else {
            DB::table('temporary_data')
            ->where('id_user', '=', $searchUserJson[0]['id'])
            ->update(['city' => $request['cityUser'],
                'address' => $request['addressUser'],
                'region' => $request['regionUser'],
                'flat' => $request['flatUser'],
                'index' => $request['indexUser'],
            ]);
            return Response::json(['status' => 'Successfully'], 200);
        }
    }

    protected function changeDataUser(Request $request) {
        DB::table('users')
        ->where('email', '=', $request['email'])
        ->update(['surname' => $request['surnameUser'],
            'name' => $request['nameUser'],
            'last_name' => $request['last_nameUser'],
            'birth_date' => $request['birth_day'],
            'phone' => $request['userPhone'],
        ]);
        return Response::json(['status' => 'Successfully'], 200);
    }

    protected function updatePhoto(Request $request) {
        DB::table('users')
        ->where('email', '=', $request['email'])
        ->update(['photo' => $request['photo']]);
        return Response::json(['status' => 'Successfully'], 200);
    }

    protected function returnPassword(Request $request) {
        $email = DB::table('users')
        ->where('email', '=', $request['email'])->get();

        if (count($email) == 0) {
            return Response::json(['status' => 'Error'], 200);
        } else {
            $password = Str::random(10);

            DB::table('users')
                ->where('email', '=', $request['email'])
                ->update(array('password' => Hash::make($password)));
            
            $email = $request['email'];

            $data = array(
                'email' => $request['email'],
                'password' => $password
            );

            Mail::send(['text' => 'password'], ['data' => $data], function($message) use ($data) {
                $message->to($data['email'], 'Восстановление пароля')->subject('Восстановление пароля');
                $message->from('support@чайна-я.рф', 'Служба поддержки');
            });
        }
    }
}
