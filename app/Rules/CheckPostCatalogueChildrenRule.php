<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\PostCatalogue;
use App\Models\Post;

class CheckPostCatalogueChildrenRule implements ValidationRule
{

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasChildren = PostCatalogue::isNodeCheck($this->id);
        if ($hasChildren) {
            $fail('Không thể xóa do vẫn còn danh mục con.');
            return;
        }

        if (Post::hasPosts($this->id)) {
            $fail('Không thể xóa do danh mục này vẫn còn bài viết.');
        }
    }
}
