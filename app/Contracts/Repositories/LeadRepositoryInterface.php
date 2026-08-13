<?php

namespace App\Contracts\Repositories;

use App\Models\Lead;
use App\Models\Manager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LeadRepositoryInterface
{
    /**
     * @param  array{name: string, phone: string, manager_id?: int|null}  $data
     */
    public function create(array $data): Lead;

    public function findLocked(int $id): Lead;

    public function save(Lead $lead): Lead;

    /**
     * @return LengthAwarePaginator<int, Lead>
     */
    public function paginateForManager(Manager $manager, int $perPage): LengthAwarePaginator;
}
