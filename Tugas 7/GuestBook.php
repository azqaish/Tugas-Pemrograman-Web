<?php
declare(strict_types=1);

class GuestBook {
    private string $host = '127.0.0.1';
    private string $db   = 'akademik_db';
    private string $user = 'root';          
    private string $pass = '';   
    private ?PDO $pdo    = null;

    private function getConnection(): PDO {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            } catch (PDOException $e) {
                error_log('Database Connection Error: ' . $e->getMessage());
                die('Sistem mengalami kegagalan teknis. DEBUG: ' . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    public static function validasi(string $nama, string $email, string $pesan): array {
        $errors = [];

        if ($nama === '') {
            $errors['nama'] = 'Nama tidak boleh kosong.';
        } elseif (mb_strlen($nama) > 100) {
            $errors['nama'] = 'Nama maksimal 100 karakter.';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Format email tidak valid.';
        } elseif (mb_strlen($email) > 100) {
            $errors['email'] = 'Email maksimal 100 karakter.';
        }

        if (mb_strlen($pesan) < 5) {
            $errors['pesan'] = 'Pesan minimal 5 karakter.';
        }

        return $errors;
    }

    public function simpan(string $nama, string $email, string $pesan): bool {
        $sql = "INSERT INTO buku_tamu (nama, email, pesan)
                VALUES (:nama, :email, :pesan)";
        $stmt = $this->getConnection()->prepare($sql);
        return $stmt->execute([
            'nama'  => $nama,
            'email' => $email,
            'pesan' => $pesan,
        ]);
    }
}