<?php

namespace App;

class LoginInfo
{
    /**
     * @var string $host ip address of connected user
     */
    private $host;

    /**
     * @var string $server ip address of server
     */
    private $server;

    /**
     * @var string $user user who connecting
     */
    private $user;

    /**
     * LoginInfo constructor.
     * @param string $host
     * @param string $server
     * @param string $user
     */
    public function __construct(string $host, string $server, string $user)
    {
        $this->host = $host;
        $this->server = $server;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    public function toArray(): array
    {
        return [
            'host' => $this->host,
            'server' => $this->server,
            'user' => $this->user,
        ];
    }
}