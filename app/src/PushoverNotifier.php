<?php


namespace App;


use App\NotificationRenderer\Renderer;
use Pushover;

class PushoverNotifier implements Notifier
{
    /**
     * @var LoginInfo $loginInfo
     */
    private $loginInfo;

    /**
     * @var Renderer $renderer
     */
    private $renderer;

    /**
     * @var Pushover
     */
    private $pushover;

    public function __construct(LoginInfo $loginInfo, Pushover $pushover, Renderer $renderer)
    {
        $this->loginInfo = $loginInfo;
        $this->pushover = $pushover;
        $this->renderer = $renderer;
    }

    /**
     * @inheritDoc
     */
    public function sendNotification(string $userId): void
    {
        $message = $this->renderer->render(
            $this->loginInfo->getHost(),
            $this->loginInfo->getServer(),
            $this->loginInfo->getUser()
        );

        $this->pushover->setUser($userId);

        $this->pushover->setHtml(1);
        $this->pushover->setTimestamp(time());
        $this->pushover->setTitle('SSH Connection opened');


        $this->pushover->setMessage($message);
    }
}