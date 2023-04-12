<?php

namespace App\Providers;

use App\Domain\Services\ATime;
use App\Domain\Services\Everve\AEverve;
use App\Domain\Services\Everve\Everve;
use App\Domain\Services\Everve\EverveFake;
use App\Domain\Services\Fake\AFake;
use App\Domain\Services\Fake\Fake;
use App\Domain\Services\Local\ALocal;
use App\Domain\Services\Local\Local;
use App\Domain\Services\Nakrutka\ANakrutka;
use App\Domain\Services\Nakrutka\Nakrutka;
use App\Domain\Services\Nakrutka\NakrutkaFake;
use App\Domain\Services\NakrutkaAuto\ANakrutkaAuto;
use App\Domain\Services\NakrutkaAuto\NakrutkaAuto;
use App\Domain\Services\NakrutkaAuto\NakrutkaAutoFake;
use App\Domain\Services\Prm4u\APrm4u;
use App\Domain\Services\Prm4u\Prm4u;
use App\Domain\Services\Prm4u\Prm4uFake;
use App\Domain\Services\Prm4uAuto\APrm4uAuto;
use App\Domain\Services\Prm4uAuto\Prm4uAuto;
use App\Domain\Services\Prm4uAuto\Prm4uAutoFake;
use App\Domain\Services\Socgress\ASocgress;
use App\Domain\Services\Socgress\Socgress;
use App\Domain\Services\Socgress\SocgressFake;
use App\Domain\Services\TimeService;
use App\Domain\Services\Vkserfing\AVkserfing;
use App\Domain\Services\Vkserfing\Vkserfing;
use App\Domain\Services\Vkserfing\VkserfingFake;
use App\Domain\Services\VkserfingAuto\AVkserfingAuto;
use App\Domain\Services\VkserfingAuto\VkserfingAuto;
use App\Domain\Services\VkserfingAuto\VkserfingAutoFake;
use App\Domain\Services\Vtope\AVtope;
use App\Domain\Services\Vtope\Vtope;
use App\Domain\Services\Vtope\VtopeFake;
use Illuminate\Support\ServiceProvider;

class ExternServicesProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('testing')) {
            $this->app->bind(AEverve::class, EverveFake::class);
            $this->app->bind(ANakrutka::class, NakrutkaFake::class);
            $this->app->bind(ANakrutkaAuto::class, NakrutkaAutoFake::class);
            $this->app->bind(APrm4u::class, Prm4uFake::class);
            $this->app->bind(APrm4uAuto::class, Prm4uAutoFake::class);
            $this->app->bind(ASocgress::class, SocgressFake::class);
            $this->app->bind(AVkserfing::class, VkserfingFake::class);
            $this->app->bind(AVkserfingAuto::class, VkserfingAutoFake::class);
            $this->app->bind(AVtope::class, VtopeFake::class);
        }
        else {
            $this->app->bind(AEverve::class, Everve::class);
            $this->app->bind(ANakrutka::class, Nakrutka::class);
            $this->app->bind(ANakrutkaAuto::class, NakrutkaAuto::class);
            $this->app->bind(APrm4u::class, Prm4u::class);
            $this->app->bind(APrm4uAuto::class, Prm4uAuto::class);
            $this->app->bind(ASocgress::class, Socgress::class);
            $this->app->bind(AVkserfing::class, Vkserfing::class);
            $this->app->bind(AVkserfingAuto::class, VkserfingAuto::class);
            $this->app->bind(AVtope::class, Vtope::class);
        }

        $this->app->bind(ALocal::class, fn() => new Local());

        $this->app->bind(ATime::class, function() {
            return new TimeService();
        });

        $this->app->bind(AFake::class, fn() => new Fake());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
