<?php

namespace App\Services;

use App\Services\Interfaces\PermissionServiceInterface;
use App\Repositories\Interfaces\PermissionRepositoryInterface as PermissionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
 * Class PermissionService
 * @package App\Services
 */
class PermissionService implements PermissionServiceInterface
{
    protected $permissionRepository;


    public function __construct(
        PermissionRepository $permissionRepository,
    ) {
        $this->permissionRepository = $permissionRepository;
    }



    public function paginate($request)
    {

        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = $request->integer('perpage');
        $permissions = $this->permissionRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'permission/index'],
        );
        return $permissions;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->except(['_token', 'send']);
            $payload['user_id'] = Auth::id();
            $permission = $this->permissionRepository->create($payload);
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

            $payload = $request->except(['_token', 'send']);
            $permission = $this->permissionRepository->update($id, $payload);
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
            $this->permissionRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    private function paginateSelect()
    {
        return [
            'id',
            'name',
            'canonical',
        ];
    }
}
