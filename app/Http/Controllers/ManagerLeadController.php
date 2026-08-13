<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\LeadRepositoryInterface;
use App\Http\Requests\IndexManagerLeadsRequest;
use App\Http\Resources\ManagerLeadResource;
use App\Models\Manager;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ManagerLeadController extends Controller
{
    public function __construct(
        private LeadRepositoryInterface $leads,
    ) {}

    public function index(
        IndexManagerLeadsRequest $request,
        Manager $manager,
    ): AnonymousResourceCollection {
        return ManagerLeadResource::collection(
            $this->leads->paginateForManager($manager, $request->perPage())
        );
    }
}
