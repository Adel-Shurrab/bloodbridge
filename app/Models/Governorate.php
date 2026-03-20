<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Governorate extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];

    public array $translatable = ['name'];

    public function donors()
    {
        return $this->hasMany(Donor::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
