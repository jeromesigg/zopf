<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    //
    protected $fillable = [
    'name', 'group_id', 'year', 'action_status_id', 'address_id', 'APIKey', 'SmartsuppToken'
    ];
    
    public function group(){
        return $this->belongsTo('App\Group');
    }
    
    public function action_status(){
        return $this->belongsTo('App\ActionStatus');
    }    

    public function orders(){
        return $this->hasMany('App\Order');
    }  

    public function addresses(){
        return $this->belongsToMany('App\Address', 'orders');
    }  

    public function center(){
        return $this->belongsTo('App\Address','address_id');
    } 
}
