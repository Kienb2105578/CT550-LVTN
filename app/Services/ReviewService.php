<?php

namespace App\Services;

use App\Services\Interfaces\ReviewServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ReviewRepositoryInterface as ReviewRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Classes\ReviewNested;
use Illuminate\Support\Facades\Auth;

/**
 * Class AttributeService
 * @package App\Services
 */
class ReviewService extends BaseService implements ReviewServiceInterface
{
    protected $reviewRepository;

    public function __construct(
        ReviewRepository $reviewRepository,
    ) {
        $this->reviewRepository = $reviewRepository;
    }


    public function paginate($request)
    {
        $condition['keyword'] = addslashes($request->input('keyword'));

        $perPage = $request->integer('perpage');
        $reviews = $this->reviewRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'review/index'],
        );

        return $reviews;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->except('_token');

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $imagePaths = [];

                foreach ($images as $image) {
                    $fileName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('review'), $fileName);
                    $imagePaths[] = 'review/' . $fileName;
                }
                $payload['images'] = json_encode($imagePaths);
            }

            $payload['product_id'] = $payload['reviewable_id'];
            $user = Auth::guard('customer')->user();
            $payload['customer_id'] = $user->id;
            $this->reviewRepository->create($payload);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
        }
    }
    public function reply($id, $replyText)
    {
        DB::beginTransaction();
        try {
            $review = $this->reviewRepository->findById($id);
            $replies = json_decode($review->replies, true);
            if (!is_array($replies)) {
                $replies = [];
            }
            $newReply = [
                'reply_by' => auth()->id(),
                'reply_text' => $replyText,
                'created_at' => now()
            ];

            $replies[] = $newReply;
            $payload['replies'] = json_encode($replies);
            $this->reviewRepository->update($id, $payload);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
            die();
        }
    }


    private function paginateSelect()
    {
        return [
            'id',
            'reviewable_id',
            'customer_id',
            'product_id',
            'reviewable_type',
            'email',
            'replies',
            'phone',
            'fullname',
            'gender',
            'score',
            'description',
            'created_at',
        ];
    }
}
