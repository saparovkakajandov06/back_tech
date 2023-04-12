<?php

namespace App\Console\Commands;

use App\Domain\Models\CompositeOrder;
use App\Order;
use App\Services\UpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AutoRestart extends Command
{
    protected $signature = 'smm:restart {action}';
    private UpdateService $svc;

    public function __construct()
    {
        parent::__construct();
        $this->svc = new UpdateService();
    }

    public function log($msg)
    {
        $msg = '[Restart] ' . $msg;
        Log::channel('orders')->info($msg);
    }

    private function ordersForRestart(): Collection
    {
        $orders = CompositeOrder::select([
            'id',
            'status',
            'created_at',
            'params',
        ])->with([
            'chunks' => function ($q) {
                $q->select('id', 'extern_id');
            },
        ])
            ->where('created_at', '>=', $this->svc->restartDate())
            ->where('status', Order::STATUS_ERROR)
            ->where(function($q) {
                $q->whereRaw("(params->>'restarts')::int < 3")
                    ->orWhereRaw("params->>'restarts' is null");
            })
            ->whereHas('chunks', function ($q) {
                $q->where('extern_id', null);
            })
            ->get();

        return $orders;
    }

    private function list()
    {
        $orders = $this->ordersForRestart();

        echo "Errored orders {$orders->count()}\n";
        $this->log("Errored orders {$orders->count()}");

        foreach($orders as $order) {
            echo $order->id . " ";
        }
        echo "\n";
    }

    private function restartAll()
    {
        $orders = $this->ordersForRestart();

        echo "Restarting {$orders->count()} orders\n";
        $this->log("Restarting {$orders->count()} orders");

        foreach($orders as $order) {
            $this->restart($order->id);
        }
        echo "\n";
    }

    private function incRestarts($id)
    {
        $order = CompositeOrder::findOrFail($id);

        $params = $order->params;
        if (empty($params['restarts'])) {
            $params['restarts'] = 1;
        } else {
            $params['restarts'] += 1;
        }
        $order->params = $params;
        $order->save();

        return $order;
    }

    public function restart($id)
    {
        $order = $this->incRestarts($id);
        echo "Restarting $id\n";
        $this->log("Restarting $id");

        $order->run();
    }

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->list();
                break;
            case 'restart_all':
                $this->restartAll();
                break;
            default:
                $this->restart($action);
        }
    }
}
