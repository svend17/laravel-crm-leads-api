<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\LeadRepositoryInterface;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Resources\LeadResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request, LeadRepositoryInterface $leads): JsonResponse
    {
        $lead = $leads->create($request->validated());

        return (new LeadResource($lead))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
