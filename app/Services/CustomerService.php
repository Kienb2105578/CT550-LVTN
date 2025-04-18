<?php

namespace App\Services;

use App\Services\Interfaces\CustomerServiceInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerService extends BaseService implements CustomerServiceInterface
{
    protected $customerRepository;


    public function __construct(
        CustomerRepository $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = $request->integer('perpage');
        $customers = $this->customerRepository->customerPagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'customer/index'],
        );

        return $customers;
    }


    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->except(['_token', 'send', 're_password']);
            if (isset($payload['birthday']) && ($payload['birthday'] != null)) {
                $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            }
            $payload['password'] = Hash::make($payload['password']);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('avatar'), $fileName);
                $payload['image'] = 'avatar/' . $fileName;
            }

            $customer = $this->customerRepository->create($payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
            return false;
        }
    }


    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->except(['_token', 'send']);
            if (isset($payload['birthday']) && $payload['birthday'] != null) {
                $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();

                $customer = $this->customerRepository->find($id);

                if (!empty($customer->image) && file_exists(public_path($customer->image)) && $customer->image !== 'avatar/default-avatar.png') {
                    unlink(public_path($customer->image));
                }

                $file->move(public_path('avatar'), $fileName);
                $payload['image'] = 'avatar/' . $fileName;
            }


            $customer = $this->customerRepository->update($id, $payload);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $customer = $this->customerRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }


    private function convertBirthdayDate($birthday = '')
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');
        return $birthday;
    }


    public function statistic()
    {

        return [
            'totalCustomers' => $this->customerRepository->totalCustomer(),
        ];
    }

    private function paginateSelect()
    {
        return [
            'id',
            'email',
            'phone',
            'address',
            'name',
            'publish',
            'province_id',
            'district_id',
            'ward_id',
        ];
    }
}
