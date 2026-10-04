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