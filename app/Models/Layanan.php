<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Loket yang melayani layanan ini.
     */
    public function lokets()
    {
        return $this->hasMany(Loket::class);
    }

    /**
     * Antrian untuk layanan ini.
     */
    public function antrians()
    {
        return $this->hasMany(Antrian::class);
    }
}
