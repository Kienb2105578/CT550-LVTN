<?php

namespace App\Services;

use App\Services\Interfaces\StockTakingServiceInterface;
use App\Repositories\Interfaces\StockTakingRepositoryInterface as StockTakingRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\StockTaking;
use Illuminate\Support\Carbon;

/**
 * Class CustomerService
 * @package App\Services
 */
class StockTakingService extends BaseService implements StockTakingServiceInterface
{
    protected $stockTakingRepository;

    public function __construct(
        StockTakingRepository $stockTakingRepository,
    ) {
        $this->stockTakingRepository = $stockTakingRepository;
        $this->controllerName = 'StockController';
    }

    public function paginate($request)
    {
        $query = StockTaking::query()
            ->select('stock_takings.*', 'users.name as user_name', 'user_catalogues.name as user_position')
            ->join('users', 'users.id', '=', 'stock_takings.user_id')
            ->join('user_catalogues', 'user_catalogues.id', '=', 'users.user_catalogue_id')
            ->whereNull('stock_takings.deleted_at');

        if ($request->filled('keyword')) {
            $keyword = addslashes($request->input('keyword'));
            $query->where('users.name', 'like', '%' . $keyword . '%');
        }

        if ($request->has('publish')) {
            $query->where('stock_takings.publish', $request->integer('publish'));
        }

        if ($request->filled('created_at')) {
            $query->whereDate('stock_takings.created_at', $request->input('created_at'));
        }

        $perPage = $request->integer('perpage', 20);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only(['code', 'description', 'publish', 'products']);
            $payload['user_id'] = Auth::id();

            $record = $this->stockTakingRepository->create($payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->all();
            $this->stockTakingRepository->update($id, $payload);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->stockTakingRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }
}
