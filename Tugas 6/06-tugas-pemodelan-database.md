1. Desain ERD

    mahasiswa (1) $\rightarrow$ (N) peminjaman: Satu mahasiswa dapat melakukan banyak transaksi peminjaman (One-to-Many).

    penerbit (1) $\rightarrow$ (N) buku: Satu penerbit dapat menerbitkan banyak judul buku (One-to-Many).

    peminjaman (1) $\rightarrow$ (N) detail_peminjaman: Satu transaksi peminjaman dapat memuat beberapa item buku (One-to-Many).

    buku (1) $\rightarrow$ (N) detail_peminjaman: Satu buku dapat tercatat di banyak transaksi peminjaman (One-to-Many).