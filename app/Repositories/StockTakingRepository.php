<?php

namespace App\Repositories;

use App\Models\StockTaking;
use App\Repositories\Interfaces\StockTakingRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Class UserService
 * @package App\Services
 */
class StockTakingRepository extends BaseRepository implements StockTakingRepositoryInterface
{
    protected $model;

    public function __construct(
        StockTaking $model
    ) {
        $this->model = $model;
    }
}
