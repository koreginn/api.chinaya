<?php


namespace App\Http\Controllers\PanelData;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class BasketController extends Controller {

    protected function addInBasketGood(Request $request) {

        $checkBasket = DB::table('basket')
            ->where('id_user', '=', $request['id_user'])->get();

        if (count($checkBasket) == 0) {
            $createBasket = DB::table('basket')->insert([
                'id_user' => $request['id_user'],
                'total_price' => $request['total_price'],
            ]);

            if ($createBasket == true)  {
                $checkBasketTwo = DB::table('basket')
                    ->where('id_user', '=', $request['id_user'])->get();
                $jsonBasket = json_decode($checkBasketTwo , true);
                if (count($checkBasketTwo) == 1) {
                    DB::table('basket_goods')->insert([
                        'id_number_order' => $jsonBasket[0]['id'],
                        'id_product' => $request['id_product'],
                        'box' => $request['box'],
                        'gram' => $request['gram'],
                    ]);
                    return Response::json(['status' => 'Successfully'], 200);
                }
            } else {
                return Response::json(['status' => 'Error'], 200);
            }
        } else {
            $checkBasketTwo = DB::table('basket')
                ->where('id_user', '=', $request['id_user'])->get();
            $jsonBasket = json_decode($checkBasketTwo , true);

            $checkGoodInBasket = DB::table('basket_goods')
                ->where([
                    ['id_product', '=', $request['id_product']],
                    ['id_number_order', '=', $jsonBasket[0]['id']],
                ])->get();

            if (count($checkGoodInBasket) == 0) {
                DB::table('basket_goods')->insert([
                    'id_number_order' => $jsonBasket[0]['id'],
                    'id_product' => $request['id_product'],
                    'box' => $request['box'],
                    'gram' => $request['gram'],
                ]);
                $totalPrice = $jsonBasket[0]['total_price'] + $request['total_price'];

                DB::table('basket')
                    ->where('id', '=', $jsonBasket[0]['id'])
                    ->update(array('total_price' => $totalPrice));
                return Response::json(['status' => 'Successfully'], 200);
            } else {
                return Response::json(['status' => 'Error'], 200);
            }
        }
    }

    protected function deleteFromBasketGood(Request $request) {

        DB::table('basket_goods')->where([
            ['id_product', '=', $request['id_product']],
            ['id_number_order', '=', $request['id_number_order']],
        ])->delete();

        $checkBasketGoods = DB::table('basket_goods')
            ->where('id_number_order', '=', $request['id_number_order'])->get();

        if (count($checkBasketGoods) == 0) {    
            DB::table('basket')
                ->where('id', '=', $request['id_number_order'])->delete();
            return Response::json(['basket' => null, 'basket_orders' => null], 200);
        } else {
            $totalPrice = DB::table('basket')
                ->where('id', '=', $request['id_number_order'])->get();

            $jsonTotalPrice = json_decode($totalPrice , true);

            $newTotalPrice = $jsonTotalPrice[0]['total_price'] - $request['total_price'];

            DB::table('basket')
                ->where('id', '=', $request['id_number_order'])
                ->update(array('total_price' => $newTotalPrice));

            $updateListBasket = DB::table('basket')
                ->where('id', '=', $request['id_number_order'])->get();

            $updateListBasketGood = DB::table('basket_goods')
                ->join('products', 'basket_goods.id_product', '=', 'products.id_good')
                ->where('id_number_order', '=', $request['id_number_order'])->get();

            return Response::json(['basket' => $updateListBasket, 'basket_orders' => $updateListBasketGood], 200);
        }
    }

    
    protected function getGoodsFromBasket(Request $request) {
        $getBasket = DB::table('basket')
                ->where('id_user', '=', $request['id_user'])->get();

        if (count($getBasket) == 1) {    
            $jsonBasket = json_decode($getBasket , true);

            $getBasketGood = DB::table('basket_goods')
                ->join('products', 'basket_goods.id_product', '=', 'products.id_good')
                ->where('id_number_order', '=', $jsonBasket[0]['id'])->get();

            return Response::json(['basket' => $getBasket, 'basket_orders' => $getBasketGood], 200);
        } else {
            return Response::json(['status' => 'Empty'], 200);
        }
    }

