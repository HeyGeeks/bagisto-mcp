<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Illuminate\Support\Facades\Auth;

class CustomerProfileTool extends BaseTool
{
    public function name(): string
    {
        return 'customer.profile';
    }

    public function description(): string
    {
        return 'Get the authenticated customer profile information including name, email, and account details. Requires a valid Bearer token from customer.login. Use this tool when the user asks about their account, profile, or personal information.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
            'required' => [],
            'description' => 'No input required. Authentication is handled via Bearer token in the Authorization header.',
        ];
    }

    public function execute(array $arguments): array
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return [
                'authenticated' => false,
                'error' => 'Unauthenticated. Please provide a valid Bearer token obtained from customer.login.',
            ];
        }

        return [
            'authenticated' => true,
            'profile' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone ?? null,
                'gender' => $user->gender ?? null,
                'group' => $user->group->name ?? 'General',
                'created_at' => $user->created_at->toIso8601String(),
            ],
        ];
    }
}
