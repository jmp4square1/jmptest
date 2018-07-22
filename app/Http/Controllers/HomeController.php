<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\ProductModel;
use App\Models\FavouriteModel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $p_request)
    {    
        // If I logged , I need know my favourites to mark them, but... I can't making selects to mysql all time
        // Caching in session... primary of caching school!! :)
        $v_my_favourites = [];
        if(Auth::check()) {
            $v_my_favourites = session('favourites');
            if(!$v_my_favourites) {
                $v_my_favourites = FavouriteModel::getAllIDs(Auth::id());   
                session(['favourites'=>$v_my_favourites]);
            }
        }        
        
        // List products with query uri params...
        $v_list_param = [
            'field' => strtolower($p_request->query('field', 'name')) ,
            'order' => strtolower($p_request->query('order', 'asc')) ,
        ];
        $v_products = ProductModel::getPaginationList($v_list_param);
        $v_pagination_links = $v_products->appends($v_list_param)->links();
        
        $v_selection_text = '';        
        switch($v_list_param['field']) {
            case 'name':
                switch($v_list_param['order']) {
                    case 'asc':
                        $v_selection_text = __('products.OrderbyNameAsc');
                    break;
                    case 'desc':
                        $v_selection_text = __('products.OrderbyNameDesc');
                    break;
                };
            break;
            case 'price_now':
                switch($v_list_param['order']) {
                    case 'asc':
                        $v_selection_text = __('products.OrderbyPriceCheaper');
                    break;
                    case 'desc':
                        $v_selection_text = __('products.OrderbyPriceNotCheaper');
                    break;
                };                                
            break;
        };
        
        return view('home',[
            'vd_products' => $v_products,
            'vd_params' => $v_list_param ,
            'vd_myfavourites' => $v_my_favourites,
            'vd_total_products' => $v_products->total(),
            'vd_pagination_links' => $v_pagination_links,
            'vd_selection_text' => $v_selection_text,
            'vd_order_key' => $v_list_param['field'].'_'.$v_list_param['order']
        ]);
    }
}
