<?php

class AuthMiddleware
{

    public function verifyToken($token)
    {
        if (!$token)
            Flight::halt(401, "Missing authentication header");
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            Flight::halt(401, 'Invalid token format');
        }
        list($h64, $p64, $s64) = $parts;
        $headerJson = $this->base64url_decode($h64);
        $payloadJson = $this->base64url_decode($p64);
        $signature = $this->base64url_decode_raw($s64);

        $expectedSignature = hash_hmac('sha256', $h64 . '.' . $p64, Config::JWT_SECRET(), true);
        if (!hash_equals($expectedSignature, $signature)) {
            Flight::halt(401, 'Invalid token signature');
        }

        $payload = json_decode($payloadJson);
        if (!$payload) {
            Flight::halt(401, 'Invalid token payload');
        }
        if (isset($payload->exp) && time() >= $payload->exp) {
            Flight::halt(401, 'Token expired');
        }

        Flight::set('user', $payload->user);
        Flight::set('jwt_token', $token);
        return TRUE;
    }

    public function authorizeRole($requiredRole)
    {
        $user = Flight::get('user');
        if ($user->role !== $requiredRole) {
            Flight::halt(403, 'Access denied: insufficient privileges');
        }
    }

    public function authorizeRoles($roles)
    {
        $user = Flight::get('user');
        if (!in_array($user->role, $roles)) {
            Flight::halt(403, 'Forbidden: role not allowed');
        }
    }

    function authorizePermission($permission)
    {
        $user = Flight::get('user');
        if (!in_array($permission, $user->permissions)) {
            Flight::halt(403, 'Access denied: permission missing');
        }
    }

    private function base64url_decode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private function base64url_decode_raw($data)
    {
        return $this->base64url_decode($data);
    }
}
