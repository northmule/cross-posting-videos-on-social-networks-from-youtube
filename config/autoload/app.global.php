<?php

/**
 * Настройки приложения
 */
return [
    \Coderun\RuTube\ConfigProvider::CONFIG_KEY => [
        'token'          => getenv('APP_RUTUBE_TOKEN'),
        'user_id'     => getenv('APP_RUTUBE_USER_ID'),
    ],
    \Coderun\Vkontakte\ConfigProvider::CONFIG_KEY => [
        'api' => [
            'group_id' => getenv('APP_VK_GROUP_ID'),
            'token' => getenv('APP_VK_TOKEN'),
            'album_id' => getenv('APP_VK_ALBUM_ID'),
            'version' => getenv('APP_VK_VERSION'),
            'archive_album_id' => getenv('APP_VK_ALBUM_ID_FROM_ALL_IMPORT'),
            'wallpost' => getenv('APP_VK_WALL_POST'),
        ],
    ],
    \Coderun\Telegram\ConfigProvider::CONFIG_KEY => [
        'channel' => getenv('APP_TELEGRAM_CHANNEL_NAME'),
        'token' => getenv('APP_TELEGRAM_BOT_API_TOKEN'),
    ],
    \Coderun\Youtube\ConfigProvider::CONFIG_KEY => [
        'channels' => getenv('APP_YOUTUBE_CHANELS'),
        'token' => getenv('APP_YOUTUBE_API_KEY'),
        'app_name' => getenv('APP_YOUTUBE_APPLICATION_NAME'),
        'max_result' => getenv('APP_YOUTUBE_VIDEO_MAX_RESULT'),
        'order' => getenv('APP_YOUTUBE_VIDEO_ORDER'),
    ],
    \Coderun\Common\ConfigProvider::CONFIG_KEY => [
        'dir_history' => getenv('APP_DIR_HISTORY'),
        'dir_log' => getenv('APP_DIR_LOG'),
    ],
];
