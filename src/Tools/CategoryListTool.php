<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Webkul\Category\Repositories\CategoryRepository;

class CategoryListTool
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * List all product categories in the store.
     * 
     * Use this tool when the user wants to browse categories, explore the catalog structure, or find products by category.
     * Returns a hierarchical list of categories.
     * 
     * @param int|null $parent_id Parent category ID to list children (optional, defaults to root)
     * @param bool $include_inactive Include inactive categories (default: false)
     * @return array List of categories
     */
    #[McpTool(name: 'categories_list')]
    public function list(?int $parent_id = null, bool $include_inactive = false): array
    {
        $query = $this->categoryRepository->query();

        if ($parent_id) {
            $query->where('parent_id', $parent_id);
        } else {
            // Get root categories (parent_id is null or root category)
            $rootCategory = $this->categoryRepository->findOneByField('parent_id', null);
            if ($rootCategory) {
                $query->where('parent_id', $rootCategory->id);
            }
        }

        if (!$include_inactive) {
            $query->where('status', 1);
        }

        $categories = $query->get();

        $result = [];
        foreach ($categories as $category) {
            $result[] = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => strip_tags($category->description ?? ''),
                'position' => $category->position,
                'status' => $category->status ? 'active' : 'inactive',
                'children_count' => $category->children()->count(),
            ];
        }

        return [
            'categories' => $result,
            'count' => count($result),
        ];
    }
}
