<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\WebhookEvent;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'leadCount' => Lead::count(),
            'webinarCount' => Lead::where('intent', 'webinar')->count(),
            'courseCount' => Lead::where('intent', 'course')->count(),
            'technicalCount' => Lead::where('intent', 'technical_support')->count(),
            'consultantCount' => Lead::where('intent', 'consultant')->count(),
            'leads' => Lead::latest()->take(20)->get(),
            'events' => WebhookEvent::latest()->take(10)->get(),
        ]);
    }
}
