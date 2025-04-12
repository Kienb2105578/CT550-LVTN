<?php

namespace App\Services;

use App\Services\Interfaces\SlideServiceInterface;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Class SlideService
 * @package App\Services
 */
class SlideService extends BaseService implements SlideServiceInterface
{
    protected $slideRepository;


    public function __construct(
        SlideRepository $slideRepository
    ) {
        $this->slideRepository = $slideRepository;
    }



    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = $request->integer('perpage');
        $slides = $this->slideRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'slide/index'],
        );
        return $slides;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {

            $payload = $request->only(['_token', 'name', 'keyword', 'setting', 'short_code']);
            $payload['item'] = $this->handleSlideItem($request);
            $slide = $this->slideRepository->create($payload);
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
            $payload = $request->only(['_token', 'name', 'keyword', 'setting', 'short_code']);
            $payload['item'] = $this->handleSlideItem($request);
            $this->slideRepository->update($id, $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
        }
    }
    private function handleSlideItem($request)
    {
        $slide = $request->input('slide');
        $temp = [];

        foreach ($slide['image'] as $key => $val) {
            $temp[] = [
                'image' => $val,
                'name' => $slide['name'][$key],
                'description' => $slide['description'][$key],
                'canonical' => $slide['canonical'][$key],
                'alt' => $slide['alt'][$key],
                'window' => isset($slide['window'][$key]) ? $slide['window'][$key] : '',
            ];
        }
        return $temp;
    }



    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $slide = $this->slideRepository->delete($id);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }


    public function updateSlideOrder($post)
    {
        $slideId = $post[0]['id'];

        $temp = array_map(function ($item) {
            unset($item['id']);
            return $item;
        }, $post);

        $payload['item'] = $temp;

        return $this->slideRepository->update($slideId, $payload);
    }


    public function converSlideArray(array $slide = []): array
    {
        $temp = [];
        $fields = ['image', 'description', 'window', 'canonical', 'name', 'alt'];
        foreach ($slide as $key => $val) {
            foreach ($fields as $field) {
                $temp[$field][] = $val[$field];
            }
        }
        return $temp;
    }

    private function paginateSelect()
    {
        return [
            'id',
            'name',
            'keyword',
            'item',
            'publish',
        ];
    }

    public function getSlide($array = [])
    {
        $slides = $this->slideRepository->findByCondition(...$this->getSlideAgrument($array));
        $temp = [];

        foreach ($slides as $val) {
            $item = $val->item;
            if (is_string($item)) {
                $item = json_decode($item, true);
            }
            if (!empty($item) && isset($item['image'])) {
                $item = [$item];
            }
            $temp[$val->keyword]['item'] = $item;
            $temp[$val->keyword]['setting'] = $val->setting;
        }

        return $temp;
    }


    private function getSlideAgrument($array)
    {
        return [
            'condition' => [
                config('apps.general.defaultPublish'),
            ],
            'flag' => TRUE,
            'relation' => [],
            'orderBy' => ['id', 'desc'],
            'param' => [
                'whereIn' => $array,
                'whereInField' => 'keyword'
            ]
        ];
    }
}
