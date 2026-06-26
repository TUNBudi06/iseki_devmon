<?php

namespace App\Services;

use App\Contracts\JwtServiceInterface;
use Firebase\JWT\JWT;

class JwtService implements JwtServiceInterface
{
    private string $secret;

    private string $issuer;

    public function __construct()
    {
        $this->secret = config('app.jwt_secret');
        $this->issuer = config('app.name', 'iseki_devmon');
    }

    public function encode(string $deviceName, string $deviceId): string
    {
        $now = time();

        return JWT::encode(
            [
                'iss' => $this->issuer,
                'sub' => $deviceName,
                'aud' => $deviceId,
                'iat' => $now,
                'jti' => hash('sha256', $deviceId.$deviceName.$now.uniqid('', true)),
            ],
            $this->secret,
            'HS512',
        );
    }

    public function decode(string $token): ?object
    {
        $headers = (object) ['HS512'];
        try {
            return JWT::decode($token, $this->secret, $headers);
        } catch (\Exception) {
            return null;
        }
    }

    public function hash(string $jwt): string
    {
        return hash('sha256', $jwt);
    }

    public static function verify(string $qrcode): bool
    {
        $data = explode(';', $qrcode);
        debugbar()->log($data);

        return false;
    }
}
