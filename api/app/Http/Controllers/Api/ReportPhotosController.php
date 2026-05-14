<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportPhotosController extends Controller
{
    public function store(Request $request, DamageReport $report): JsonResponse
    {
        $request->validate([
            'urls' => ['array'],
            'urls.*' => ['url'],
        ]);

        $report->update(['photos' => $request->input('urls', [])]);

        return response()->json(['photos' => $report->photos]);
    }
}
