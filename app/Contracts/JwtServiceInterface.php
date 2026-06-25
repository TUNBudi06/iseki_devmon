<?php

namespace App\Contracts;

interface JwtServiceInterface
{
    public function encode(string $deviceName, string $deviceId): string;

    public function decode(string $token): ?object;

    public function hash(string $jwt): string;
}
