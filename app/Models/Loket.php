<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loket extends Model
{
    protected $table = 'loket';

    protected $fillable = [
        'nama',
        'layanan_id',
        'user_id',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Layanan yang dilayani oleh loket ini.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Petugas yang bertugas di loket ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Antrian yang dilayani di loket ini.
     */
    public function antrians()
    {
        return $this->hasMany(Antrian::class);
    }
}
