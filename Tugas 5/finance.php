<?php
declare(strict_types=1);

require_once __DIR__ . '/Transaction.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 100000.0;
}

if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $postToken)) {
        die('Kesalahan Keamanan: Token CSRF tidak valid atau tidak cocok.');
    }

    $typeRaw = trim($_POST['type'] ?? '');
    $amountRaw = trim($_POST['amount'] ?? '');

    $type = match ($typeRaw) {
        'deposit', 'withdrawal' => $typeRaw,
        default => null
    };

    if ($type === null) {
        $errors[] = 'Jenis transaksi tidak valid. Pilih antara deposit atau penarikan (withdrawal).';
    }

    if (!is_numeric($amountRaw) || (float)$amountRaw <= 0) {
        $errors[] = 'Jumlah transaksi harus berupa angka desimal positif (lebih besar dari 0).';
    } else {
        $amount = (float)$amountRaw;
    }

    if (empty($errors)) {
        $transactionId = 'TRX-' . strtoupper(bin2hex(random_bytes(4)));
        $transaction = new Transaction($transactionId, $type, $amount);

        $result = $transaction->process($_SESSION['balance']);

        if ($result['success']) {
            $successMessage = $result['message'];

            $_SESSION['transactions'][] = [
                'id' => $transaction->getId(),
                'type' => $transaction->getType(),
                'amount' => $transaction->getAmount(),
                'timestamp' => date('Y-m-d H:i:s'),
                'status' => 'Sukses'
            ];

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Keuangan Modern</title>
</head>
<body>
<div class="container">
    <h1>Modul Pemrosesan Transaksi Keuangan</h1>

    <div class="balance-card">
        <h3>Sisa Saldo Saat Ini:</h3>
        <p style="font-size: 24px; font-weight: bold; margin: 0;">
            Rp <?= htmlspecialchars(number_format((float)$_SESSION['balance'], 2, ',', '.')) ?>
        </p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <h2>Tambah Transaksi</h2>
    <form action="finance.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-group">
            <label for="type">Jenis Transaksi:</label>
            <select name="type" id="type" required>
                <option value="">-- Pilih Jenis Transaksi --</option>
                <option value="deposit">Deposit (Tambah Saldo)</option>
                <option value="withdrawal">Penarikan (Tarik Saldo)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Jumlah Transaksi (Rp):</label>
            <input type="number" step="0.01" min="0.01" name="amount" id="amount" placeholder="Contoh: 50000.00" required>
        </div>

        <button type="submit">Proses Transaksi</button>
    </form>

    <hr style="margin: 30px 0;">

    <h2>Riwayat Transaksi</h2>
    <?php if (empty($_SESSION['transactions'])): ?>
        <p>Belum ada transaksi yang tercatat.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Waktu</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_reverse($_SESSION['transactions']) as $trx): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)$trx['id']) ?></td>
                        <td><?= htmlspecialchars((string)$trx['timestamp']) ?></td>
                        <td>
                            <?php if ($trx['type'] === 'deposit'): ?>
                                <span class="badge-deposit">Deposit</span>
                            <?php else: ?>
                                <span class="badge-withdrawal">Penarikan</span>
                            <?php endif; ?>
                        </td>
                        <td>Rp <?= htmlspecialchars(number_format((float)$trx['amount'], 2, ',', '.')) ?></td>
                        <td><?= htmlspecialchars((string)$trx['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>