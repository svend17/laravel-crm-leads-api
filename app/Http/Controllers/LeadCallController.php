<?php

namespace App\Http\Controllers;

use App\Actions\CreateCallAction;
use App\Http\Requests\StoreCallRequest;
use App\Http\Resources\CallResource;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LeadCallController extends Controller
{
    public function store(StoreCallRequest $request, Lead $lead, CreateCallAction $createCall): JsonResponse
    {
        $call = $createCall->handle($lead, $request->validated());

        return (new CallResource($call))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
