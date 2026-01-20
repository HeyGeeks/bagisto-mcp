<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Illuminate\Support\Facades\Hash;
use Webkul\Customer\Repositories\CustomerRepository;
use Illuminate\Validation\ValidationException;

class CustomerLoginTool
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Authenticate a customer using email and password.
     * 
     * Returns an MCP session token on success.
     * Use this tool when the user wants to log in, access their account, or perform authenticated actions.
     * The returned 'token' must be passed as an argument to subsequent authenticated tool calls.
     * Rate-limited for security.
     * 
     * @param string $email Customer email address
     * @param string $password Customer password (never stored or logged)
     * @return array Login result
     */
    #[McpTool(name: 'customer_login')]
    public function login(string $email, string $password): array
    {
        if (!$email || !$password) {
            throw ValidationException::withMessages(['message' => 'Email and password are required.']);
        }

        $customer = $this->customerRepository->findOneByField('email', $email);

        if (!$customer || !Hash::check($password, $customer->password)) {
            return [
                'authenticated' => false,
                'error' => 'Invalid credentials',
            ];
        }

        $token = $customer->createToken('mcp-device')->plainTextToken;

        return [
            'authenticated' => true,
            'customer_id' => $customer->id,
            'token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Login successful. Use the token in Authorization header for authenticated requests.',
        ];
    }
}
