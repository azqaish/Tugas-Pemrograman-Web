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