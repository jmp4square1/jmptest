<?php

use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client as GuzzleClient;

use Illuminate\Support\Facades\Redis;

function h_normalizePriceFromHTML($p_price_txt='')
{
    $v_results = [];
    $v_price = 0.0;
    // €1,199.95    
    preg_match_all('/[0-9\.]+/imu',$p_price_txt, $v_results);
    if(isset($v_results[0]) && count($v_results[0])>0) {
        $v_price = floatval(implode('',$v_results[0]));
    }
    return $v_price;    
}

function h_getImageAndSave($p_source_url='',$p_name='',$p_type='',$p_replace_if_exist=false)
{    
    if(strlen($p_source_url)>0) {
        // Always jpg format, browsers can show even if it is gif :)
        $v_final_name = $p_name . '_' . $p_type . '.jpg';           
        if(( ! Storage::disk('catalog')->exists($v_final_name) ) || $p_replace_if_exist) {  
            
            // If fast mode is on, we pass the job to NODEJS and continue with our task.
            if(config('squareone.fast_mode')) {
                $v_redis_data = [
                    'url' => $p_source_url,
                    'name' => $v_final_name
                ];
                Redis::publish( config('squareone.redis_channel_download_img') , json_encode($v_redis_data));
            }
            else {
                $v_getClient = new GuzzleClient();
                $v_image_response = $v_getClient->request('GET',$p_source_url);
                if($v_image_response->getStatusCode()==200) {         
                    $v_image_raw = $v_image_response->getBody();
                    $v_bytes =  Storage::disk('catalog')->put($v_final_name,$v_image_raw);
                    if($v_bytes===false) {
                         Storage::disk('catalog')->delete($v_final_name);
                    }
                }                   
            }         
        }
    }           
}

function h_getLocalProductImage($p_data=[],$p_type='')
{    
    // First, we try locate local image
    $v_final_name = str_slug($p_data->name) . '_' . $p_type . '.jpg';  
    if(Storage::disk('catalog')->exists($v_final_name)) {
        return Storage::disk('catalog')->url($v_final_name);
    }
    else {
        // May be... not downloaded yet, hotlinking :)
        if(strlen($p_data['url_img_'.$p_type])>0) {
            return $p_data['url_img_'.$p_type];
        }
        else {
            // No!? , ok ok...
            return false;
        }        
    }
}

function h_formatPrice($p_price=0.0,$p_decimal=2)
{
    return number_format($p_price,$p_decimal,'.',',');
}