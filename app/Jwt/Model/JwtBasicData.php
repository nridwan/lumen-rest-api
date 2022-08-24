<?php

namespace App\Jwt\Model;

use DateTimeImmutable;
use Hidehalo\Nanoid\Client;

class JwtBasicData {
    public string $hash;
    public DateTimeImmutable $now;
    public DateTimeImmutable $expired;
    public DateTimeImmutable $expiredRefresh;

    public function __construct(Client $nanoid) {
        $this->hash = $nanoid->generateId();
        $this->now = new DateTimeImmutable();
        $this->expired = $this->now->modify('+' . env('JWT_TOKEN_LIFETIME', '0') . ' second');
        $this->expiredRefresh = $this->now->modify('+' . env('JWT_REFRESH_LIFETIME', '0') . ' second');
    }
}
