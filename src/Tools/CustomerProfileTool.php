<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Illuminate\Support\Facades\Auth;
use HeyGeeks\BagistoMCP\Tools\Traits\AuthenticatedToolTrait;

class CustomerProfileTool
{
    use AuthenticatedToolTrait;

    /**
     * Get the authenticated customer profile information.
     * 
     * Includes name, email, and account details.
     * Requires a valid Bearer token from customer.login.
     * Use this tool when the user asks about their account, profile, or personal information.
     * 
     * @param string $token Authentication token from customer.login
     * @return array Profile details
     */
    #[McpTool(name: 'customer_profile')]
    public function getProfile(string $token): array
    {
        if (!$this->authenticate($token)) {
            return [
                'authenticated' => false,
                'error' => 'Unauthenticated or Invalid Token. Please provide a valid token obtained from customer.login.',
            ];
        }

        $user = Auth::guard('sanctum')->user();

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
