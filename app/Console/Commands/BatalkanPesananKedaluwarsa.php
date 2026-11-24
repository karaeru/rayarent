<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class BatalkanPesananKedaluwarsa extends Command
{
    protected $signature = 'pesanan:batal-kedaluwarsa';

    protected $description = 'Batalkan pesanan yang belum mengunggah bukti bayar melewati batas waktu';

    public function handle(): int
    {
        $n = Order::cancelExpired();
        $this->info($n ? "{$n} pesanan dibatalkan otomatis." : 'Tidak ada pesanan kedaluwarsa.');

        return self::SUCCESS;
    }
}
