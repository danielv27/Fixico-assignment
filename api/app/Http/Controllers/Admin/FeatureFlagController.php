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

    public function update(UpdateFeatureFlagRequest $request, FeatureFlag $flag): JsonResponse|FeatureFlagResource
    {
        $data = $request->validated();

        if (array_key_exists('enabled', $data) && $flag->ends_at !== null && now()->isAfter($flag->ends_at)) {
            return response()->json(['message' => 'Expired flags cannot be toggled.'], 422);
        }

        $flag->update($data);

        return new FeatureFlagResource($flag);
    }

    public function destroy(FeatureFlag $flag): JsonResponse
    {
        $flag->delete();

        return response()->json(null, 204);
    }
}
