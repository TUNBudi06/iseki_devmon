<?php

namespace App\Http\Helper;

use Firebase\JWT\JWT;

class JwtManager
{
    public $iss = "iseki_devmon"; //issuer
    public $sub = ""; //subject
    public $aud = "devices"; //audience
    public $iat = 0; //issued at
    public $jti = '';

    private $jwt = '';

    public function __construct($deviceName,$deviceId)
    {
        $this->sub = $deviceName;
        $this->aud = $deviceId;
        $this->iat = time();
        $this->jti = hash("sha256", $deviceId.$this->sub.$this->iat.time());
        $jwt = JWT::encode(
            [
                "iss" => $this->iss,
                "sub" => $this->sub,
                "aud" => $this->aud,
                "iat" => $this->iat,
                "jti" => $this->jti
            ],config('app.jwt_secret')
            ,'HS512'
        );
    }

    public function encode(){
        return $this->jwt;
    }

    public function decode($token){
        try {
            $decoded = JWT::decode($token, config('app.jwt_secret'), ['HS512']);
            return $decoded;
        } catch (\Exception $e) {
            return null; // Invalid token
        }
    }

    public function hashJwt() {
        return hash("sha256", $this->jwt);
    }
}
