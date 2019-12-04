<?php

require_once '../vendor/autoload.php';

use App\LoginInfo;
use App\NotificationRenderer\TwigRenderer;
use App\TelegramNotifier;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Rakit\Validation\Validator;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;

function sendJson(array $response): void
{
    header('Content-Type: application/json');
    file_put_contents('php://output', json_encode($response));
}

function env(string $variable, string $default = ''): string
{
    $result = getenv($variable);
    return $result ? $result : $default;
}

$log = new Logger('telegram-notifier');
$loginInfo = new LoginInfo($_GET['host'] ?? '', $_GET['server'] ?? '', $_GET['user'] ?? '');

try {
    if (env('ACCESS_KEY') && $_GET['key'] != env('ACCESS_KEY')) {
        throw new Exception('Invalid key!', 401);
    }

    if (!env('TELEGRAM_BOT_TOKEN')) {
        throw new Exception('Invalid telegram bot token', 500);
    }

    $validator = new Validator();
    $validation = $validator->make($loginInfo->toArray(), [
        'host' => 'required|ip',
        'server' => 'required|ip',
        'user' => 'required|regex:/^[a-z_]([a-z0-9_-]{0,31})/',
    ]);

    $validation->validate();
    if ($validation->fails()) {
        $errors = $validation->errors();
        throw new Exception($errors->all()[0], 400);
    }


    $ids = env('TELEGRAM_CHAT_IDS', '');
    $userIds = $ids != '' ? explode(',', $ids) : [];
    $userIds = array_map(function ($userId) {
        return (int) $userId;
    }, $userIds);

    $bot = new BotApi(env('TELEGRAM_BOT_TOKEN'));
    $renderer = new TwigRenderer();

    $log->pushHandler(new StreamHandler('php://stderr', (int) env('LOGGING_LEVEL', Logger::INFO)));

    $notifier = new TelegramNotifier($loginInfo, $bot, $renderer);

    $sentToUserIds = [];

    foreach ($userIds as $userId) {
        try {
            $notifier->sendNotification($userId);

            $sentToUserIds[] = $userId;
            $log->debug('Notification sent to user', [
                'userId' => $userId,
            ]);
        } catch (Exception $exception) {
            $log->warning('Failed to send notification', [
                'userId' => $userId,
            ]);
        }
    }

    $logContext = array_merge($loginInfo->toArray(), ['userIds' => $sentToUserIds]);
    $response = [
        'ok' => true,
        'data' => [
            'message' => 'Notifications sent successfully!',
        ],
        'error' => null
    ];
    if (count($userIds) === count($sentToUserIds)) {
        if (empty($userIds)) {
            $log->info('Noting to send', $logContext);
            $response['data']['message'] = 'Nothing to send';
        } else {
            $log->info('Notifications sent successfully', $logContext);
        }
    } else if (!empty($sentToUserIds)) {
        $log->info('Notifications sent partially', $logContext);
        $response['data']['message'] = 'Notifications sent partially';
    } else {
        throw new Exception('Notifications sent unsuccessfully', 500);
    }

    sendJson($response);
} catch (Exception $exception) {
    $log->emergency($exception->getMessage(), $loginInfo->toArray());
    $response = [
        'ok' => false,
        'data' => null,
        'error' => [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ],
    ];

    if (in_array($exception->getCode(), [
        100, 101,
        200, 201, 202, 203, 204, 205, 206,
        300, 301, 302, 303, 304, 305, 306, 307,
        400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417,
        500, 501, 502, 503, 504, 505,
    ])) {
        http_response_code($exception->getCode());
    } else {
        http_response_code(500);
    }
    sendJson($response);
}