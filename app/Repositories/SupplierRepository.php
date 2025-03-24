<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class SupplierService
 * @package App\Services
 */
class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    protected $model;

    public function __construct(
        Supplier $model
    ) {
        $this->model = $model;
    }
    public function supplierPagination(
        array $column = [
            'id',
            'code',
            'name',
            'phone',
            'province_id',
            'district_id',
            'ward_id',
            'address',
            'description'
        ],
        array $condition = [],
        int $perPage = 15,
        array $extend = [],
        array $orderBy = ['id', 'DESC']
    ) {
        // 1️⃣ Lấy danh sách nhà cung cấp
        $suppliers = $this->model->select($column)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($condition) {
                if (!empty($condition['keyword'])) {
                    $query->where('name', 'LIKE', '%' . $condition['keyword'] . '%')
                        ->orWhere('phone', 'LIKE', '%' . $condition['keyword'] . '%')
                        ->orWhere('address', 'LIKE', '%' . $condition['keyword'] . '%');
                }
            })
            ->orderBy($orderBy[0], $orderBy[1])
            ->paginate($perPage)
            ->withQueryString()
            ->withPath(env('APP_URL') . ($extend['path'] ?? ''));

        // 2️⃣ Lấy danh sách tỉnh, huyện, xã
        $provinces = DB::table('provinces')->pluck('name', 'code'); // ['02' => 'Hà Giang']
        $districts = DB::table('districts')->pluck('name', 'code'); // ['026' => 'Đồng Văn']
        $wards = DB::table('wards')->pluck('name', 'code'); // ['00718' => 'Má Lé']

        // 3️⃣ Gán lại tên tỉnh, huyện, xã bằng PHP
        $suppliers->getCollection()->transform(function ($supplier) use ($provinces, $districts, $wards) {
            $supplier->province_name = $provinces[$supplier->province_id] ?? null;
            $supplier->district_name = $districts[$supplier->district_id] ?? null;
            $supplier->ward_name = $wards[$supplier->ward_id] ?? null;
            return $supplier;
        });

        return $suppliers;
    }

    public function getSupplier($supplier_id = [], $condition = [])
    {
        $query = $this->model->select(
            'id',
            'code',
            'name',
            'phone',
            'email',
            'address'
        )->whereIn('id', $supplier_id);
        if (isset($condition['keyword']) && !empty($condition['keyword'])) {
            $keyword = $condition['keyword'];
            $query->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('code', 'LIKE', '%' . $keyword . '%')
                ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                ->orWhere('address', 'LIKE', '%' . $keyword . '%')
                ->orWhere('phone', 'LIKE', '%' . $keyword . '%');
        }
        return $query->paginate(20);
    }
    public function getSupplierByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }
    public function getAllSuppliers()
    {
        return $this->model->all();
    }


    public function totalSupplier()
    {
        return $this->model->count();
    }
}
