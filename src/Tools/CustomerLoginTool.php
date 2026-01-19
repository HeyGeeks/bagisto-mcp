<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Illuminate\Support\Facades\Hash;
use Webkul\Customer\Repositories\CustomerRepository;
use Illuminate\Validation\ValidationException;

class CustomerLoginTool extends BaseTool
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function name(): string
    {
        return 'customer.login';
    }

    public function description(): string
    {
        return 'Authenticate a customer using email and password. Returns an MCP session token on success. Use this tool when the user wants to log in, access their account, or perform authenticated actions. Rate-limited for security.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                    'description' => 'Customer email address',
                ],
                'password' => [
                    'type' => 'string',
                    'description' => 'Customer password (never stored or logged)',
                ],
            ],
            'required' => ['email', 'password'],
        ];
    }

    public function execute(array $arguments): array
    {
        $email = $arguments['email'] ?? null;
        $password = $arguments['password'] ?? null;

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
