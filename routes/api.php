<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Контроллеры авторизации, регистрации и пользователя

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/register', 'RegisterController@create');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/login', 'LoginController@login');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/user', 'LoginController@userInfo');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/user-shop', 'LoginController@userInfoForShop');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/user-access', 'LoginController@userAccess');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/new-password', 'LoginController@changePassword');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/change-email', 'LoginController@changeEmail');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/change-personal-data', 'LoginController@changeDataUser');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/change-delivery-data', 'LoginController@changeDeliveryData');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/update-photo', 'LoginController@updatePhoto');
});

Route::group(['namespace' => 'Auth'], function()
{
    Route::post('/return-password', 'LoginController@returnPassword');
});

//Контроллеры панели главного администратора

Route::group(['namespace' => 'Administrator'], function()
{
    Route::post('/admin-panel', 'AdminController@adminPanel');
});

Route::group(['namespace' => 'Administrator'], function()
{
    Route::post('/delete-admin', 'AdminController@adminDelete');
});

Route::group(['namespace' => 'Administrator'], function()
{
    Route::post('/test-auth', 'AdminController@authTest');
});

Route::group(['namespace' => 'Administrator'], function()
{
    Route::post('/new-admin', 'AdminController@addAdmin');
});


//Контроллеры заказов

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/orders', 'OrdersControllers@getOrders');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/last-orders', 'OrdersControllers@getOrdersMinimal');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/order/{id}', 'OrdersControllers@getOrder');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/delete-order', 'OrdersControllers@deleteOrder');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/panel-action', 'OrdersControllers@panelAction');
});

//Контроллер панели информации

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/panel-info', 'DataController@panelInformation');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/add-trackcode', 'OrdersControllers@addTrackCode');
});


//Контроллеры товаров

Route::group(['namespace' => 'PanelData'], function()
{
    Route::get('/goods', 'DataController@goods');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::get('/last-new-goods', 'GoodsController@lastNewGoogs');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::get('/shop-goods', 'GoodsController@allGoods');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::get('/goods/{id}', 'GoodsController@getProduct');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/create-goods', 'GoodsController@setProduct');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/add-quantity', 'GoodsController@addQuantityGood');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/delete-good/{id}', 'GoodsController@deleteProduct');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/update-goods/', 'GoodsController@updateProduct');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/update-status', 'GoodsController@updateStatusProduct');
});

//Контроллеры категорий

Route::group(['namespace' => 'PanelData'], function()
{
    Route::get('/categories', 'DataController@categories');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/delete-categories/{id}', 'CategoriesController@deleteCategories');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/new-categories', 'CategoriesController@setCategories');
});

// Контроллеры статусов

Route::group(['namespace' => 'PanelData'], function()
{
    Route::get('/status-product', 'DataController@statusProduct');
});

// Контроллеры промокодов

Route::group(['namespace' => 'PanelData'], function()
{
    Route::get('/sale', 'DataController@sale');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/delete-sale/{id}', 'PromoCodeControllers@deletePromoCode');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/new-sale', 'PromoCodeControllers@setPromoCode');
});

//Контроллеры корзины

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/add-basket', 'BasketController@addInBasketGood');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/delete-basket', 'BasketController@deleteFromBasketGood');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/basket', 'BasketController@getGoodsFromBasket');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/all-delete-basket', 'BasketController@deleteAllBasket');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/order-payment', 'BasketController@orderPayment');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/promocode', 'BasketController@activedPromoCode');
});

Route::group(['namespace' => 'PanelData'], function()
{
    Route::post('/order-registration', 'BasketController@orderRegistration');
});




