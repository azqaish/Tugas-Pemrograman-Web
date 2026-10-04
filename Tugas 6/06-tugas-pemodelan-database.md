1. Desain ERD 

Sistem E-Library Kampus digunakan untuk mencatat data mahasiswa, buku, penerbit, serta transaksi peminjaman dan pengembalian buku.

Entitas yang Digunakan:

1. **Mahasiswa**
2. **Buku**
3. **Penerbit**
4. **Transaksi Peminjaman**

Relasi Antar Entitas

1. Satu **mahasiswa** dapat melakukan banyak transaksi peminjaman.
2. Satu **buku** dapat dipinjam berkali-kali melalui transaksi yang berbeda.
3. Satu **penerbit** dapat menerbitkan banyak buku.
4. Setiap **buku** diterbitkan oleh satu penerbit.
5. Setiap **transaksi peminjaman** dilakukan oleh satu mahasiswa untuk satu buku.

| Relasi | Kardinalitas | Keterangan |
|---|---|---|
| Mahasiswa - Transaksi Peminjaman | 1 : N | Satu mahasiswa dapat memiliki banyak transaksi |
| Buku - Transaksi Peminjaman | 1 : N | Satu buku dapat muncul dalam banyak transaksi |
| Penerbit - Buku | 1 : N | Satu penerbit dapat menerbitkan banyak buku |


2. Identifikasi Atribut

a. Entitas Mahasiswa

| Atribut | Keterangan | Key |
|---|---|---|
| nim | Nomor induk mahasiswa | PK |
| nama_mahasiswa | Nama lengkap mahasiswa | - |
| alamat | Alamat mahasiswa | - |
| no_telepon | Nomor telepon mahasiswa | - |

Primary Key: nim

b. Entitas Penerbit

| Atribut | Keterangan | Key |
|---|---|---|
| id_penerbit | Identitas unik penerbit | PK |
| nama_penerbit | Nama penerbit | - |
| alamat_penerbit | Alamat penerbit | - |

Primary Key: id_penerbit

c. Entitas Buku

| Atribut | Keterangan | Key |
|---|---|---|
| isbn | Nomor ISBN buku | PK |
| judul_buku | Judul buku | - |
| tahun_terbit | Tahun penerbitan buku | - |
| kategori | Kategori buku | - |
| id_penerbit | Identitas penerbit buku | FK |

Primary Key: isbn

Foreign Key: id_penerbit → penerbit.id_penerbit

d. Entitas Transaksi Peminjaman

| Atribut | Keterangan | Key |
|---|---|---|
| id_peminjaman | Identitas unik transaksi | PK |
| nim | Mahasiswa yang meminjam buku | FK |
| isbn | Buku yang dipinjam | FK |
| tanggal_pinjam | Tanggal buku dipinjam | - |
| tanggal_jatuh_tempo | Batas waktu pengembalian | - |
| tanggal_kembali | Tanggal buku dikembalikan | - |
| status | Status peminjaman | - |

Primary Key: id_peminjaman

Foreign Key:
- nim → mahasiswa.nim
- isbn → buku.isbn
