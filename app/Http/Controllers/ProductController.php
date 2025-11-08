<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * Inject the service dependencies for the controller.
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Return every product style for consumption by the settings screens.
     */
    public function get(): JsonResponse
    {
        $categories = $this->productService->listCategories();

        return response()->json($categories);
    }

    /**
     * Persist a brand new product style.
     */
    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|string|max:255',
        ]);

        $category = $this->productService->createCategory($validated['data']);

        session()->flash('message', 'Product style created successfully!');

        return response()->json([
            'message' => 'Product style created successfully.',
            'category' => $category,
        ], 201);
    }

    /**
     * Update the name of an existing product style.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data.id' => 'required|integer|exists:productstyles,id',
            'data.name' => 'required|string|max:255',
        ]);

        $category = $this->productService->renameCategory(
            (int) $validated['data']['id'],
            $validated['data']['name']
        );

        session()->flash('message', 'Product style updated successfully!');

        return response()->json([
            'message' => 'Product style updated successfully.',
            'category' => $category,
        ]);
    }

    /**
     * Mark the product style as published.
     */
    public function publish(int $id): RedirectResponse
    {
        $this->productService->setCategoryPublication($id, true);

        return Redirect::to('settings')->with('message', 'Product style published successfully!');
    }

    /**
     * Mark the product style as unpublished.
     */
    public function unpublish(int $id): RedirectResponse
    {
        $this->productService->setCategoryPublication($id, false);

        return Redirect::to('settings')->with('message', 'Product style unpublished successfully!');
    }

    /**
     * Retrieve all available products for selection during sales.
     */
    public function getProductAll(): ProductResource
    {
        $products = $this->productService->listAvailableProducts();

        return new ProductResource($products);
    }
}
