<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FavouriteModel extends Model
{
    
    protected $table = 'favourite';
    protected $fillable = ['user_id','product_id'];
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function __construct($p_ini_data=[])
    {
        parent::__construct($p_ini_data);
    }
    
    public static function add($p_favourite_id=0,$p_id_user=0)
    {
        return parent::updateOrCreate(
            ['user_id' => $p_id_user,'product_id' => $p_favourite_id],
            ['create_at' => DB::raw('NOW()')]
        );            
    }        
   
    public static function remove($p_favourite_id=0,$p_id_user=0)
    {
        return parent::where('user_id',$p_id_user)->
                       where('product_id',$p_favourite_id)->
                       delete();
    }    
    
    public static function getAllIDs($p_id_user=0)
    {
        $v_result_raw = parent::select('product_id')->
                                where('user_id',$p_id_user)->
                                get();
        $v_result = [];
        if($v_result_raw) {
           foreach($v_result_raw as $v_result_fav)  {
               $v_result[] = $v_result_fav['product_id'];
           }
        }
        return $v_result;
    }       
    
    public static function getAllProducts($p_id_user=0)
    {
        return parent::select('p.*')->
                       where('user_id',$p_id_user)->
                       join('products as p', function ($pObJoin) {
                        $pObJoin->on('p.id','=','product_id');
                       })->                
                       get();
    }      
    
    
}