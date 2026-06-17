<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\WebhookEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $locale = $request->query('lang', session('locale', config('app.locale')));

        if (! in_array($locale, ['fa', 'en'], true)) {
            $locale = config('app.locale');
        }

        session(['locale' => $locale]);
        App::setLocale($locale);

        return view('dashboard', [
            'leadCount' => Lead::count(),
            'webinarCount' => Lead::where('intent', 'webinar')->count(),
            'courseCount' => Lead::where('intent', 'course')->count(),
            'technicalCount' => Lead::where('intent', 'technical_support')->count(),
            'consultantCount' => Lead::where('intent', 'consultant')->count(),
            'leads' => Lead::latest()->take(20)->get(),
            'events' => WebhookEvent::latest()->take(10)->get(),
            'locale' => $locale,
        ]);
    }
}
