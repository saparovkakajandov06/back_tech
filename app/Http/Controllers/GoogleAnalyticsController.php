<?php

namespace App\Http\Controllers;

use App\Documentor\Documentor as D;
use App\Documentor\Endpoint;
use App\Documentor\Group;
use App\Documentor\Role;
use App\Documentor\Text;
use App\Documentor\Verbs;
use App\Responses\ApiSuccess;
use App\Services\GoogleAnalytics;
use Illuminate\Http\Request;

class GoogleAnalyticsController extends Controller
{
    #[Endpoint('collect/analytic/{type}')]
    #[Verbs(D::POST)]
    #[Role('ROLE_ANY')]
    #[Group('analytic')]
    #[Text('Google analytics collect proxy method')]
    public function collect(Request $request, GoogleAnalytics $googleAnalytics)
    {
        $request->validate([
            'type' => ['required|in:pageview,event,transaction']
        ]);

        $data = $request->all();
        $data['t'] = $request->type;
        $googleAnalytics->collect($data);

        return new ApiSuccess('Google analytics - ok', null);
    }
}
