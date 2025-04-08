<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    public function __construct() {}

    public function create(Request $request)
    {
        $question = $request->input('message');

        $products = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.price',
                'products.image',
                'products.canonical',
                'product_catalogues.name as category_name',
                DB::raw('SUM(inventory_batches.quantity) as stock')
            )
            ->join('inventory_batches', 'products.id', '=', 'inventory_batches.product_id')
            ->leftJoin('product_catalogues', 'products.product_catalogue_id', '=', 'product_catalogues.id')
            ->where('inventory_batches.publish', 2)
            ->groupBy('products.id', 'products.name', 'products.price', 'products.image', 'products.canonical', 'product_catalogues.name')
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'link' => write_url($product->canonical),
                    'category' => $product->category_name,
                ];
            });

        return response()->json([
            'message' => $question,
            'products' => $products,
        ]);
    }

    public function getGeminiKey()
    {
        return response()->json([
            'key' => env('GEMINI_API_KEY')
        ]);
    }
}
