<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Product', [
            'options' => Product::query()->withCount('services')
                ->get()
                ->toTree()
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'product_id' => ['integer', 'nullable'],
        ])->validateWithBag('createProduct');

        if ($input['product_id'] === null) {
            Product::create([
                'name' => $input['name'],
            ])->save();

            return back(303);
        }

        $parent = Product::query()
            ->where('id', $input['product_id'])
            ->firstOrFail();

        $parent->children()->create([
            'name' => $input['name'],
        ]);

        return back(303);
    }

    public function addProductToParent(Request $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'product_id' => ['required', 'integer'],
        ])->validateWithBag('createProduct');

        $parent = Product::query()
            ->where('id', $input['product_id'])
            ->firstOrFail();

        $parent->children()->create([
            'name' => $input['name'],
        ]);

        return back(303);
    }

    public function updateProduct(Request $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'product_id' => ['required', 'integer'],
        ])->validateWithBag('updateProduct');

       $product = Product::query()
            ->where('id', $input['product_id'])
            ->firstOrFail();

       $product->name = $input['name'];
       $product->save();

        return back(303);
    }
}
