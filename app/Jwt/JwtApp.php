<?php

namespace App\Jwt;

use App\Models\ApiApp;
use App\Models\ApiAppToken;
use Carbon\Carbon;
use Hidehalo\Nanoid\Client;
use Lcobucci\JWT\Token\Plain;

class JwtApp extends BaseJwt
{
    protected string $prv = 'app';
    protected string $prvRefresh = 'app/refresh';
    private ?Plain $currentToken = null;
    private ?ApiAppToken $currentAppToken = null;
    protected $app_id = null;
    protected ?ApiApp $apiApp = null;

    public function __construct(Client $nanoid)
    {
        parent::__construct($nanoid);
    }

    public function parseToken(?string $strToken): ?Plain {
        $token = parent::parseToken($strToken);
        $this->currentToken = $token;
        return $token;
    }

    public function checkToken(?Plain $token, bool $refresh): ?ApiAppToken
    {
        if(!$token) return null;
        if (($refresh && $token->claims()->get('prv') != $this->prvRefresh) || (!$refresh && $token->claims()->get('prv') != $this->prv)) {
            return null;
        }
        $data = ApiAppToken::where('app_id', $token->claims()->get('sub'))
            ->where('hash', $token->claims()->get('jti'))
            ->first();
        $this->currentAppToken = $data;
        if($data) $this->app_id = $data->app_id;
        return $data;
    }

    public function setAppId($app_id) {
        $this->app_id = $app_id;
    }

    public function getAppId() {
        return $this->app_id;
    }

    public function getApiApp(): ?ApiApp {
        if(!$this->apiApp) $this->apiApp = ApiApp::where('id', $this->app_id)->first();
        return $this->apiApp;
    }

    public function generate(ApiApp $app)
    {
        $basic = $this->getJwtBasicData();
        ApiAppToken::create([
            'app_id' => $app->id,
            'hash' => $basic->hash,
            'expired_at' => new Carbon($basic->expiredRefresh)
        ]);
        $token = $this->generateToken($app->id, $basic, false)
            ->getToken($this->config->signer(), $this->config->signingKey());
        $refresh = $this->generateToken($app->id, $basic, true)
            ->getToken($this->config->signer(), $this->config->signingKey());

        return ['access_token' => $token->toString(), 'refresh_token' => $refresh->toString()];
    }

    public function refresh()
    {
        $basic = $this->getJwtBasicData();
        $this->currentAppToken->hash = $basic->hash;
        $this->currentAppToken->save();
        $token = $this->generateToken($this->currentToken->claims()->get('sub'), $basic, false)
            ->getToken($this->config->signer(), $this->config->signingKey());
        $refresh = $this->generateToken($this->currentToken->claims()->get('sub'), $basic, true)
            ->getToken($this->config->signer(), $this->config->signingKey());

        return ['access_token' => $token->toString(), 'refresh_token' => $refresh->toString()];
    }

    public function logout()
    {
        $this->currentAppToken->delete();
    }
}
