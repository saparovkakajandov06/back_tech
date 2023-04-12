<?php

namespace App\Domain\OrderSM;

interface IOrderState
{
    public function split();
    public function pay();
    public function run();

    public function startUpdate();

    public function pause();

    public function complete();
    public function partial();

    public function cancel();
    public function error();

    public function modRun();
    public function modStop();
    public function modComplete();
    public function modCancel();
    public function modCompleteMain();
}
