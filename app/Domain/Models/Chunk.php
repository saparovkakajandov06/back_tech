<?php

namespace App\Domain\Models;

use App;
use App\Action;
use App\AddResult;
use App\Domain\Services\AbstractService;
use App\Exceptions\Reportable\ReportableException;
use App\ExternStatus;
use App\Order;
use App\Services\ProfilerService;
use App\User;
use Database\Factories\ChunkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Часть заказа
 */
class Chunk extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'details' => 'array',
        'add_request' => 'array',
        'remote_response' => 'array',
    ];
    protected $hidden = [
        'compositeOrder',
    ];

    protected static function newFactory()
    {
        return ChunkFactory::new();
    }

    public function compositeOrder()
    {
        return $this->belongsTo(CompositeOrder::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'actions');
    }

    public function run(): AddResult
    {
        if ($this->isTestMode()) {
            bind_fake_suppliers();
        }

        if (!empty($this->extern_id)) {
            throw new ReportableException("Chunk already has an extern id.");
        }
        if (!($service = App::make($this->service_class)))
            throw new ReportableException("Could not create service {$this->service_class}");

        $orderParams = $this->compositeOrder->params;

        $slotName = $this->details['slot'];
        $allSlots = $this->compositeOrder->userService->config;
        if (! ($serviceConfig = Slots::getSlotFromArray($slotName, $allSlots))) {
            throw new ReportableException("No service config");
        }

        $addResult = $service->add($this, $orderParams, $serviceConfig);

        $this->add_request = $addResult->request;
        $this->remote_response = $addResult->response;
        $this->status = $addResult->status;

        if (!empty($addResult->externId)) {
            $this->extern_id = $addResult->externId;
        }

        $this->details = array_merge($this->details, [
            'charge' => $addResult->charge ?? 0.
        ]);

        $this->save();

        return $addResult;
    }

    public function updateStatus()
    {
        // not error and not completed

        if (empty($this->extern_id)) {
//            Log::info("Chunk $this->id has no extern id. Exit.");
            return;
        }

        if ($this->isTestMode()) {
            bind_fake_suppliers();
        }

        $p = resolve(ProfilerService::class);
        $p->start('serviceCall');
        $exStatus = App::make($this->service_class)->getStatus($this->extern_id);
        $p->stop('serviceCall');

        $this->feedStatus($exStatus);
    }

    public function feedStatus(ExternStatus $es)
    {
        $this->status = $es->status;

        if (in_array($es->status, [ Order::STATUS_RUNNING,
                                    Order::STATUS_COMPLETED,
                                    Order::STATUS_PARTIAL_COMPLETED])) {

            $this->completed = $es->completed ?? ($this->getRemoteCount() - $es->remains);
        }

        echo json_encode($es->response) . "\n";
        $this->remote_response = $es->response;

        $this->save();
    }

    public function getOldRemoteCount(): int
    {
        $count = $this->details['count'];
        $config = $this->compositeOrder->userService->config[$this->service_class];

        return AbstractService::getRemoteCountWithMods($count, $config);
    }

    public function getRemoteCount(): int
    {
        if (! empty($this->details['remote_count'])) {
            $rc = $this->details['remote_count'];
        }
        else {
            [ 'slot' => $slot, 'count' => $count ] = $this->details;
            $config = Slots::getConfig($slot);

            $rc = AbstractService::getRemoteCountWithMods($count, $config);
        }
        return $rc;
    }

    // get my service config
    public function getServiceConfig(): ?array
    {
        $slotName = data_get($this, 'details.slot');
        if (! $slotName) {
            return null;
        }
        $config = Slots::getConfig($slotName);
        return $config;
    }

    // количество, которое реально заказывается
    public function getVendorCount()
    {
        $svcConfig = $this->getServiceConfig();
        $count = data_get($this, 'details.count');
        if (! $svcConfig) {
            return $count;
        } else {
            return AbstractService::
                    getLocalCountWithMods($count, $svcConfig);
        }
    }

    private function isTestMode(): bool
    {
        return isset($this->compositeOrder->params['is_test_mode']) 
            && $this->compositeOrder->params['is_test_mode'];
    }
}
