<?php

namespace HeyGeeks\BagistoMCP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use HeyGeeks\BagistoMCP\Services\MCPServerFactory;
use Mcp\Server\Transport\StreamableHttpTransport;
use Http\Discovery\Psr17FactoryDiscovery;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MCPController extends Controller
{
    /**
     * Handle MCP Protocol requests.
     * Supports both SSE (GET) and JSON-RPC (POST).
     */
    public function handle(Request $request)
    {
        // 1. Convert Laravel Request to PSR-7 ServerRequest
        // We use Psr17FactoryDiscovery to find available PSR-17 factories
        // and PsrHttpFactory to convert the Symfony request to PSR-7.
        $psr17Factory = Psr17FactoryDiscovery::findPsr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);

        // 2. Setup Transport
        $transport = new StreamableHttpTransport($psrRequest);

        // 3. Setup Server
        $server = MCPServerFactory::create();

        // 4. Run Server
        // listen() returns a Psr\Http\Message\ResponseInterface
        $psrResponse = $server->run($transport);

        // 5. Convert PSR-7 Response back to Laravel/Symfony Response
        // Since it might be a Streamed Response (for SSE), we need to handle it carefully.

        $headers = $psrResponse->getHeaders();
        $statusCode = $psrResponse->getStatusCode();

        // If it's an Event Stream, we use StreamedResponse
        if ($psrResponse->hasHeader('Content-Type') && str_contains($psrResponse->getHeaderLine('Content-Type'), 'text/event-stream')) {
            return new StreamedResponse(function () use ($psrResponse) {
                $body = $psrResponse->getBody();
                while (!$body->eof()) {
                    echo $body->read(1024);
                    if (ob_get_level() > 0)
                        ob_flush();
                    flush();
                }
            }, $statusCode, $headers);
        }

        // Otherwise (JSON response), standard response
        return response(
            $psrResponse->getBody()->__toString(),
            $statusCode,
            $headers
        );
    }
}
