@extends('layouts.dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="admin-two-col">
    {{-- Kiri: Kontrol Antrian --}}
    <div class="col-left">
        <div class="dashboard-grid">
            @foreach($layanan as $l)
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3>{{ $l->nama }}</h3>
                    <span class="kode-badge">{{ $l->kode }}</span>
                </div>

                <div class="dash-card-body">
                    <div class="current-number">
                        <span class="current-label">Sedang Dipanggil</span>
                        @if($l->antrian_dipanggil)
                            <span class="current-value active">{{ $l->antrian_dipanggil->kode_antrian }}</span>
                            <span class="current-loket">{{ $l->antrian_dipanggil->loket->nama ?? '-' }}</span>
                        @else
                            <span class="current-value empty">—</span>
                            <span class="current-loket">Belum ada</span>
                        @endif
                    </div>

                    <div class="dash-stats">
                        <div class="dash-stat">
                            <span class="dash-stat-value">{{ $l->jumlah_menunggu }}</span>
                            <span class="dash-stat-label">Menunggu</span>
                        </div>
                        <div class="dash-stat">
                            <span class="dash-stat-value">{{ $l->jumlah_selesai }}</span>
                            <span class="dash-stat-label">Selesai</span>
                        </div>
                        <div class="dash-stat">
                            <span class="dash-stat-value">{{ $l->total_hari_ini }}</span>
                            <span class="dash-stat-label">Total</span>
                        </div>
                    </div>
                </div>

                <div class="dash-card-footer">
                    <form method="POST" action="{{ route('admin.panggil.prev', $l->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary" title="Prev" {{ $l->antrian_sebelumnya ? '' : 'disabled' }}>
                            ◀ Prev
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.panggil', $l->id) }}" style="flex:1;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block" {{ $l->jumlah_menunggu > 0 ? '' : 'disabled' }}>
                            Next ▶
                        </button>
                    </form>

                    @if($l->antrian_dipanggil)
                    <form method="POST" action="{{ route('admin.selesai', $l->antrian_dipanggil->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success" title="Selesai">✓</button>
                    </form>
                    <form method="POST" action="{{ route('admin.batal', $l->antrian_dipanggil->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger" title="Batal">✕</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Kanan: Daftar Antrian Masuk --}}
    <div class="col-right">
        <h3 class="col-right-title">Daftar Antrian Masuk</h3>
        <div class="antrian-card-list">
            @forelse($antrian as $a)
            <div class="antrian-item">
                <div class="antrian-item-left">
                    <span class="antrian-item-code">{{ $a->kode_antrian }}</span>
                    <span class="antrian-item-layanan">{{ $a->layanan->nama }}</span>
                </div>
                <div class="antrian-item-right">
                    <span class="status-badge status-{{ $a->status }}">{{ ucfirst($a->status) }}</span>
                    <span class="antrian-item-time">{{ $a->created_at->format('H:i') }}</span>
                </div>
            </div>
            @empty
            <div class="antrian-empty">Belum ada antrian hari ini.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
