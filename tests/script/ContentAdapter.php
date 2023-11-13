<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use YouTube\Models\VideoDetails;

chdir(dirname(dirname(__DIR__)));

require 'vendor/autoload.php';

/** @var \Northmule\Container\Builder $container */
$container = require 'config/container.php';

$adapter = $container->get(\Coderun\Youtube\ContentAdapter\PresaverCom::class);
$videoDetails = VideoDetails::fromPlayerResponseArray([
    'videoDetails' => [
        'videoId'           => 'oYEwL6lI2xE',
        'title'             => 'Prorab Forward 241 MOS потерял мощность. Ремонт',
        'lengthSeconds'     => '519',
        'keywords'          =>
            [
                0 => 'Ремонт сварочного инвертора',
            ],
        'channelId'         => 'UCW6zZ8S2PzYGM0I5cA9ZU3Q',
        'isOwnerViewing'    => false,
        'shortDescription'  => '',
        'isCrawlable'       => true,
        'thumbnail'         =>
            [
                'thumbnails' =>
                    [],
            ],
        'allowRatings'      => true,
        'viewCount'         => '4147',
        'author'            => 'Мастер-электрик. Павлово.',
        'isPrivate'         => false,
        'isUnpluggedCorpus' => false,
        'isLiveContent'     => false,
    ]

]);
$video = new \Coderun\Common\ValueObject\Video('https://ya.ru', $videoDetails);

try {
    $content = $adapter->getContent($video);
} catch (Throwable $throwable) {
    echo $throwable->getMessage();
}

exit();