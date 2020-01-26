<?php


namespace App\Http\Controllers\PanelData;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class GoodsController extends Controller {
    public function getProduct($productID) {
        $product = DB::table('products')
            ->join('categories', 'products.id_categories', '=', 'categories.id')
            ->where('products.id_good', '=', $productID)->get();

        if (count($product) == 0) {
            return Response::json(['status' => 'Error'], 200);
        } else {
            return $product;
        }
    }

    public function updateStatusProduct(Request $request) {

        if ($request['id_status'] == "Обычный товар") {
            $id_status = 1;
        }

        if ($request['id_status'] == "Новинка") {
            $id_status = 2;
        }

        DB::table('products')
            ->where('id_good', '=', $request['id_good'])
            ->update(array('this_is_new' => $id_status));
        return $this->getProduct($request['id_good']);
    }

    static public function setProduct(Request $request) {

        if ($request['status'] == "Выберите тип товара") {
            return Response::json(['status' => 'Error'], 200);
        }

        if ($request['this_is_new'] == "Это новинка ?") {
            return Response::json(['status' => 'Error'], 200);
        }

        if ($request['id_categories'] == "Выберите категорию") {
            return Response::json(['status' => 'Error'], 200);
        }

        $newGood = DB::table('products')->insert([
            'title' => $request['title'],
            'description' => $request['description'],
            'quantity' => $request['quantity'],
            'price' => $request['price'],
            'photo' => $request['photo'],
            'status' => $request['status'],
            'this_is_new' => $request['this_is_new'],
            'id_categories' => $request['id_categories'],
        ]);

        if ($newGood == true) {
            return Response::json(['status' => 'Successfully'], 200);
        } else {
            return Response::json(['status' => 'Error'], 200);
        }
    }


    static public function deleteProduct($productID) {
        $product = DB::table('order_goods')
            ->where('id_product', '=', $productID)->get();
        if (count($product)) {
            return Response::json(['status' => 'error'], 200);
        } else {
            DB::table('products')
                ->where('id_good', '=', $productID)->delete();
            $updateListProduct = DB::table('products')->get();
            return $updateListProduct;
        }
    }

    static public function updateProduct(Request $request) {
        return DB::table('products')
            ->where('id', '=', $request['id'])
            ->update(['title' => $request['title'],
                'description' => $request['description'],
                'quantity' => $request['quantity'],
                'price' => $request['price'],
                'photo' => $request['photo'],
                'status' => $request['status'],
                'id_categories' => $request['id_categories'],
            ]);
    }

    public function lastNewGoogs(Request $request) {
        $lastProduct = DB::table('products')
            ->join('categories', 'products.id_categories', '=', 'categories.id')
            ->where([
                ['this_is_new', '=', '2'],
                ['quantity', '>', '0'],
            ])->limit(4)->get();
        return $lastProduct;
    }

    public function allGoods(Request $request) {
        $allGoods = DB::table('products')
            ->join('categories', 'products.id_categories', '=', 'categories.id')
            ->where('quantity', '>', 0)->get();
        return $allGoods;
    }

    public function addQuantityGood(Request $request) {
        $product = DB::table('products')
            ->where('id_good', '=', $request['id_good'])->get();

        $jsonProduct = json_decode($product , true);

        $newQuantity = $jsonProduct[0]['quantity'] + $request['quantity'];

        DB::table('products')
            ->where('id_good', '=', $request['id_good'])
            ->update(array('quantity' => $newQuantity));

        $product = DB::table('products')
            ->where('id_good', '=', $request['id_good'])->get();

        if ($product == true) {
            return $product;
        } else {
            return Response::json(['status' => 'Error'], 200);
        }
    }
}
