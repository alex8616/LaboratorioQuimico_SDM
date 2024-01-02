<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'delegate',
        'fax',
        'url',
        'face',
        'user_id'
    ];

    //Relacion 1 a N Inversa User
    public function user(){
        return $this->belongsTo(User::class);
    }

    //Relacion 1 a N
    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
