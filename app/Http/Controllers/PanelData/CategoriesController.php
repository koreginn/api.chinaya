<?php


namespace App\Http\Controllers\PanelData;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class CategoriesController extends Controller {

    static public function setCategories(Request $request) {

        if ($request['refers_category'] == 'Посуда') {
            $refers_category = 2;
        } else {
            $refers_category = 1;
        }

        DB::table('categories')->insert([
            'name_categories' => $request['name_categories'],
            'refers_category' => $refers_category
        ]);
        $updateListCategories = DB::table('categories')->get();
        return $updateListCategories;
    }

    static public function deleteCategories($categoriesID) {
        $product = DB::table('products')
            ->where('id_categories', '=', $categoriesID)->get();
        if (count($product)) {
            $returnData = array(
                'status' => 'error'
            );
            return Response::json($returnData, 200);
        } else {
            DB::table('categories')
                ->where('id', '=', $categoriesID)->delete();
            $updateListCategories = DB::table('categories')->get();
            return $updateListCategories;
        }
    }
}
