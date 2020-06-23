<?php namespace Syga\Config;

use CodeIgniter\Config\BaseConfig;

class JWT extends BaseConfig
{
    /**
     * @var string $key The key
     *
     */
    private $key = "sygaapi";

    /**
     * @var string $iss Issuer of the token
     *
     */
    private $iss = "localhost";

    /**
     * @var string $aud Audience of the token
     *
     */
    private $aud = "localhost";

    /**
     * @var string $nbf "Not before" is a future time when the token will become active
     *
     */
    private $nbf = 10;

    /**
     * @var string $nbf "Not before" is a future time when the token will become active
     *
     */
    // seconds * minutes
    private $exp = 60 * 30;

    /**
     * @var array $publicTokens Public tokens allowed
     *
     */
    private $publicTokens = [
        "c3lnYS1hcGktd2Vi" //syga-api-web (base64)
    ];

    /**
     * Key getter
     * @return string The key
     *
     */
    public function getKey()
    {
        return \base64_decode($this->key);
    }

    /**
     * Issuer key getter
     * @return string The issuer
     *
     */
    public function getIssuer(){
        return $this->iss;
    }

    /**
     * Issuer key getter
     * @return string The audience
     *
     */
    public function getAudience(){
        return $this->aud;
    }

    /**
     * Not before key getter
     * @return number The not before in seconds
     *
     */
    public function getNbfTime(){
        return (int) $this->nbf;
    }

    /**
     * Expire time key getter
     * @return number The expire time in seconds
     *
     */
    public function getExpireTime(){
        return (int) $this->exp;
    }

    /**
     * Public tokens allowed getter
     * @return array The public tokens
     *
     */
    public function getPublicTokens(){
        return $this->publicTokens;
    }


}
