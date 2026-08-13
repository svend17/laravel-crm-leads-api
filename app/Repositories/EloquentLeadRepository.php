<?php

namespace App\Repositories;

use App\Contracts\Repositories\LeadRepositoryInterface;
use App\Models\Lead;
use App\Models\Manager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentLeadRepository implements LeadRepositoryInterface
{
    /**
     * @param  array{name: string, phone: string, manager_id?: int|null}  $data
     */
    public function create(array $data): Lead
    {
        return Lead::create($data);
    }

    public function findLocked(int $id): Lead
    {
        return Lead::query()
            ->whereKey($id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function save(Lead $lead): Lead
    {
        $lead->save();

        return $lead;
    }

    /**
     * @return LengthAwarePaginator<int, Lead>
     */
    public function paginateForManager(Manager $manager, int $perPage): LengthAwarePaginator
    {
        return Lead::query()
            ->whereBelongsTo($manager)
            ->withCount('calls')
            ->withSum('calls as total_call_duration', 'duration')
            ->latest('id')
            ->paginate($perPage);
    }
}
