@extends('layouts.public')
@section('title', 'Antrian Online')

@section('content')
<div class="public-header">
    <h1>Antrian Online</h1>
    <div class="clock" id="clock"></div>
</div>

<div class="layanan-grid" id="layanan-grid">
    @foreach($layanan as $l)
    <div class="layanan-card" data-layanan-id="{{ $l->id }}">
        <div class="card-header">
            <h2>{{ $l->nama }}</h2>
            <span class="kode-badge">{{ $l->kode }}</span>
        </div>

        <div class="nomor-dipanggil">
            <div class="label">Nomor Antrian Saat Ini</div>
            <div class="nomor kosong" id="nomor-{{ $l->id }}">Belum ada</div>
            <div class="loket-info" id="loket-{{ $l->id }}"></div>
        </div>

        <div class="stats-row">
            <div class="stat">
                <div class="stat-value" id="menunggu-count-{{ $l->id }}">0</div>
                <div class="stat-label">Menunggu</div>
            </div>
            <div class="stat">
                <div class="stat-value" id="selesai-count-{{ $l->id }}">0</div>
                <div class="stat-label">Selesai</div>
            </div>
            <div class="stat">
                <div class="stat-value" id="total-count-{{ $l->id }}">0</div>
                <div class="stat-label">Total</div>
            </div>
        </div>

        <div class="waiting-list">
            <div class="list-title">Daftar Menunggu</div>
            <ul id="waiting-list-{{ $l->id }}">
                <li class="empty-state">Belum ada antrian</li>
            </ul>
        </div>

        <div class="card-footer">
            <button class="btn-ambil" onclick="ambilAntrian({{ $l->id }})">
                Ambil Nomor Antrian
            </button>
        </div>
    </div>
    @endforeach
</div>

<div class="public-footer">
    &copy; {{ date('Y') }} Antrian Online &mdash; Sistem Antrian Rumah Sakit
</div>

{{-- Modal Tiket --}}
<div class="modal-overlay" id="modal-tiket">
    <div class="modal-content">
        <div class="ticket-label">Nomor Antrian Anda</div>
        <div class="ticket-number" id="ticket-number">-</div>
        <div class="ticket-layanan" id="ticket-layanan">-</div>
        <button class="btn-close-modal" onclick="closeModal()">Tutup</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // === Waktu ===
    function updateClock() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const date = now.toLocaleDateString('id-ID', options);
        const time = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('clock').textContent = date + ' • ' + time;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // === Polling Antrian ===
    function fetchAntrian() {
        fetch('/api/antrian')
            .then(res => res.json())
            .then(result => {
                if (!result.success) return;
                result.data.forEach(layanan => {
                    updateCard(layanan);
                });
            })
            .catch(err => console.error('Gagal fetch antrian:', err));
    }

    function updateCard(layanan) {
        const nomorEl = document.getElementById('nomor-' + layanan.id);
        const loketEl = document.getElementById('loket-' + layanan.id);
        const menungguCount = document.getElementById('menunggu-count-' + layanan.id);
        const selesaiCount = document.getElementById('selesai-count-' + layanan.id);
        const totalCount = document.getElementById('total-count-' + layanan.id);
        const waitingList = document.getElementById('waiting-list-' + layanan.id);

        // Update nomor dipanggil
        if (layanan.dipanggil) {
            const prevText = nomorEl.textContent;
            nomorEl.textContent = layanan.dipanggil.kode_antrian;
            nomorEl.className = 'nomor aktif';
            loketEl.textContent = layanan.dipanggil.loket;

            // Animasi jika nomor berubah
            if (prevText !== layanan.dipanggil.kode_antrian) {
                nomorEl.classList.add('fade-in');
                setTimeout(() => nomorEl.classList.remove('fade-in'), 400);
            }
        } else {
            nomorEl.textContent = 'Belum ada';
            nomorEl.className = 'nomor kosong';
            loketEl.textContent = '';
        }

        // Update
        menungguCount.textContent = layanan.menunggu.length;
        selesaiCount.textContent = layanan.total_selesai;
        totalCount.textContent = layanan.total_hari_ini;

        // Update waiting list
        if (layanan.menunggu.length === 0) {
            waitingList.innerHTML = '<li class="empty-state">Belum ada antrian</li>';
        } else {
            waitingList.innerHTML = layanan.menunggu.map(a => {
                const waktu = new Date(a.created_at);
                const jam = waktu.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                return `<li>
                    <span class="waiting-code">${a.kode_antrian}</span>
                    <span class="waiting-time">${jam}</span>
                </li>`;
            }).join('');
        }
    }

    // Polling setiap 3 detik
    setInterval(fetchAntrian, 3000);
    fetchAntrian();

    // === Ambil Antrian ===
    function ambilAntrian(layananId) {
        const btn = document.querySelector(`[data-layanan-id="${layananId}"] .btn-ambil`);
        btn.disabled = true;
        btn.textContent = 'Mengambil...';

        fetch('/api/antrian/ambil', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ layanan_id: layananId })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                // Tampilkan modal tiket
                document.getElementById('ticket-number').textContent = result.data.kode_antrian;
                document.getElementById('ticket-layanan').textContent = result.data.layanan;
                document.getElementById('modal-tiket').classList.add('show');

                // Merefresh data secara langsung
                fetchAntrian();
            }
        })
        .catch(err => {
            console.error('Gagal ambil antrian:', err);
            alert('Gagal mengambil antrian. Silakan coba lagi.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Ambil Nomor Antrian';
        });
    }

    function closeModal() {
        document.getElementById('modal-tiket').classList.remove('show');
    }

    document.getElementById('modal-tiket').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection
