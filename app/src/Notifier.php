<?php

namespace App;

use Exception;

interface Notifier
{
    /**
     * @param string $userId id of user
     * @throws Exception
     */
    public function sendNotification(string $userId): void;
}