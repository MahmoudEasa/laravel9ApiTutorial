<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = [
        'name_ar',
        'name_en',
        'active',
        'created_at',
        'updated_at',
    ];

    protected function scopeSelection(){
        $defaultLang = app()->getLocale();
        return $this::select('id', "name_$defaultLang as name", 'active', 'created_at', 'updated_at');
    }
}