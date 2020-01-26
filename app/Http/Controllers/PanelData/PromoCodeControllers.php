<?php


namespace App\Http\Controllers\PanelData;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class PromoCodeControllers extends Controller {

    static public function setPromoCode(Request $request) {
        DB::table('sales')->insert([
            'name_sale' => $request['name_sale'],
            'value_sale' => $request['value_sale'],
        ]);
        $updateListPromoCode = DB::table('sales')->get();
        return $updateListPromoCode;
    }

    static public function deletePromoCode($promoCodeID) {
        DB::table('sales')
            ->where('id', '=', $promoCodeID)->delete();
        $updateListPromoCode = DB::table('sales')->get();
        return $updateListPromoCode;
    }
}
