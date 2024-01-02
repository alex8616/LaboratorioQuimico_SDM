<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'code', 'features', 'renown','status', 'fecha', 'user_id', 'company_id'];


    //Relacion 1 a N Inversa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relacion 1 a N Inversa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    //Relacion N a N
    public function elements()
    {
        return $this->belongsToMany(Element::class)->withPivot('value');
    }
}
