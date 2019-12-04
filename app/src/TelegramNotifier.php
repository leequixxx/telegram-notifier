<?php


namespace App;

use App\NotificationRenderer\Renderer;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;

class TelegramNotifier
{
    /**
     * @var LoginInfo $loginInfo
     */
    private $loginInfo;

    /**
     * @var BotApi $bot
     */
    private $bot;

    /**
     * @var Renderer $renderer
     */
    private $renderer;

    /**
     * TelegramNotifier constructor.
     * @param LoginInfo $loginInfo
     * @param BotApi $bot
     * @param Renderer $renderer of message
     */
    public function __construct(LoginInfo $loginInfo, BotApi $bot, Renderer $renderer)
    {
        $this->loginInfo = $loginInfo;
        $this->bot = $bot;
        $this->renderer = $renderer;
    }

    /**
     * @param string $userId id of telegram user
     * @throws Exception
     */
    public function sendNotification(string $userId): void
    {
        $message = $this->renderer->render(
          $this->loginInfo->getHost(),
          $this->loginInfo->getServer(),
          $this->loginInfo->getUser(),
        );

        $this->bot->sendMessage($userId, $message, 'html');
    }
}