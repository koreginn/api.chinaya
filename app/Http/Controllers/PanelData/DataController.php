<?php


namespace App\Http\Controllers\PanelData;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class DataController extends Controller {
    static public function goods() {
        $goods = DB::table('products')->get();
        return $goods;
    }

    static public function sale() {
        $sale = DB::table('sales')->get();
        return $sale;
    }

    static public function categories() {
        $categories = DB::table('categories')->get();
        return $categories;
    }

    static public function statusProduct() {
        $statusProduct = DB::table('status_product')->get();
        return $statusProduct;
    }

    static public function setBasket(Request $request) {
        DB::table('temporary_data')->insert([
                'city' => $request['cityUser'],
                'address' => $request['addressUser'],
                'region' => $request['regionUser'],
                'flat' => $request['flatUser'],
                'index' => $request['indexUser'],
                'id_user' => $searchUserJson[0]['id'],
        ]);
        return Response::json(['status' => 'Successfully'], 200);
    }

    static public function panelInformation() {
        $users = DB::table('users')->where('id_access_code', '=', '1')->count();
        $orders = DB::table('orders')->get()->count();
        $goods = DB::table('products')->get()->count();
        $admins = DB::table('users')->where('id_access_code', '>=', '2')->count();
        return response()->json(['users' => $users, 'orders' => $orders, 'goods' => $goods, 'admins' => $admins], 200);
    }
}
