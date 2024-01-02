<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'price',
    ];

    //Relacion N a N
    public function packages()
    {
        return $this->belongsToMany(Package::class)->withPivot('value');
    }
}
