<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $guarded = ['id'];

    public static function current(): self
    {
        return self::firstOrCreate([], ['name' => 'Nama sekolah belum diisi']);
    }
}
