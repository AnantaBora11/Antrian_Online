<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Layanan;
use App\Models\Loket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $layanan = Layanan::where('aktif', true)->get()->map(function ($l) use ($today) {
            $l->antrian_dipanggil = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->whereIn('status', ['dipanggil', 'dilayani'])
                ->with('loket')
                ->orderBy('waktu_dipanggil', 'desc')
                ->first();

            // Antrian sebelumnya (terakhir selesai/batal) untuk tombol Prev
            $l->antrian_sebelumnya = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->whereIn('status', ['selesai', 'batal'])
                ->orderBy('waktu_selesai', 'desc')
                ->first();

            $l->jumlah_menunggu = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->where('status', 'menunggu')
                ->count();

            $l->jumlah_selesai = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->where('status', 'selesai')
                ->count();

            $l->total_hari_ini = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->count();

            return $l;
        });

        // Daftar antrian hari ini untuk tabel
        $antrian = Antrian::where('tanggal', $today)
            ->with(['layanan', 'loket', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.index', compact('layanan', 'antrian'));
    }

    /**
     * Panggil antrian berikutnya untuk layanan tertentu.
     */
    public function panggilBerikutnya($layananId)
    {
        $today = Carbon::today();
        $layanan = Layanan::findOrFail($layananId);

        // Selesaikan antrian yang sedang dipanggil/dilayani
        Antrian::where('layanan_id', $layananId)
            ->where('tanggal', $today)
            ->whereIn('status', ['dipanggil', 'dilayani'])
            ->update([
                'status' => 'selesai',
                'waktu_selesai' => now(),
            ]);

        // Cari antrian menunggu berikutnya
        $next = Antrian::where('layanan_id', $layananId)
            ->where('tanggal', $today)
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($next) {
            // Cari loket untuk layanan ini
            $loket = Loket::where('layanan_id', $layananId)
                ->where('aktif', true)
                ->first();

            $next->update([
                'status' => 'dipanggil',
                'loket_id' => $loket ? $loket->id : null,
                'user_id' => auth()->id(),
                'waktu_dipanggil' => now(),
            ]);

            return back()->with('success', "Memanggil antrian {$next->kode_antrian}");
        }

        return back()->with('info', "Tidak ada antrian menunggu untuk {$layanan->nama}");
    }

    /**
     * Panggil ulang antrian sebelumnya (prev).
     */
    public function panggilSebelumnya($layananId)
    {
        $today = Carbon::today();
        $layanan = Layanan::findOrFail($layananId);

        // Kembalikan antrian yang sedang aktif ke status menunggu
        $current = Antrian::where('layanan_id', $layananId)
            ->where('tanggal', $today)
            ->whereIn('status', ['dipanggil', 'dilayani'])
            ->first();

        if ($current) {
            $current->update([
                'status' => 'menunggu',
                'loket_id' => null,
                'user_id' => null,
                'waktu_dipanggil' => null,
            ]);
        }

        // Cari antrian terakhir yang selesai/batal, panggil ulang
        $prev = Antrian::where('layanan_id', $layananId)
            ->where('tanggal', $today)
            ->whereIn('status', ['selesai', 'batal'])
            ->orderBy('waktu_selesai', 'desc')
            ->first();

        if ($prev) {
            $loket = Loket::where('layanan_id', $layananId)
                ->where('aktif', true)
                ->first();

            $prev->update([
                'status' => 'dipanggil',
                'loket_id' => $loket ? $loket->id : null,
                'user_id' => auth()->id(),
                'waktu_dipanggil' => now(),
                'waktu_selesai' => null,
            ]);

            return back()->with('success', "Memanggil ulang antrian {$prev->kode_antrian}");
        }

        return back()->with('info', "Tidak ada antrian sebelumnya untuk {$layanan->nama}");
    }

    /**
     * Selesaikan antrian tertentu.
     */
    public function selesaikan($antrianId)
    {
        $antrian = Antrian::findOrFail($antrianId);
        $antrian->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        return back()->with('success', "Antrian {$antrian->kode_antrian} telah selesai.");
    }

    /**
     * Batalkan antrian tertentu.
     */
    public function batalkan($antrianId)
    {
        $antrian = Antrian::findOrFail($antrianId);
        $antrian->update([
            'status' => 'batal',
            'waktu_selesai' => now(),
        ]);

        return back()->with('success', "Antrian {$antrian->kode_antrian} telah dibatalkan.");
    }

    /**
     * List semua antrian hari ini.
     */
    public function listAntrian()
    {
        $today = Carbon::today();
        $antrian = Antrian::where('tanggal', $today)
            ->with(['layanan', 'loket', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();

        $layanan = Layanan::where('aktif', true)->get();

        return view('admin.antrian', compact('antrian', 'layanan'));
    }
}
