<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rota extends Model
{
    //
    protected $table = 'rota'; 
    
    public function shifts(){
        return $this->hasMany(RotaShift::class, 'rota_id', 'id');
    }
    public function rotaAssigns(){
        return $this->hasMany(RotaAssignEmployee::class, 'rota_id', 'id');
    }
}
