<?php


namespace App\Http\Controllers\PanelData;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class OrdersControllers extends Controller {

    public function getOrder($ordersID) {
        $orders = DB::table('orders')
            ->join('order_goods', 'orders.id_order', '=', 'order_goods.id_number_order')
            ->join('temporary_data', 'orders.id_td', '=', 'temporary_data.id')
            ->join('products', 'order_goods.id_product', '=', 'products.id_good')
            ->join('users', 'orders.id_user', '=', 'users.id')
            ->where('id_order', '=', $ordersID)->get();

        if (count($orders) == 0) {
            return Response::json(['status' => 'Error'], 200);
        } else {
            return $orders;
        }
    }

    public function getOrdersMinimal() {
        $orders = DB::table('orders')
            ->join('users', 'orders.id_user', '=', 'users.id')->latest()->limit(4)->get()->reverse();;
        return $orders;
    }

    public function deleteOrder(Request $request) {
        DB::table('order_goods')
            ->where('id_number_order', '=', $request['id_order'])->delete();
        DB::table('orders')
            ->where('id_order', '=', $request['id_order'])->delete();
         return $this->getOrders($request['id_order']);

    }

    public function addTrackCode(Request $request) {
        $checkTrackCode = DB::table('orders')
                    ->where('id_order', '=', $request['id_order'])
                    ->update(array('trackcode' => $request['trackcode']));

        if ($checkTrackCode == true) {
            return Response::json(['status' => 'Successfully'], 200);
        } else {
            return Response::json(['status' => 'Error'], 200);
        }

    }

    public function panelAction(Request $request) {
        switch ($request['select']) {
            case 9:
                DB::table('orders')
                    ->where('id_order', '=', $request['id_order'])
                    ->update(array('id_status' => 9));
                $scanned = DB::table('orders')->where('id_order', '=', $request['id_order'])->get();
                return $scanned;
            case 10:
                DB::table('orders')
                    ->where('id_order', '=', $request['id_order'])
                    ->update(array('id_status' => 10));
                $shipped = DB::table('orders')->where('id_order', '=', $request['id_order'])->get();
                return $shipped;
            case 11:
                DB::table('orders')
                    ->where('id_order', '=', $request['id_order'])
                    ->update(array('id_status' => 11));
                $delivered = DB::table('orders')->where('id_order', '=', $request['id_order'])->get();
                return $delivered;
            case 12:
                DB::table('orders')
                    ->where('id_order', '=', $request['id_order'])
                    ->update(array('id_status' => 12));
                $cancelled = DB::table('orders')->where('id_order', '=', $request['id_order'])->get();
                return $cancelled;
        }
    }

    static public function getOrders() {
        $orders = DB::table('orders')
            ->join('users', 'orders.id_user', '=', 'users.id')->orderBy('id_order', 'DESC')->get();
        return $orders;
    }
}
