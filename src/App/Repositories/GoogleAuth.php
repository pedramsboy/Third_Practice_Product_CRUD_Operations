<?php

declare(strict_types=1);

namespace App\Repositories;

use Google\Client;
use Google\Service\Oauth2;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

class GoogleAuth
{
    public function __construct(
        private Client $client,
        private UserRepository $userRepository
    ) {
        $this->client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $this->client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $this->client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \Exception('Google authentication failed: ' . $token['error']);
        }

        $this->client->setAccessToken($token['access_token']);
        $oauth = new Oauth2($this->client);
        $userInfo = $oauth->userinfo->get();

        return [
            'google_id' => $userInfo->getId(),
            'email' => $userInfo->getEmail(),
            'name' => $userInfo->getName(),
            'avatar' => $userInfo->getPicture()
        ];
    }

    public function findOrCreateUser(array $googleUser): array
    {
        // Check if user exists by google_id
        $user = $this->userRepository->find('google_id', $googleUser['google_id']);

        if ($user) {
            return $user;
        }

        // Check if user exists by email
        $user = $this->userRepository->find('email', $googleUser['email']);

        if ($user) {
            // Update existing user with google_id
            $this->userRepository->updateGoogleId($user['id'], $googleUser['google_id']);
            return $user;
        }

        // Create new user
        $data = [
            'name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'password_hash' => '', // Empty for Google users
            'google_id' => $googleUser['google_id'],
            'avatar' => $googleUser['avatar']
        ];

        // Generate API key for Google users too
        $api_key = bin2hex(random_bytes(16));
        $encryption_key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);
        $data['api_key'] = Crypto::encrypt($api_key, $encryption_key);
        $data['api_key_hash'] = hash_hmac('sha256', $api_key, $_ENV['HASH_SECRET_KEY']);

        $this->userRepository->create($data);

        return $this->userRepository->find('google_id', $googleUser['google_id']);
    }
}