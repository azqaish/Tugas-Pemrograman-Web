<?php
declare(strict_types=1);

class GuestBook {
    private string $host = '127.0.0.1';
    private string $db   = 'akademik_db';
    private string $user = 'root';          
    private string $pass = '';   
    private ?PDO $pdo    = null;
}