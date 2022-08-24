<?php

namespace App\Jwt;

use App\Jwt\Model\JwtBasicData;
use App\Models\User;
use App\Models\UserToken;
use Carbon\Carbon;
use Hidehalo\Nanoid\Client;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Token\Plain;

class JwtUser extends BaseJwt
{
    protected string $prv = 'user';
    protected string $prvRefresh = 'user/refresh';
    private JwtApp $jwtApp;
    private ?Plain $currentToken = null;
    private ?UserToken $currentUserToken = null;

    public function __construct(Client $nanoid, JwtApp $jwtApp)
    {
        parent::__construct($nanoid);
        $this->jwtApp = $jwtApp;
    }

    public function parseToken(?string $strToken): ?Plain {
        $token = parent::parseToken($strToken);
        $this->currentToken = $token;
        return $token;
    }

    public function checkToken(?Plain $token, bool $refresh): ?UserToken
    {
        if(!$token) return null;
        if (($refresh && $token->claims()->get('prv') != $this->prvRefresh) || (!$refresh && $token->claims()->get('prv') != $this->prv)) {
            return null;
        }
        $data = UserToken::where('user_id', $token->claims()->get('sub'))
            ->where('hash', $token->claims()->get('jti'))
            ->first();
        $this->currentUserToken = $data;
        $this->jwtApp->setAppId($token->claims()->get('api'));
        return $data;
    }

    public function maybeGuest(?Plain $token, bool $refresh): bool
    {
        return $this->jwtApp->checkToken($token, $refresh) || $this->checkToken($token, $refresh);
    }

    public function getUser(): ?User {
        if(!$this->currentUserToken) return null;
        return User::where('id', $this->currentUserToken->user_id)->first();
    }

    protected function generateToken($sub, JwtBasicData $basic, bool $refresh): Builder {
        return parent::generateToken($sub, $basic, $refresh)->withClaim('api',$this->jwtApp->getAppId());
    }

    public function generate(User $user)
    {
        $basic = $this->getJwtBasicData();
        UserToken::create([
            'user_id' => $user->id,
            'hash' => $basic->hash,
            'expired_at' => new Carbon($basic->expiredRefresh)
        ]);
        $token = $this->generateToken($user->id, $basic, false)
            ->getToken($this->config->signer(), $this->config->signingKey());
        $refresh = $this->generateToken($user->id, $basic, true)
            ->getToken($this->config->signer(), $this->config->signingKey());

        return ['access_token' => $token->toString(), 'refresh_token' => $refresh->toString()];
    }

    public function refresh()
    {
        $basic = $this->getJwtBasicData();
        $this->currentUserToken->hash = $basic->hash;
        $this->currentUserToken->save();
        $token = $this->generateToken($this->currentToken->claims()->get('sub'), $basic, false)
            ->getToken($this->config->signer(), $this->config->signingKey());
        $refresh = $this->generateToken($this->currentToken->claims()->get('sub'), $basic, true)
            ->getToken($this->config->signer(), $this->config->signingKey());

        return ['access_token' => $token->toString(), 'refresh_token' => $refresh->toString()];
    }

    public function logout()
    {
        $this->currentUserToken->delete();
    }
}
