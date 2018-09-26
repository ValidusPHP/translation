<?php

declare(strict_types=1);

use Symfony\Component\Translation\Loader\YamlFileLoader;

return  [
    'debug' => false,
    'translation' => [
        'locale' => 'en',
        'fallback' => ['en', 'fr'],
        'cache_dir' => __DIR__ . '/../cache/',
        'loaders' => [
            'yaml' => YamlFileLoader::class,
        ],
        'resources' => [
            [
                'format' => 'yaml',
                'resource' => __DIR__ . '/resources/simple/messages.en.yaml',
                'locale' => 'en',
            ],
            [
                'format' => 'yaml',
                'resource' => __DIR__ . '/resources/simple/messages.fr.yaml',
                'locale' => 'fr',
            ],
            [
                'format' => 'yaml',
                'resource' => __DIR__ . '/resources/simple/validations.en.yaml',
                'locale' => 'en',
                'domain' => 'validations',
            ],
            [
                'format' => 'yaml',
                'resource' => __DIR__ . '/resources/simple/validations.fr.yaml',
                'locale' => 'fr',
                'domain' => 'validations',
            ],
        ],
    ],
];
