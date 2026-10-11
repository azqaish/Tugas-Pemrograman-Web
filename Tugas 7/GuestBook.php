<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/class/GuestBook.php';

function e(string $teks): string {
    return htmlspecialchars($teks, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function inputPost(string $kunci): string {
    $nilai = $_POST[$kunci] ?? '';
    return is_string($nilai) ? trim($nilai) : '';
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$old    = ['nama' => '', 'email' => '', 'pesan' => ''];
$sukses = $_SESSION['flash_sukses'] ?? null;   // pesan sukses sekali tampil
unset($_SESSION['flash_sukses']);

$guestBook = new GuestBook();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1) Periksa token CSRF
    $tokenKiriman = $_POST['csrf_token'] ?? '';
    if (!is_string($tokenKiriman) || !hash_equals($_SESSION['csrf_token'], $tokenKiriman)) {
        http_response_code(403);
        die('Permintaan ditolak: token CSRF tidak valid.');
    }

    // 2) Ambil & validasi masukan
    $old['nama']  = inputPost('nama');
    $old['email'] = inputPost('email');
    $old['pesan'] = inputPost('pesan');
    $errors = GuestBook::validasi($old['nama'], $old['email'], $old['pesan']);

    // 3) Simpan bila valid
    if (empty($errors)) {
        try {
            $guestBook->simpan($old['nama'], $old['email'], $old['pesan']);
            $_SESSION['flash_sukses'] = 'Terima kasih! Pesan Anda berhasil disimpan.';
            unset($_SESSION['csrf_token']);           // token sekali pakai
            header('Location: guestbook.php');        // cegah kirim ulang saat refresh
            exit;
        } catch (PDOException $ex) {
            error_log('GuestBook simpan error: ' . $ex->getMessage());
            $errors['umum'] = 'Maaf, pesan gagal disimpan. Silakan coba lagi.';
        }
    }
}

try {
    $daftarPesan = $guestBook->ambilSemua(50);
} catch (PDOException $ex) {
    error_log('GuestBook ambil error: ' . $ex->getMessage());
    $daftarPesan = [];
    $errors['umum'] = 'Daftar pesan tidak dapat dimuat saat ini.';
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Tamu Digital Perpustakaan</title>
</head>
<body>
    <h1>Buku Tamu Digital Perpustakaan</h1>
    <p>Silakan tinggalkan pesan atau kesan Anda.</p>

    <?php if ($sukses): ?>
        <div class="alert-ok"><?= e($sukses) ?></div>
    <?php endif; ?>
    <?php if (isset($errors['umum'])): ?>
        <div class="alert-err"><?= e($errors['umum']) ?></div>
    <?php endif; ?>

    <form method="post" action="guestbook.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">

        <label for="nama">Nama</label>
        <input type="text" id="nama" name="nama" value="<?= e($old['nama']) ?>">
        <?php if (isset($errors['nama'])): ?><div class="error"><?= e($errors['nama']) ?></div><?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($old['email']) ?>">
        <?php if (isset($errors['email'])): ?><div class="error"><?= e($errors['email']) ?></div><?php endif; ?>

        <label for="pesan">Pesan</label>
        <textarea id="pesan" name="pesan" rows="4"><?= e($old['pesan']) ?></textarea>
        <?php if (isset($errors['pesan'])): ?><div class="error"><?= e($errors['pesan']) ?></div><?php endif; ?>

        <button type="submit">Kirim Pesan</button>
    </form>

    <h2>Daftar Pesan</h2>
    <?php if (empty($daftarPesan)): ?>
        <p>Belum ada pesan.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>No</th><th>Nama</th><th>Email</th><th>Pesan</th><th>Tanggal</th></tr>
            </thead>
            <tbody>
                <?php foreach ($daftarPesan as $i => $baris): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($baris['nama']) ?></td>
                        <td><?= e($baris['email']) ?></td>
                        <td><?= nl2br(e($baris['pesan'])) ?></td>
                        <td><?= e(date('d-m-Y H:i', strtotime($baris['tanggal_kirim']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>