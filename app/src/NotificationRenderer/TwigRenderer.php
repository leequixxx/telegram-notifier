<?php

namespace App\NotificationRenderer;

use Exception;
use Kagatan\IpAPI\IpAPI;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements Renderer
{
    /**
     * Render notification message
     * @param string $host ip address of connected user
     * @param string $server ip address of server
     * @param string $user
     * @return string
     * @throws Exception
     */
    public function render(string $host, string $server, string $user): string
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../views');
        $twig = new Environment($loader);

        $info = IpAPI::info($host);

        $country = 'Unknown';
        $city = 'Unknown';
        if (!isset($info['message'])) {
            $country = $info['country'];
            $city = $info['city'];
        }

        return $twig->render('message.twig', [
            'host' => $host,
            'country' => $country,
            'city' => $city,
            'server' => $server,
            'user' => $user,
        ]);
    }
}