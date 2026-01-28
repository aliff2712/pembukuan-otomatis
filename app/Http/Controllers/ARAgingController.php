<?php

namespace App\Http\Controllers;

use App\Services\ARAgingService;

class ARAgingController extends Controller
{
    public function index(ARAgingService $service)
    {
        $summary = $service->summary();

        return view('reports.ar-aging', [
            'summary' => $summary
        ]);
    }
}
        