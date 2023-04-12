<?php

namespace App\Domain\OrderSM;

trait StateMethods
{
    public function split(): self
    {
        $this->state()->split();
        return $this;
    }

    public function pay(): self
    {
        $this->state()->pay();
        return $this;
    }

    public function run(): self
    {
        $this->state()->run();
        return $this;
    }

    public function startUpdate(): self
    {
        $this->state()->startUpdate();
        return $this;

    }
    public function pause(): self
    {
        $this->state()->pause();
        return $this;
    }

    public function complete(): self
    {
        $this->state()->complete();
        return $this;
    }

    public function partial(): self
    {
        $this->state()->partial();
        return $this;
    }

    public function cancel(): self
    {
        $this->state()->cancel();
        return $this;
    }

    public function error(): self
    {
        $this->state()->error();
        return $this;
    }

    public function modRun(): self
    {
        $this->state()->modRun();
        return $this;
    }

    public function modStop(): self
    {
        $this->state()->modStop();
        return $this;
    }

    public function modComplete(): self
    {
        $this->state()->modComplete();
        return $this;
    }

    public function modCancel(): self
    {
        $this->state()->modCancel();
        return $this;
    }

    public function modCompleteMain(): self
    {
        $this->state()->modCompleteMain();
        return $this;
    }
}
