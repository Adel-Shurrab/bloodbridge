<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = ['name'];

    public function donors()
    {
        return $this->hasMany(Donor::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
