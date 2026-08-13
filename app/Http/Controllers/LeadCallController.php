<?php

namespace App\Http\Controllers;

use App\Contracts\Actions\CreatesCalls;
use App\Http\Requests\StoreCallRequest;
use App\Http\Resources\CallResource;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LeadCallController extends Controller
{
    public function __construct(
        private CreatesCalls $callCreator,
    ) {}

    public function store(StoreCallRequest $request, Lead $lead): JsonResponse
    {
        $call = $this->callCreator->handle($lead, $request->validated());

        return new CallResource($call)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
