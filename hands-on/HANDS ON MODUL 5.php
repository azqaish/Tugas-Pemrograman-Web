<?php
declare(strict_types=1);
require_once './Student.php';

session_start();

// 1. Generate CSRF Token jika belum ada di session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$successMessage = '';

// 2. Pemrosesan HTTP POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verifikasi CSRF Token
    $postToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
        die('Kesalahan Keamanan: Token CSRF tidak cocok.');
    }

    // Mengambil dan mensanitasi input teks dasar
    $nim = trim($_POST['nim'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validasi NIM (Wajib angka dan panjang tepat 10 karakter)
    if (!preg_match('/^[0-9]{10}$/', $nim)) {
        $errors[] = 'NIM harus berupa angka sepanjang tepat 10 digit.';
    }

    // Validasi Nama (Tidak boleh kosong)
    if (empty($name)) {
        $errors[] = 'Nama lengkap mahasiswa tidak boleh kosong.';
    }

    // Validasi Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format alamat email tidak valid.';
    }

    // Jika tidak ada error, buat instansiasi objek dan simpan
    if (empty($errors)) {
        $student = new Student($nim, $name, $email);
        if ($student->saveToSession()) {
            $successMessage = 'Pendaftaran mahasiswa ' . htmlspecialchars($student->getName()) . ' berhasil disimpan!';
            // Regenerasi CSRF token setelah sukses untuk keamanan tambahan
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
?>