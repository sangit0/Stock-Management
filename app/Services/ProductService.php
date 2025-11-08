<?php

namespace App\Services;

use App\Product;
use App\ProductCategory;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Retrieve every product category ordered by name to keep dropdowns predictable.
     */
    public function listCategories(): Collection
    {
        return ProductCategory::query()
            ->orderBy('name')
            ->get();
    }

    /**
     * Persist a brand-new product category.
     */
    public function createCategory(string $name): ProductCategory
    {
        return ProductCategory::create([
            'name' => $name,
        ]);
    }

    /**
     * Update the name of an existing category.
     */
    public function renameCategory(int $categoryId, string $name): ProductCategory
    {
        $category = ProductCategory::findOrFail($categoryId);
        $category->update([
            'name' => $name,
        ]);

        return $category;
    }

    /**
     * Toggle the publication status of a category.
     */
    public function setCategoryPublication(int $categoryId, bool $published): ProductCategory
    {
        $category = ProductCategory::findOrFail($categoryId);
        $category->update([
            'status' => $published ? 1 : 0,
        ]);

        return $category;
    }

    /**
     * Fetch every product that is still in stock alongside its relationships.
     */
    public function listAvailableProducts(): Collection
    {
        return Product::query()
            ->where('availableQty', '!=', 0)
            ->with(['brand', 'styles', 'stockID'])
            ->get();
    }
}
