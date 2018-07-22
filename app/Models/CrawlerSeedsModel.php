<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
 * This model can works in two modes:
 * config/squareone.php -> fast_mode
 * false => Normal, like a traditional database model 
 * true => Attacks to REDIS, NOT mysql
 */
use Illuminate\Support\Facades\Redis;

class CrawlerSeedsModel extends Model
{
    protected $table = 'crawler_seeds';
    protected $fillable = ['url','date'];
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public static function nextSeed()
    {
        if(config('squareone.fast_mode')) {
            return Redis::spop(config('squareone.redis_var_seeds'));
        }
        else {
            $v_url = false;
            $v_seed = parent::first();
            if($v_seed) {
                $v_url = $v_seed->url;
                $v_seed->delete();
            }
            return $v_url;            
        }
    }
 
    public static function newSeeds($p_new_seeds=[])
    {
        if(count($p_new_seeds)==0) { return 0; }
        
        if(config('squareone.fast_mode')) {
            // To bulk insert we need phpredis extension installed, and i don't want complicate the deploy...
            // So, insert one by one...
            foreach($p_new_seeds as $v_seed) { Redis::sadd(config('squareone.redis_var_seeds'),$v_seed['url']); }
            return true;
        }
        else {
            return parent::insert($p_new_seeds);            
        }        
    }    
        

}