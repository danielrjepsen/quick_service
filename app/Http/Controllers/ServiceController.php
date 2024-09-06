<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Product;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $unfinishedAssets = Asset::with(['services.product'])
            ->whereHas('services', function ($query) {
                $query->whereNull('completed_at');
            })
            ->paginate(2);

        $finishedAssets = Asset::with(['services.product'])
            ->whereDoesntHave('services', function ($query) {
                $query->whereNull('completed_at');
            })->get();

        return Inertia::render('Service', [
           'options' => Product::whereIsRoot()->get(),
           'unfinishedAssets' => $unfinishedAssets,
           'finishedAssets' => $finishedAssets
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'unique:assets'],
            'due_date' => ['date', 'nullable'],
            'planned_date' => ['required', 'date'],
            'products' => ['required', 'Array'],
        ])->validateWithBag('createService');

        $asset = Asset::create([
            'name' => $input['name']
        ]);

        foreach ($input['products'] as $item) {
            if (!is_int($item)) {
                $product = new Product;
                $product->name = $item;
                $product->save();
                $item = $product->id;
            }

            $service = new Service;
            $service->due_date = $input['due_date'];
            $service->planned_date = $input['planned_date'];
            $service->asset_id = $asset->id;
            $service->product_id = $item;
            $service->save();
        }

        return back(303);
    }

    public function getProductsNotOnAsset(Request $request, Asset $asset)
    {
        $input = $request->all();
        $Products = Product::whereIsRoot()
            ->where('name', 'like', '%' . $input['query'] . '%')
            ->whereDoesntHave('services', function ($query) use ($asset, $input) {
                $query->where('asset_id', $asset->id);
            })
            ->get();

        return response()->json([$Products]);
    }

    public function toggleService(Request $request)
    {
        $service = Service::query()->findOrFail($request->service);
        if ($service->completed_at === null) {
            $service->completed_at = Carbon::now()->toISOString();
        } else {
            $service->completed_at = null;
        }
        $service->save();

        return back(303);
    }

    public function toggleCompleteAllAsset(Request $request)
    {
        $completed = $request->completed;
        $services = Asset::findOrFail($request->asset_id)->services()->get();

        foreach ($services as $service) {
            if ($completed === true) {
                if ($service->completed_at === null) {
                    $service->completed_at = Carbon::now()->toISOString();
                }
            } else {
                $service->completed_at = null;
            }
            $service->save();
        }

        return back(303);
    }

    public function addNewService(Request $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'asset_id' => ['required'],
            'due_date' => ['date', 'nullable'],
            'planned_date' => ['required', 'date'],
            'products' => ['required', 'Array'],
        ])->validateWithBag('addNewService');

        foreach ($input['products'] as $item) {
            if (!is_int($item)) {
                $product = new Product;
                $product->name = $item;
                $product->save();
                $item = $product->id;
            }

            $service = new Service;
            $service->due_date = $input['due_date'];
            $service->planned_date = $input['planned_date'];
            $service->asset_id = $input['asset_id'];
            $service->product_id = $item;
            $service->save();
        }

        return back(303);
    }

    public function updateDate(Request $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'service_id' => ['required'],
            'due_date' => ['required_if:planned_date,null', 'nullable', 'date'],
            'planned_date' => ['required_if:due_date,null' ,'nullable', 'date'],
        ])->validateWithBag('updateDate');

        $service = Service::query()->where('id', $input['service_id'])->firstOrFail();

        isset($input['planned_date'])
            ? $service->planned_date = $input['planned_date']
            : $service->due_date = $input['due_date'];

        $service->save();

        return back(303);
    }
}
