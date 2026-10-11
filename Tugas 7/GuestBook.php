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