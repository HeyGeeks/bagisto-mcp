<?php

namespace HeyGeeks\BagistoMCP\Tools\Traits;

use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

trait AuthenticatedToolTrait
{
    protected function authenticate(string $token): bool
    {
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !$accessToken->tokenable) {
            return false;
        }

        Auth::guard('sanctum')->setUser($accessToken->tokenable);
        return true;
    }
}
