<?php

namespace App\Script;

use Composer\Script\Event;
use Symfony\Component\Dotenv\Dotenv;

class PortChange
{
    public static function change(Event $event)
    {
        $sub_name = 'port-change';
        $args = $event->getArguments();
        if (!empty($args)) {
            $input = $args[0];

            $path = dirname(__DIR__, 2) . '/.env';
            $dotenv = new Dotenv();
            $dotenv->load($path);

            try {
                if (file_exists($path)) {
                    file_put_contents($path, str_replace(
                        'APP_URL=' . $_ENV['APP_URL'],
                        'APP_URL=' . 'http://localhost:' . $input,
                        file_get_contents($path)
                    ));
                }
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
        }
    }
}
