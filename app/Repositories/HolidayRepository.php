<?php

namespace App\Repositories;

use App\Models\Holiday;
use Illuminate\Pagination\LengthAwarePaginator;

class HolidayRepository
{
    /**
     * @var App\Models\Holiday $holiday
     */
    public Holiday $holiday;

    public function __construct(Holiday $holiday)
    {
        $this->holiday = $holiday;
    }

    /**
     * @param array $params
     * 
     * @return Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $params): LengthAwarePaginator
    {
        return $this->holiday
            ->where(function ($q) use ($params) {
                if ($params['keyword']) {
                    $q->where('holiday', 'LIKE', "%{$params['keyword']}%");
                }

                if ($params['year']) {
                    $q->whereYear('date', $params['year']);
                }

                if ($params['month']) {
                    $q->whereMonth('date', $params['month']);
                }
            })
            ->orderBy($params['sortBy'], $params['sorting'])
            ->paginate($params['limit']);
    }

    /**
     * @param array $data
     * 
     * @return object
     */
    public function create(array $data): object
    {
        return $this->holiday->create($data);
    }

    /**
     * @param int $id
     * 
     * @return object|null
     */
    public function findById(int $id): ?object
    {
        return $this->holiday->findOrFail($id);
    }

    /**
     * @param int $id
     * 
     * @return bool
     */
    public function deleteById(int $id): bool
    {
        return $this->holiday->where('id', $id)->delete();
    }
}
