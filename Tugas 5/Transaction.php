<?php
declare(strict_types=1); 

class Transaction {
    public function __construct(
        private string $id,
        private string $type,
        private float $amount
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function process(float &$balance): array {
        return match ($this->type) {
            'deposit' => $this->handleDeposit($balance),
            'withdrawal' => $this->handleWithdrawal($balance),
            default => [
                'success' => false,
                'message' => 'Transaksi tidak valid.'
            ]
        };
    }

    private function handleDeposit(float &$balance): array {
        $balance += $this->amount;
        return [
            'success' => true,
            'message' => 'Deposit sebesar Rp ' . number_format($this->amount, 2, ',', '.') . ' berhasil ditambahkan.'
        ];
    }

    private function handleWithdrawal(float &$balance): array {
        if ($this->amount > $balance) {
            return [
                'success' => false,
                'message' => 'Penarikan gagal: Saldo dalam sesi tidak mencukupi.'
            ];
        }

        $balance -= $this->amount;
        return [
            'success' => true,
            'message' => 'Penarikan sebesar Rp ' . number_format($this->amount, 2, ',', '.') . ' berhasil diproses.'
        ];
    }
}