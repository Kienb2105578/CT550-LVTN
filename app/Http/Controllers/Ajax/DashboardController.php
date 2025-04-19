<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrderRepositoryInterface  as OrderRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class DashboardController extends Controller
{

    protected $orderRepository;

    public function __construct(
        OrderRepository $orderRepository,
    ) {
        $this->orderRepository = $orderRepository;
        $this->middleware(function ($request, $next) {
            return $next($request);
        });
    }

    public function changeStatus(Request $request)
    {
        $post = $request->input();
        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        $flag = $serviceInstance->updateStatus($post);
        Log::info($flag);

        return response()->json(['flag' => $flag]);
    }


    public function changeStatusAll(Request $request)
    {
        $post = $request->input();
        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']) . 'Service';
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        $flag = $serviceInstance->updateStatusAll($post);
        return response()->json(['flag' => $flag]);
    }

    public function getMenu(Request $request)
    {
        $model = $request->input('model');
        $page = ($request->input('page')) ?? 1;
        $keyword = ($request->string('keyword')) ?? null;

        $serviceInterfaceNamespace = '\App\Repositories\\' . ucfirst($model) . 'Repository';
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }

        $agruments = $this->paginationAgrument($model, $keyword);
        $object = $serviceInstance->pagination(...array_values($agruments));

        return response()->json($object);
    }

    private function paginationAgrument(string $model = '', string $keyword): array
    {
        $model = Str::snake($model);
        $join = [];
        if (strpos($model, '_catalogue') === false) {
            $join[] = [$model . '_catalogue_' . $model . ' as tb3', $model . 's.id', '=', 'tb3.' . $model . '_id'];
        }

        $condition = [];
        if (!is_null($keyword)) {
            $condition['keyword'] = addslashes($keyword);
        }

        return [
            'column' => ['id', 'name', 'canonical'],
            'condition' => $condition,
            'perpage' => 20,
            'extend' => [
                'path' => $model . '.index',
                'groupBy' => ['id', 'name']
            ],
            'orderBy' => [$model . 's.id', 'DESC'],
            'join' => $join,
            'relations' => [],
        ];
    }

    public function findModelObject(Request $request)
    {
        $get = $request->input();

        $alias = Str::snake($get['model'] ?? '') . 's';
        $class = loadClass($get['model'] ?? '');
        $keyword = $get['keyword'] ?? '';

        $object = $class->findWidgetItem([
            ['name', 'LIKE', '%' . $keyword . '%'],
        ], 1, $alias);

        return response()->json($object);
    }

    public function findPromotionObject(Request $request)
    {
        $get = $request->input();

        $model = $get['option']['model'] ?? '';
        $keyword = $get['search'] ?? '';
        $alias = Str::snake($model) . 's';
        $class = loadClass($model);


        $object = $class->findWidgetItem([
            ['name', 'LIKE', '%' . $keyword . '%'],
        ], 1, $alias);


        $temp = array_map(function ($val) {
            return [
                'id' => $val->id,
                'text' => $val->name,
            ];
        }, $object->toArray());

        return response()->json(['items' => $temp]);
    }




    public function getPromotionConditionValue(Request $request)
    {
        try {
            $get = $request->input();
            switch ($get['value']) {
                case 'staff_take_care_customer':
                    $class = loadClass('User');
                    $object = $class->all()->toArray();
                    break;
                case 'customer_group':
                    $class = loadClass('CustomerCatalogue');
                    $object = $class->all()->toArray();
                    break;
                case 'customer_gender':
                    $object = __('module.gender');
                    break;
                case 'customer_birthday':
                    $object = __('module.day');
                    break;
            }
            $temp = [];
            if (!is_null($object) && count($object)) {
                foreach ($object as $key => $val) {
                    $temp[] = [
                        'id' => $val['id'],
                        'text' => $val['name'],
                    ];
                }
            }
            return response()->json([
                'data' => $temp,
                'error' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'messages' =>  $e->getMessage(),
            ]);
        }
    }

    public function findInformationObject(Request $request)
    {
        $get = $request->input();
        $class = loadClass($get['model']);
        $object = $class->searchInformation([
            ['name', 'LIKE', '%' . $get['keyword'] . '%'],
            ['phone', 'LIKE', '%' . $get['keyword'] . '%', 'orWhere'],
            ['code', 'LIKE', '%' . $get['keyword'] . '%', 'orWhere'],
        ]);

        return response()->json($object);
    }

    public function DasnboardchartRevenueAndCost(Request $request)
    {
        $chart = $this->orderRepository->DasnboardchartRevenueAndCost();
        return response()->json($chart);
    }
}
