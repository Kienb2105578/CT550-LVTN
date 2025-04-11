<?php

if (!function_exists('pre')) {
    function pre($data = '')
    {
        echo '<pre>';
        print_r($data);
        echo '<pre>';
        die();
    }
}


/*
*
*   Phương thức thanh toán VnPay
*
*/


if (!function_exists('vnpayConfig')) {
    function vnpayConfig()
    {
        return [
            'vnp_Url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'vnp_Returnurl' => write_url('return/vnpay'),
            'vnp_TmnCode' => 'RLE42FCR',
            'vnp_HashSecret' => 'OQPUUZRVSSJASOQVUQHHURHBXGDIMBTU',
            'vnp_apiUrl' => 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html',
            'apiUrl' => 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'
        ];
    }
}

/*
*
*   Phương thức thanh toán momo
*
*/

if (!function_exists('momoConfig')) {
    function momoConfig()
    {
        return [
            'partnerCode' => 'MOMOBKUN20180529',
            'accessKey' => 'klm05TvNBzhg7h7j',
            'secretKey' => 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa',
        ];
    }
}


if (!function_exists('execPostRequest')) {
    function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

/*
*
*   Hàm định dạng lại các biểu đồ trang Tổng Quan
*
*/

if (!function_exists('convertRevenueChartData')) {
    function convertRevenueChartData($chartData, $data = 'monthly_revenue', $label = 'month', $text = 'Tháng')
    {
        $newArray = [];
        if (!is_null($chartData) && count($chartData)) {
            foreach ($chartData as $key => $val) {
                $newArray['data'][] = $val->{$data};
                $newArray['label'][] = $text . ' ' . $val->{$label};
            }
        }
        return $newArray;
    }
}

/*
*
*   Phương thức hiện image ra frontend
*
*/

if (!function_exists('image')) {
    function image($image)
    {


        if (is_null($image)) return 'backend/img/not-found.jpg';

        $image = str_replace('/public/', '/', $image);

        return $image;
    }
}


/*
*
*   Phương thức hiện Giá Price
*
*/

if (!function_exists('convert_price')) {
    function convert_price(mixed $price = '', $flag = false)
    {
        if ($price === null) return 0;
        return ($flag === false) ? str_replace('.', '', $price) : number_format($price, 0, ',', '.');
    }
}

/*
*
*   Phương thức Tính toán và hiển thị giá sản phẩm khi giảm
*
*/

if (!function_exists('getPercent')) {
    function getPercent($product = null, $discountValue = 0)
    {
        return ($product->price > 0) ? round($discountValue / $product->price * 100) : 0;
    }
}

if (!function_exists('getPromotionPrice')) {
    function getPromotionPrice($priceMain = 0, $discountValue = 0)
    {
        return $priceMain - $discountValue;
    }
}


if (!function_exists('getPrice')) {
    function getPrice($product = null)
    {
        $result = [
            'price' => $product->price,
            'priceSale' => 0,
            'percent' => 0,
            'html' => ''
        ];

        if ($product->price == 0) {

            $result['html'] .= '<div class="price mt10">';
            $result['html'] .= '<div class="price-sale">Liên Hệ</div>';
            $result['html'] .= '</div>';
            return $result;
        }

        if (isset($product->promotions) && isset($product->promotions->discountType)) {
            $result['percent'] = getPercent($product, $product->promotions->discount);
            if ($product->promotions->discountValue > 0) {
                $result['priceSale'] = getPromotionPrice($product->price, $product->promotions->discount);
            }
        }
        $result['html'] .= '<div class="price uk-flex uk-flex-middle mt10">';
        $result['html'] .= '<div class="price-sale">' . (($result['priceSale'] > 0) ? convert_price($result['priceSale'], true) : convert_price($result['price'], true)) . 'đ</div>';
        if ($result['priceSale'] > 0) {
            $result['html'] .= '<div class="price-old uk-flex uk-flex-middle">' . convert_price($result['price'], true) . 'đ <div class="percent"><div class="percent-value">-' . $result['percent'] . '%</div></div></div>';
        }
        $result['html'] .= '</div>';
        return $result;
    }
}

/*
*
*   Phương thức Tính toán và hiển thị giá biến thể kho có khuyến mãi
*
*/

if (!function_exists('getVariantPrice')) {
    function getVariantPrice($variant, $variantPromotion)
    {
        $result = [
            'price' => $variant->price,
            'priceSale' => 0,
            'percent' => 0,
            'html' => ''
        ];

        if ($variant->price == 0) {

            $result['html'] .= '<div class="price mt10">';
            $result['html'] .= '<div class="price-sale">Liên Hệ</div>';
            $result['html'] .= '</div>';
            return $result;
        }

        if (!is_null($variantPromotion)) {
            $result['percent'] = getPercent($variant, $variantPromotion->discount);
            $result['priceSale'] = getPromotionPrice($variant->price, $variantPromotion->discount);
        }


        $result['html'] .= '<div class="price-sale">' . (($result['priceSale'] > 0) ? convert_price($result['priceSale'], true) : convert_price($result['price'], true)) . 'đ</div>';
        if ($result['priceSale'] !== $result['price']) {
            $result['html'] .= '<div class="price-old">' . convert_price($result['price'], true) . 'đ <div class="percent"><div class="percent-value">-' . $result['percent'] . '%</div></div></div>';
        }
        return $result;
    }
}

/*
*
*   Phương thức tính trung bình đánh giá (sao) và số lượng đánh giá của sản phẩm
*
*/

if (!function_exists('getReview')) {
    function getReview($product = null)
    {
        if (!$product) {
            return ['star' => 0, 'count' => 0];
        }

        $totalReviews = $product->reviews()->count();
        $totalRate = $product->reviews()->avg('score') ?? 0;
        $starPercent = ($totalReviews == 0) ? 0 : ($totalRate / 5 * 100);

        return [
            'star' => round($starPercent, 2),
            'count' => $totalReviews,
        ];
    }
}

/*
*
*   Phương thức chuyển đổi một mảng dữ liệu về dạng key-value theo cặp giá trị chỉ định
*
*/

if (!function_exists('convert_array')) {
    function convert_array($system = null, $keyword = '', $value = '')
    {
        $temp = [];
        if (is_array($system)) {
            foreach ($system as $key => $val) {
                $temp[$val[$keyword]] = $val[$value];
            }
        }
        if (is_object($system)) {
            foreach ($system as $key => $val) {
                $temp[$val->{$keyword}] = $val->{$value};
            }
        }

        return $temp;
    }
}

/*
*
*   Phương thức chuyển đổi định dạng ngày giờ
*
*/

if (!function_exists('convertDateTime')) {
    function convertDateTime(string $date = '', string $format = 'd/m/Y H:i', string $inputDateFormat = 'Y-m-d H:i:s')
    {
        $carbonDate = \Carbon\Carbon::createFromFormat($inputDateFormat, $date);

        return $carbonDate->format($format);
    }
}

/*
*
*   Phương thức ghi URL đầy đủ với suffix và domain
*
*/

if (!function_exists('write_url')) {
    function write_url($canonical = null, bool $fullDomain = true, $suffix = true)
    {
        $canonical = ($canonical) ?? '';
        if (strpos($canonical, 'http') !== false) {
            return $canonical;
        }
        $fullUrl = (($fullDomain === true) ? config('app.url') : '') . $canonical . (($suffix === true) ? config('apps.general.suffix') : '');
        return $fullUrl;
    }
}

/*
*
*   Phương thức tạo dữ liệu SEO từ model, hỗ trợ phân trang
*
*/

if (!function_exists('seo')) {
    function seo($model = null, $page = 1)
    {
        $canonical = ($page > 1)
            ? write_url($model->canonical, true, false) . '/trang-' . $page . config('apps.general.suffix')
            : write_url($model->canonical, true, true);

        $description = $model->meta_description ?? (function ($str, $n = 168) {
            $str = html_entity_decode($str);
            $str = strip_tags($str);
            return cutnchar($str, $n);
        })($model->descipriont ?? '');

        return [
            'meta_title' => $model->meta_title ?? $model->name,
            'meta_keyword' => $model->meta_keyword ?? '',
            'meta_description' => $description,
            'meta_image' => $model->image,
            'canonical' => $canonical,
        ];
    }
}

/*
*
*   Phương thức đệ quy để phân cấp dữ liệu dạng cây (tree) theo parent_id
*
*/

if (!function_exists('recursive')) {
    function recursive($data, $parentId = 0)
    {
        $temp = [];
        if (!is_null($data) && count($data)) {
            foreach ($data as $key => $val) {
                if ($val->parent_id == $parentId) {
                    $temp[] = [
                        'item' => $val,
                        'children' => recursive($data, $val->id)
                    ];
                }
            }
        }
        return $temp;
    }
}

/*
*
*   Phương thức dựng menu dạng cây (có dropdown) ở phía frontend
*
*/

if (!function_exists('frontend_recursive_menu')) {
    function frontend_recursive_menu(array $data = [], int $parentId = 0, int $count = 1, $type = 'html')
    {
        $html = '';
        if (isset($data) && !is_null($data) && count($data)) {
            if ($type == 'html') {
                foreach ($data as $key => $val) {
                    $name = $val['item']->name;
                    $canonical = write_url($val['item']->canonical, true, true);
                    $ulClass = ($count >= 1) ? 'menu-level__' . ($count + 1) : '';
                    $html .= '<li class="' . (($count == 1 && count($val['children'])) ? 'children' : '') . '">';
                    $html .= '<a href="' . (($name == 'Trang chủ') ? '.' : $canonical) . '" title="' . $name . '">' . $name . '</a>';
                    if (count($val['children'])) {
                        $html .= '<div class="dropdown-menu">';
                        $html .= '<ul class="uk-list uk-clearfix menu-style ' . $ulClass . '">';
                        $html .= frontend_recursive_menu($val['children'], $val['item']->parent_id,  $count + 1, $type);
                        $html .= '</ul>';
                        $html .= '</div>';
                    }
                    $html .= '</li>';
                }
                return $html;
            }
        }
        return $data;
    }
}

/*
*
*   Phương thức dựng giao diện menu dạng drag & drop (backend) dùng cho quản lý
*
*/

if (!function_exists('recursive_menu')) {
    function recursive_menu($data)
    {
        $html = '';
        if (count($data)) {
            foreach ($data as $key => $val) {
                $itemId = $val['item']->id;
                $itemName = $val['item']->name;
                $itemUrl = route('menu.children', ['id' => $itemId]);


                $html .= "<li class='dd-item' data-id='$itemId'>";
                $html .= "<div class='dd-handle'>";
                $html .= "<span class='label label-info'><i class='fa fa-arrows'></i></span> $itemName";
                $html .= "</div>";
                $html .= "<a class='create-children-menu' href='$itemUrl'> Quản lý menu con </a>";

                if (count($val['children'])) {
                    $html .= "<ol class='dd-list'>";
                    $html .= recursive_menu($val['children']);
                    $html .= '</ol>';
                }
                $html .= "</li>";
            }
        }
        return $html;
    }
}

/*
*
*   Phương thức tự động load class theo namespace dựa trên tên model
*
*/

if (!function_exists('loadClass')) {
    function loadClass(string $model = '', $folder = 'Repositories', $interface = 'Repository')
    {
        $serviceInterfaceNamespace = '\App\\' . $folder . '\\' . ucfirst($model) . $interface;
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        return $serviceInstance;
    }
}

/*
*
*   Phương thức cắt chuỗi theo số ký tự giới hạn, giữ nguyên từ cuối
*
*/

if (!function_exists('cutnchar')) {
    function cutnchar($str = NULL, $n = 320)
    {
        if (strlen($str) < $n) return $str;
        $html = substr($str, 0, $n);
        $html = substr($html, 0, strrpos($html, ' '));
        return $html . '...';
    }
}

/*
*
*   Phương thức sắp xếp mảng attribute_id theo số thứ tự và nối chuỗi bằng dấu phẩy
*
*/

if (!function_exists('sortAttributeId')) {
    function sortAttributeId(array $attributeId = [])
    {
        sort($attributeId, SORT_NUMERIC);
        $attributeId = implode(',', $attributeId);
        return $attributeId;
    }
}
