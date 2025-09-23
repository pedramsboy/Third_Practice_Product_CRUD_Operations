<?php
declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\GoogleAuth;

class GoogleAuthentication
{
    public function __construct(private GoogleAuth $googleAuth)
    {
    }

    public function redirect(Request $request, Response $response): Response
    {
        $authUrl = $this->googleAuth->getAuthUrl();
        return $response->withHeader('Location', $authUrl)->withStatus(302);
    }

    public function callback(Request $request, Response $response): Response
    {
        $code = $request->getQueryParams()['code'] ?? '';

        if (empty($code)) {
            return $response->withHeader('Location', '/login?error=google_auth_failed')->withStatus(302);
        }

        try {
            $googleUser = $this->googleAuth->handleCallback($code);
            $user = $this->googleAuth->findOrCreateUser($googleUser);

            $_SESSION['user_id'] = $user['id'];

            return $response->withHeader('Location', '/')->withStatus(302);
        } catch (\Exception $e) {
            return $response->withHeader('Location', '/login?error=google_auth_failed')->withStatus(302);
        }
    }
}