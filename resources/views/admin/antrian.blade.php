@extends('layouts.dashboard')
@section('page-title', 'Daftar Antrian')

@section('content')
<div class="antrian-header">
    <h2>Antrian Hari Ini</h2>
    <span class="antrian-date">{{ now()->translatedFormat('l, d F Y') }}</span>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Antrian</th>
                <th>Layanan</th>
                <th>Status</th>
                <th>Loket</th>
                <th>Waktu Ambil</th>
                <th>Waktu Dipanggil</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($antrian as $i => $a)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $a->kode_antrian }}</strong></td>
                <td>{{ $a->layanan->nama }}</td>
                <td>
                    <span class="status-badge status-{{ $a->status }}">
                        {{ ucfirst($a->status) }}
                    </span>
                </td>
                <td>{{ $a->loket->nama ?? '-' }}</td>
                <td>{{ $a->created_at->format('H:i') }}</td>
                <td>{{ $a->waktu_dipanggil ? $a->waktu_dipanggil->format('H:i') : '-' }}</td>
                <td class="action-cell">
                    @if($a->status === 'menunggu' || $a->status === 'dipanggil')
                    <div class="action-buttons">
                        @if($a->status === 'dipanggil')
                        <form method="POST" action="{{ route('admin.selesai', $a->id) }}">
                            @csrf
                            <button type="submit" class="btn-sm btn-success" title="Selesai">✓</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.batal', $a->id) }}">
                            @csrf
                            <button type="submit" class="btn-sm btn-danger" title="Batal">✕</button>
                        </form>
                    </div>
                    @elseif($a->status === 'selesai')
                        <span class="text-muted">—</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="empty-row">Belum ada antrian hari ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
