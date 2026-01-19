<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Webkul\Category\Repositories\CategoryRepository;

class CategoryListTool extends BaseTool
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function name(): string
    {
        return 'categories.list';
    }

    public function description(): string
    {
        return 'List all product categories in the store. Use this tool when the user wants to browse categories, explore the catalog structure, or find products by category. Returns a hierarchical list of categories.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'parent_id' => [
                    'type' => 'integer',
                    'description' => 'Parent category ID to list children (optional, defaults to root)',
                ],
                'include_inactive' => [
                    'type' => 'boolean',
                    'description' => 'Include inactive categories (default: false)',
                    'default' => false,
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $arguments): array
    {
        $parentId = $arguments['parent_id'] ?? null;
        $includeInactive = $arguments['include_inactive'] ?? false;

        $query = $this->categoryRepository->query();

        if ($parentId) {
            $query->where('parent_id', $parentId);
        } else {
            // Get root categories (parent_id is null or root category)
            $rootCategory = $this->categoryRepository->findOneByField('parent_id', null);
            if ($rootCategory) {
                $query->where('parent_id', $rootCategory->id);
            }
        }

        if (!$includeInactive) {
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
