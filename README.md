# Demo
https://github.com/user-attachments/assets/dbbe57f1-ce70-4c2f-8d43-df1ad916c2b8

# Database
Tabel users menyimpan data admin yang mengoperasikan sistem. Tabel layanan menyimpan jenis layanan (Poli Umum, Poli Gigi, Farmasi) dengan kode unik sebagai awalan nomor antrian. Tabel loket mencatat loket pelayanan yang terhubung ke layanan tertentu. Tabel antrian menyimpan data setiap nomor antrian beserta status, waktu, dan relasinya ke layanan, loket, dan user yang memanggil, data poli dan admin/petugas dimasukan lewat seeder.

Relasi:
- **Layanan → Loket (1:N)** — satu layanan bisa punya banyak loket.  
- **Layanan → Antrian (1:N)** — satu layanan menghasilkan banyak antrian.  
- **Loket → Antrian (1:N)** — mencatat antrian dilayani di loket mana.  
- **Users → Antrian (1:N)** — mencatat siapa yang memanggil antrian.

# Backend
- **Login** — Pakai Auth::attempt() bawaan Laravel. Admin masukkan email dan password, kalau cocok dibuatkan session, kalau salah muncul pesan error. Route admin dilindungi middleware auth jadi yang belum login langsung dilempar ke halaman login.
- **Next/Prev** — Next itu ambil antrian menunggu paling awal, ubah statusnya jadi dipanggil. Prev kebalikannya, ambil antrian terakhir yang sudah selesai / batal dan panggil ulang. Tombol otomatis disabled kalau datanya kosong
- **Daftar Antrian** — Query semua data dari tabel antrian yang tanggalnya hari ini, ditampilin langsung di dashboard dalam bentuk card yang menunjukkan kode antrian, layanan, status, dan waktunya. 

# Frontend
- **Menampilkan nomor antrian dan list** — Halaman publik menampilkan card per layanan berisi nomor antrian yang sedang dipanggil dan daftar antrian yang menunggu. Ada tombol "Ambil Antrian" untuk generate nomor baru lewat API.
- **Realtime tanpa refresh** — Pakai JavaScript fetch() yang polling ke endpoint /api/antrian setiap 3 detik. Data JSON yang balik langsung dipakai untuk update tampilan secara otomatis tanpa reload halaman.
- **Tampilan bersih dan responsif** — Pakai CSS biasa dengan layout grid yang otomatis menyesuaikan jumlah kolom berdasarkan lebar layar. Di desktop tampil beberapa kolom, di HP jadi satu kolom. Desain putih bersih tanpa gradasi atau animasi berlebihan.
