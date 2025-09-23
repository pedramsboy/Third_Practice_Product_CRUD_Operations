<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class ParsePatchFormData
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');
        $method = $request->getMethod();

        // Check if it's a PATCH/PUT request with form-data
        if (in_array($method, ['PATCH', 'PUT']) &&
            strpos($contentType, 'multipart/form-data') !== false) {

            // Get the current parsed body
            $parsedBody = $request->getParsedBody();

            // If the parsed body is empty (which it will be for PATCH/PUT), parse it manually
            if (empty($parsedBody)) {
                $parsedBody = $this->parseMultipartFormData($request);
                $request = $request->withParsedBody($parsedBody);
            }
        }

        return $handler->handle($request);
    }

    private function parseMultipartFormData(Request $request): array
    {
        $body = $request->getBody()->getContents();
        $contentType = $request->getHeaderLine('Content-Type');

        // Extract boundary from Content-Type
        preg_match('/boundary=(.*)$/', $contentType, $matches);
        $boundary = $matches[1] ?? null;

        if (!$boundary) {
            return [];
        }

        $data = [];
        $parts = explode("--$boundary", $body);

        foreach ($parts as $part) {
            if (empty($part) || $part === "--\r\n") {
                continue;
            }

            // Parse each part to extract field name and value
            if (preg_match('/name="([^"]+)"\s*\r\n\r\n(.*)\r\n$/s', $part, $matches)) {
                $fieldName = $matches[1];
                $fieldValue = trim($matches[2]);
                $data[$fieldName] = $fieldValue;
            }
        }

        return $data;
    }

}