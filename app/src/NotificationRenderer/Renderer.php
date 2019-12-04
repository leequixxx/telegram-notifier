<?php

namespace App\NotificationRenderer;

interface Renderer
{
    /**
     * Render notification message
     * @param string $host ip address of connected user
     * @param string $server ip address of server
     * @param string $user
     * @return string
     */
    public function render(string $host, string $server, string $user): string;
}