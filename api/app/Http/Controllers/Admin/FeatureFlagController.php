<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFeatureFlagRequest;
use App\Http\Requests\Admin\UpdateFeatureFlagRequest;
use App\Http\Resources\FeatureFlagResource;
use App\Models\FeatureFlag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeatureFlagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return FeatureFlagResource::collection(
            FeatureFlag::query()->orderBy('name')->get()
        );
    }

    public function store(StoreFeatureFlagRequest $request): JsonResponse
    {
        $flag = FeatureFlag::create($request->validated());

        return (new FeatureFlagResource($flag))->response()->setStatusCode(201);
    }

    public function show(FeatureFlag $flag): FeatureFlagResource
    {
        return new FeatureFlagResource($flag);
    }

    public function update(UpdateFeatureFlagRequest $request, FeatureFlag $flag): FeatureFlagResource
    {
        $flag->update($request->validated());

        return new FeatureFlagResource($flag);
    }

    public function destroy(FeatureFlag $flag): JsonResponse
    {
        $flag->delete();

        return response()->json(null, 204);
    }
}
