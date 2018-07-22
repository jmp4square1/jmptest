<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $fillable = ['url_detail','url_img_base','name','url_img_logo','description','price_previous','price_now'];
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function __construct($p_ini_data=[])
    {
        parent::__construct($p_ini_data);
    }
    
    
    public static function updateData($p_products_data=[])
    {
        if(count($p_products_data)==0) { return 0; }
        foreach($p_products_data as $v_product_data) {
            parent::updateOrCreate(
                ['name' => $v_product_data['name']],$v_product_data
            );            
        }
    }    
    
    public static function getPaginationList($p_order=[])
    {
        return parent::select(['id','url_img_base','url_detail','name','description','price_previous','price_now'])->                                
                       orderBy($p_order['field'],$p_order['order'])->
                       paginate( config('squareone.catalog_n_products_x_page') );        
    }  
    
}