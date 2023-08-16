<?php

namespace EceoPos\Entity\Account;

abstract class AbstractPosAccount
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * Hesap modeli: regular, 3d, 3d_pay, 3d_host
     *
     * @var string
     */
    protected $model;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * Normal olmayan hesap modelleri için gerekli.
     *
     * @var string|null
     */
    protected $storeKey;

    /**
     * @var string
     */
    protected $lang;

    /**
     * Yapılandırma dosyasında kullanılan banka anahtarının adı:
     *
     * @var string
     */
    protected $bank;

    /**
     * AbstractPosAccount yapıcı metodu:
     *
     * @param  string  $bank
     * @param  string  $model
     * @param  string  $clientId
     * @param  string  $username
     * @param  string  $password
     * @param  string  $lang
     * @param  string|null  $storeKey
     */
    public function __construct(string $bank, string $model, string $clientId, string $username, string $password, string $lang, ?string $storeKey = null)
    {
        $this->model = $model;
        $this->clientId = $clientId;
        $this->username = $username;
        $this->password = $password;
        $this->storeKey = $storeKey;
        $this->lang = $lang;
        $this->bank = $bank;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getStoreKey(): ?string
    {
        return $this->storeKey;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @return string
     */
    public function getBank(): string
    {
        return $this->bank;
    }
}
