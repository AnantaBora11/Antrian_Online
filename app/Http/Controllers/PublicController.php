<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layanan;
use App\Models\Antrian;
use Carbon\Carbon;

class PublicController extends Controller
{
    public function index()
    {
        $layanan = Layanan::where('aktif', true)->get();
        return view('public.index', compact('layanan'));
    }

    /**
     * API endpoint untuk data antrian realtime.
     */
    public function apiAntrian()
    {
        $today = Carbon::today();
        $layanan = Layanan::where('aktif', true)->with(['lokets' => function ($q) {
            $q->where('aktif', true);
        }])->get();

        $data = $layanan->map(function ($l) use ($today) {
            // Antrian yang sedang dipanggil/dilayani
            $dipanggil = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->whereIn('status', ['dipanggil', 'dilayani'])
                ->with('loket')
                ->orderBy('waktu_dipanggil', 'desc')
                ->first();

            // Daftar antrian menunggu
            $menunggu = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->where('status', 'menunggu')
                ->orderBy('created_at', 'asc')
                ->get(['id', 'kode_antrian', 'status', 'created_at']);

            // Total antrian hari ini
            $totalHariIni = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->count();

            // Total selesai hari ini
            $totalSelesai = Antrian::where('layanan_id', $l->id)
                ->where('tanggal', $today)
                ->where('status', 'selesai')
                ->count();

            return [
                'id' => $l->id,
                'kode' => $l->kode,
                'nama' => $l->nama,
                'deskripsi' => $l->deskripsi,
                'dipanggil' => $dipanggil ? [
                    'kode_antrian' => $dipanggil->kode_antrian,
                    'loket' => $dipanggil->loket ? $dipanggil->loket->nama : '-',
                    'status' => $dipanggil->status,
                ] : null,
                'menunggu' => $menunggu,
                'total_hari_ini' => $totalHariIni,
                'total_selesai' => $totalSelesai,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Ambil nomor antrian baru.
     */
    public function ambilAntrian(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
        ]);

        $layanan = Layanan::findOrFail($request->layanan_id);
        $today = Carbon::today();

        // Hitung nomor antrian berikutnya
        $lastAntrian = Antrian::where('layanan_id', $layanan->id)
            ->where('tanggal', $today)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastAntrian
            ? intval(substr($lastAntrian->kode_antrian, 2)) + 1
            : 1;

        $kodeAntrian = $layanan->kode . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $antrian = Antrian::create([
            'kode_antrian' => $kodeAntrian,
            'layanan_id' => $layanan->id,
            'status' => 'menunggu',
            'tanggal' => $today,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'kode_antrian' => $antrian->kode_antrian,
                'layanan' => $layanan->nama,
                'nomor' => $nextNumber,
            ],
        ]);
    }
}
