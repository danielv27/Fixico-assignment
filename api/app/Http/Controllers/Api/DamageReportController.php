<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDamageReportRequest;
use App\Http\Requests\Api\UpdateDamageReportRequest;
use App\Http\Resources\DamageReportResource;
use App\Models\DamageReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DamageReportController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return DamageReportResource::collection(
            DamageReport::query()->latest()->get(),
        );
    }

    public function store(StoreDamageReportRequest $request): JsonResponse
    {
        $report = DamageReport::query()->create($request->validated());

        return DamageReportResource::make($report)
            ->response()
            ->setStatusCode(201);
    }

    public function show(DamageReport $report): DamageReportResource
    {
        return DamageReportResource::make($report);
    }

    public function update(UpdateDamageReportRequest $request, DamageReport $report): DamageReportResource
    {
        $report->update($request->validated());

        return DamageReportResource::make($report);
    }
}
