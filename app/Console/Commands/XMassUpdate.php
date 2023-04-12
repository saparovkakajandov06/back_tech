<?php

namespace App\Console\Commands;

use App\Domain\Models\Chunk;
use App\Domain\Models\CompositeOrder;
use App\Services\ProfilerService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class XMassUpdate extends Command
{
    protected $signature = 'x:mass_update {timeout} {ids*}';

    public function logMessage($text)
    {
        $timestamp = Carbon::now()->toTimeString();

        $fp = fopen('x_mass_update.log', 'a');
        fwrite($fp, "[$timestamp] $text\n");
        fclose($fp);
    }

    protected function chunksMassUpdate(Collection $loadedChunks)
    {
        $services = $loadedChunks->map(fn($c)=>$c->service_class)
                           ->unique()
                           ->values()
                           ->all();

        echo "Total chunks: " . $loadedChunks->count() . "\n";
        echo "Total services: " . count($services) . "\n";
        foreach($services as $s) {
            echo $s . "\n";
        }

        foreach($services as $serviceClass) {
            $chunks = $loadedChunks->where('service_class', $serviceClass);

            if(! $chunks->count()) {
                continue;
            }

            echo "Update $serviceClass with {$chunks->count()} chunks\n";

            $externIds = $chunks->map(fn($chunk) => $chunk->extern_id)
                                ->values()
                                ->all();

            $svc = App::make($serviceClass);
            $exStatuses = $svc->getManyStatuses($externIds);

//            echo "extern statuses array\n";
//            echo json_encode($exStatuses, 128);

            // vkserfing не отдает больше 10 статусов сразу
            $i = 0;
            foreach($chunks as $currentChunk) {
                $exStatus = $exStatuses[$i];
                $currentChunk->feedStatus($exStatus);
                $i++;
            }
        }
    }

//DB::beginTransaction();
//    // do all your updates here
//
//foreach ($users as $user) {
//
//$new_value = rand(1,10) // use your own criteria
//
//DB::table('users')
//->where('id', '=', $user->id)
//->update([
//'status' => $new_value  // update your field(s) here
//]);
//}
//// when done commit
//DB::commit();
//Now you can have 1 milion different updates in one DB transaction

    public function handle(ProfilerService $p)
    {
        $start = microtime(true);
        $timeout = (int) $this->argument('timeout');

        $this->logMessage('Update started');

        $p->start('total');

        $ids = $this->argument('ids');

        $runningOrders = CompositeOrder::whereIn('id', $ids)->get();

        $runningIds = $runningOrders->pluck('id');
        $chunks = Chunk::whereIn('composite_order_id', $runningIds)
            ->get()
            ->keyBy('id');

        $this->chunksMassUpdate($chunks);

        $x = 0;
        foreach($runningOrders as $order) {
            $order->startUpdate(); // set updating state
            // chunks already updated
            $order->nextState($chunks); // update done

            if (microtime(true) - $start > $timeout) {
                $msg = 'XUpdate exit by timeout ' . $timeout;
                Log::info($msg);
                echo("$msg\n");
                $this->logMessage($msg);

                return;
            }

            $x++;
        }
        echo "updated $x orders\n";

        $p->stop('total');

        echo "\n";
        $p->dump('total');

        $updateTime = $p->get('total');
        $this->logMessage("Update finished. $x orders after $updateTime seconds");
    }
}