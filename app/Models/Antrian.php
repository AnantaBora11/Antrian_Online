<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $table = 'antrian';

    protected $fillable = [
        'kode_antrian',
        'layanan_id',
        'loket_id',
        'user_id',
        'status',
        'waktu_dipanggil',
        'waktu_selesai',
        'tanggal',
    ];

    protected $casts = [
        'waktu_dipanggil' => 'datetime',
        'waktu_selesai' => 'datetime',
        'tanggal' => 'date',
    ];

    /**
     * Layanan dari antrian ini.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Loket yang melayani antrian ini.
     */
    public function loket()
    {
        return $this->belongsTo(Loket::class);
    }

    /**
     * Petugas yang memanggil antrian ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
