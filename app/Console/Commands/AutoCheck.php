<?php

namespace App\Console\Commands;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Services\UpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AutoCheck extends Command
{
    protected $signature = 'smm:check';
    private UpdateService $svc;

    public function __construct()
    {
        parent::__construct();
        $this->svc = new UpdateService();
    }

    public function log($msg)
    {
        $msg = '[Check] ' . $msg;
        Log::channel('orders')->info($msg);
    }

    public function handle()
    {
        // chunks whose process was killed
        $badChunksQuery =
            Chunk::where('created_at', '>=', $this->svc->checkDate())
                 ->where('status', '!=', 'STATUS_ERROR')
                 ->where(function($q) {
                     $q->where('add_request', null)
                       ->orWhere('extern_id', null);
                 });

        $badChunksCount = $badChunksQuery->count();
        echo "$badChunksCount bad chunks.\n";
        $this->log("$badChunksCount bad chunks.");

        $updated = $badChunksQuery->update([
            'status' => Order::STATUS_ERROR,
        ]);
        echo "$updated chunks updated.\n";
        $this->log("$updated chunks updated.");

        // ---------- orders ----------------
        $badOrdersPaid = CompositeOrder::where('status', Order::STATUS_PAID)
            ->whereBetween('created_at', [
                Carbon::parse('08 may 2021'),
                Carbon::parse('5 min ago'),
            ])
            ->get();

        $this->log("Paid orders > 5 min: " . count($badOrdersPaid));

        foreach ($badOrdersPaid as $order) {
            $this->log("{$order->id} {$order->status} -> STATUS_ERROR");
            $order->update(['status' => Order::STATUS_ERROR]);
            $order->writeLog('autoCheck', 'STATUS_PAID -> STATUS_ERROR');
        }
    }
}
