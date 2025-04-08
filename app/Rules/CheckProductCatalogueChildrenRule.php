<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\ProductCatalogue;
use App\Models\Product;

class CheckProductCatalogueChildrenRule implements ValidationRule
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
        // Kiểm tra danh mục có danh mục con hay không
        if (!ProductCatalogue::isNodeCheck($this->id)) {
            $fail('Không thể xóa vì danh mục vẫn còn danh mục con.');
            return;
        }

        // Kiểm tra danh mục có sản phẩm hay không
        if (Product::hasProducts($this->id)) {
            $fail('Không thể xóa vì danh mục vẫn còn sản phẩm.');
            return;
        }
    }
}
