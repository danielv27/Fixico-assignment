<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulkDeleteReportsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (! is_array($ids) || count($ids) === 0) {
            return response()->json(['message' => 'No report IDs provided.'], 422);
        }

        $deleted = DamageReport::query()->whereIn('id', $ids)->delete();

        return response()->json(['deleted' => $deleted]);
    }
}
