<?php
    require_once 'vendor/autoload.php';

    use BotMan\BotMan\BotMan;
    use BotMan\BotMan\BotManFactory;
    use BotMan\BotMan\Drivers\DriverManager;
    use BotMan\BotMan\Messages\Conversations\Conversation;
    use BotMan\BotMan\Messages\Outgoing\Question;
    use BotMan\BotMan\Messages\Outgoing\Actions\Button;
    use BotMan\BotMan\Messages\Incoming\Answer;
    use BotMan\BotMan\Cache\DoctrineCache;
    use Doctrine\Common\Cache\FilesystemCache;

    $config = [
        // Your driver-specific configuration
        // "telegram" => [
        //    "token" => "TOKEN"
        // ]
    ];

    DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

    $botman = BotManFactory::create($config);

    $botman->hears('hello', function (BotMan $bot) {
        $bot->reply('Hello yourself.');
    });
    
    // Start listening
    $botman->listen();


?>