<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    // Generate a key from user's data (obviously this is a terrible idea but to technical test is good :))
    public function getKey($p_id_user=0)
    {
        $v_result_raw = parent::selectRaw('MD5(CONCAT(id,email,password)) as keygen')->
                                where('id',$p_id_user)->
                                first();
        return $v_result_raw->keygen;
    }     
    // Find an user with his key
    public function getUserFromKey($p_key='')
    {
        $v_user_data = false;
        $v_result_raw = parent::select(['id','name'])->
                                // Don't worry about sqlinject, $p_key come from url [0-9az]
                                whereRaw('MD5(CONCAT(id,email,password))="'.$p_key.'"')->
                                first();
        if($v_result_raw && isset($v_result_raw['id'])) {
            $v_user_data = $v_result_raw;
        }
        return $v_user_data;
    }     
    
}
