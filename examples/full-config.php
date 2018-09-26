<?php

declare(strict_types=1);

use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;

return  [
    'debug' => true,
    'translation' => [
        'locale' => 'en',
        'fallback' => ['en', 'fr', 'pl'],
        'cache_dir' => __DIR__ . '/../cache/',
        'formatter' => MessageFormatter::class,
        'loaders' => [
            'array' => ArrayLoader::class,
            'json' => JsonFileLoader::class,
            'php' => PhpFileLoader::class,
            'xliff' => XliffFileLoader::class,
            'yaml' => YamlFileLoader::class,
        ],
        'resources' => [
            [
                'format' => 'array',
                'resource' => ['Hello %name%' => 'Hello %name%'],
                // in case the 'locale' is not provided, the default locale will be used
                // which in this case is 'en'
            ], [
                'format' => 'php',
                'resource' => __DIR__ . '/resources/resource.php',
                'locale' => 'es',
            ], [
                'format' => 'yaml',
                'resource' => __DIR__ . '/resources/resource.yaml',
                'locale' => 'fr',
                'domain' => 'messages', // default domain
            ], [
                'format' => 'xliff',
                'resource' => __DIR__ . '/resources/resource.xml',
                'locale' => 'pl',
            ], [
                'format' => 'json',
                'resource' => __DIR__ . '/resources/resource.json',
                'locale' => 'ar',
            ],
        ],
    ],
];