    protected function deleteAllBasket(Request $request) {
        DB::table('basket_goods')
            ->where('id_number_order', '=', $request['id_number_order'])->delete();
        DB::table('basket')
            ->where('id', '=', $request['id_number_order'])->delete();
        return Response::json(['basket' => null, 'basket_orders' => null], 200);
    }

    protected function orderPayment(Request $request) {
        $basket = DB::table('basket')
            ->where('id', '=', $request['id_number_order'])->get();
        $basketGoods = DB::table('basket_goods')
            ->join('products', 'basket_goods.id_product', '=', 'products.id_good')
            ->where('id_number_order', '=', $request['id_number_order'])->get();
        $delivery = DB::table('temporary_data')
                ->where('id_user', '=', $request['id_user'])->get();
        
        if (count($delivery) == 0) {
            return Response::json(['basket' => $basket, 'basket_orders' =>  $basketGoods, 'delivery' => null], 200);
        } else {
            return Response::json(['basket' => $basket, 'basket_orders' =>  $basketGoods, 'delivery' => $delivery], 200);
        }
        
    }

    protected function activedPromoCode(Request $request) {
        $promocode = DB::table('sales')
            ->where('name_sale', '=', $request['name_sale'])->get();

        if (count($promocode) == 0) {
            return Response::json(['status' => 'Error'], 200);
        } else {
            $jsonSale = json_decode($promocode , true);
            return Response::json(['sale' => $jsonSale[0]['value_sale']], 200);
        }
    }

    protected function orderRegistration(Request $request) {

        $date = new DateTime();

        $delivery = DB::table('temporary_data')
            ->where('id_user', '=', $request['id_user'])->get();

        $jsonDelivery = json_decode($delivery , true);

        $inOrders = DB::table('orders')->insert([
            'id_status' => 7,
            'id_td' => $jsonDelivery[0]['id'],
            'id_user' => $request['id_user'],
            'way_delivery' => $request['way_delivery'],
            'total_price' => $request['total_price'],
            'sale' => $request['sale'],
            'created_at' => $date,
        ]);

        $orderNumber = DB::table('orders')
                ->where('id_user', '=', $request['id_user'])->get()->last();

        $basketOrders = DB::table('basket_goods')
            ->where('id_number_order', '=', $request['id_number_order'])->get();
        
        $jsonBasketOrders = json_decode($basketOrders , true);

        $value = count($basketOrders);

        for ($i = 0; $i < $value; $i++) {
            $addGoodInOrder = DB::table('order_goods')->insert([
                'id_number_order' => $orderNumber->id_order,
                'id_product' => $jsonBasketOrders[$i]['id_product'],
                'box' => $jsonBasketOrders[$i]['box'],
                'gram' => $jsonBasketOrders[$i]['gram'],
            ]);

            $product = DB::table('products')
                ->where('id_good', '=', $jsonBasketOrders[$i]['id_product'])->get();

            $jsonProduct = json_decode($product , true);

            $newGram = $jsonProduct[0]['quantity'] - $jsonBasketOrders[$i]['gram'];

            DB::table('products')
                ->where('id_good', '=', $jsonBasketOrders[$i]['id_product'])
                ->update(array('quantity' => $newGram));
        }

        DB::table('basket_goods')
            ->where('id_number_order', '=', $request['id_number_order'])->delete();

        DB::table('basket')
            ->where('id', '=', $request['id_number_order'])->delete();

        if ($request['newBonusValue'] == 0) {
            $newBonusValue = 0;
        } else {
            $newBonusValue = $request['originalBonusValue'] - $request['newBonusValue'];
        }

        $returnBonuses = ($request['total_price'] / 100) * 1;

        $newBonusValue += $returnBonuses;

        DB::table('users')
            ->where('id', '=', $request['id_user'])
            ->update(array('bonus' => $newBonusValue));

        if ($addGoodInOrder == true) {
            return Response::json(['status' => 'Successfully', 'orderValue' => $orderNumber->id_order], 200);
        } else {
            return Response::json(['status' => 'Error'], 200);
        }
    }
}
