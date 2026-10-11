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