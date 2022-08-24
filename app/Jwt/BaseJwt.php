<?php

namespace App\Jwt;

use App\Jwt\Model\JwtBasicData;
use App\Models\User;
use App\Models\UserToken;
use Carbon\Carbon;
use DateTimeImmutable;
use Hidehalo\Nanoid\Client;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BaseJwt
{
    protected InMemory $key;
    protected string $prv;
    protected string $prvRefresh;
    protected Configuration $config;
    protected Client $nanoid;

    public function __construct(Client $nanoid)
    {
        $this->key = InMemory::plainText(env('JWT_SECRET'));
        $this->config = Configuration::forSymmetricSigner(new Sha256(), $this->key);
        $this->nanoid = $nanoid;
        $this->config->setValidationConstraints(
            new SignedWith($this->config->signer(), $this->config->signingKey()),
            new ValidAt(SystemClock::fromSystemTimezone())
        );
    }

    public function parseToken(string $strToken): ?Plain
    {
        if(substr($strToken, 0, 6) != 'Bearer') return null;
        try {
            $token = $this->config->parser()->parse(substr($strToken, 7));
            if (!$token instanceof Plain || !$this->config->validator()->validate($token, ...$this->config->validationConstraints())) {
                return null;
            }
            return $token;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function getJwtBasicData(): JwtBasicData {
        return new JwtBasicData($this->nanoid);
    }

    protected function generateToken($sub, JwtBasicData $basic, bool $refresh): Builder {
        return $this->config->builder()
            ->issuedBy(env('APP_URL'))
            ->identifiedBy($basic->hash)
            ->relatedTo($sub)
            ->withClaim('prv', $refresh ? $this->prvRefresh : $this->prv)
            ->issuedAt($basic->now)
            ->canOnlyBeUsedAfter($basic->now)
            ->expiresAt($refresh ? $basic->expiredRefresh : $basic->expired);
    }

    public function logout()
    {
    }
}
