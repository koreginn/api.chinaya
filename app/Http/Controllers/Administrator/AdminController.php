<?php


namespace App\Http\Controllers\Administrator;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller {

    static public function adminPanel(Request $request) {
        $admins = DB::table('users')->where('id_access_code', '>=', '2')->get();
        return $admins;
    }

    static public function authTest(Request $request) {
        $access = DB::table('users')->where('api_token', '=', $request['api_token'])->pluck('id_access_code');
        return response()->json(['access' => $access], 200);
    }

    static public function adminDelete(Request $request) {
        DB::table('users')
            ->where('id', '=', $request['user_id'])
            ->update(array('id_access_code' => 1));
        $admins = DB::table('users')->where('id_access_code', '>=', '2')->get();
        return $admins;
    }

    static  public  function addAdmin(Request $request) {
        DB::table('users')
            ->where('id', '=', $request['admin_id'])
            ->update(array('id_access_code' => 2));
        $admins = DB::table('users')->where('id_access_code', '>=', '2')->get();
        return $admins;
    }
}
