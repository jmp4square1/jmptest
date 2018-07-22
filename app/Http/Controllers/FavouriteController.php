<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\FavouriteModel;
use App\User;

class FavouriteController extends Controller
{
    // Object used only with ajax call
    private $_ajaxResponse = ['result'=>false,'msg'=>''];
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('show');
    }    
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
        
        // Social links
        $v_key_user = Auth()->user()->getKey(Auth::id());
        $v_url_share = [
            'fb' => route('showfavourites',['key'=>$v_key_user]) ,
            'tw' => urlencode(__('products.LookMyFavourites') . ' ' . route('showfavourites',['key'=>$v_key_user])) ,
            'gp' => route('showfavourites',['key'=>$v_key_user]) ,
            'ln' => route('showfavourites',['key'=>$v_key_user])
        ];
        
        return view('favourites',[
            'vd_favourites' => FavouriteModel::getAllProducts(Auth::id()),
            'vd_url_share' => $v_url_share // To share with friends...
        ]);
    }
    
    // This view is for sharing with friends
    public function show($p_key)
    {
        $v_users = new User();
        $v_user_data = $v_users->getUserFromKey($p_key);
        if(!$v_user_data) { abort(404); }
        
        return view('favouritesfriends',[
            'vd_favourites' => FavouriteModel::getAllProducts($v_user_data['id']) ,
            'vd_name' => $v_user_data['name']
        ]);
    }    
    
    
    /*
     * Ajax call to add favourite
     */
    public function ajax_add(Request $p_request)
    {        
        $v_id_favourite = intval($p_request->input('id'));        
        $this->_ajaxResponse['id'] = $v_id_favourite;                        
        if(FavouriteModel::add($v_id_favourite,Auth::id())) {
            $this->_ajaxResponse['result'] = true;
            $this->_ajaxResponse['m'] = __('products.FavouriteAdd');
            // Cache rebuilding
            session(['favourites'=>FavouriteModel::getAllIDs(Auth::id())]);            
        }
        else {
            $this->_ajaxResponse['m'] = __('products.TryAgain');
        }
        return $this->_ajaxResponse;
    }
    
    /*
     * Ajax call to remove favourite
     */
    public function ajax_remove(Request $p_request)
    {        
        $v_id_favourite = intval($p_request->input('id'));
        $v_from_favourites = intval($p_request->input('infav'))==1;
                
        $this->_ajaxResponse['id'] = $v_id_favourite;
        $this->_ajaxResponse['from_favourites'] = $v_from_favourites;
        if(FavouriteModel::remove($v_id_favourite,Auth::id())) {            
            $this->_ajaxResponse['result'] = true;
            $this->_ajaxResponse['m'] = __('products.FavouriteRemove');
            // Cache rebuilding
            session(['favourites'=>FavouriteModel::getAllIDs(Auth::id())]);            
        }
        else {
            $this->_ajaxResponse['m'] = __('products.TryAgain');
        }
        return $this->_ajaxResponse;
    }    
    
}
