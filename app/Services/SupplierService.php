<?php

namespace App\Services;

use App\Services\Interfaces\SupplierServiceInterface;
use App\Repositories\Interfaces\SupplierRepositoryInterface as SupplierRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SupplierService
 * @package App\Services
 */
class SupplierService extends BaseService implements SupplierServiceInterface
{
    protected $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $perPage = $request->integer('perpage');

        $suppliers = $this->supplierRepository->supplierPagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'supplier/index']
        );

        return $suppliers;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only($this->payload());
            $supplier = $this->supplierRepository->create($payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only($this->payload());

            $supplier = $this->supplierRepository->update($id, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->supplierRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }

    public function statistic()
    {
        return [
            'totalSuppliers' => $this->supplierRepository->totalSupplier(),
        ];
    }

    private function paginateSelect()
    {
        return [
            'id',
            'suppliers.code',
            'name',
            'phone',
            'province_id',
            'district_id',
            'ward_id',
            'address',
            'description',
        ];
    }
    private function payload()
    {
        return [
            'code',
            'name',
            'phone',
            'province_id',
            'district_id',
            'ward_id',
            'address',
            'description',
        ];
    }
}
