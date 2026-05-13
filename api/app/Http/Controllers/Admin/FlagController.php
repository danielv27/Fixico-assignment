<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFlagRequest;
use App\Http\Requests\Admin\UpdateFlagRequest;
use App\Http\Resources\FlagResource;
use App\Models\FeatureFlag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FlagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return FlagResource::collection(
            FeatureFlag::query()->orderBy('name')->get()
        );
    }

    public function store(StoreFlagRequest $request): JsonResponse
    {
        $flag = FeatureFlag::create($request->validated());

        return (new FlagResource($flag))->response()->setStatusCode(201);
    }

    public function show(FeatureFlag $flag): FlagResource
    {
        return new FlagResource($flag);
    }

    public function update(UpdateFlagRequest $request, FeatureFlag $flag): FlagResource
    {
        $flag->update($request->validated());

        return new FlagResource($flag);
    }

    public function destroy(FeatureFlag $flag): JsonResponse
    {
        $flag->delete();

        return response()->json(null, 204);
    }
}
