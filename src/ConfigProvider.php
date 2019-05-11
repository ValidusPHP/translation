<?php
/**
 * This File is Part of the Validus Translation package.
 *
 * @copyright (c) 2018 Validus <https://github.com/ValidusPHP/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Validus\Translation;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validus\Translation\Middleware\TranslatorMiddleware;
use Validus\Translation\Middleware\TranslatorMiddlewareFactory;

class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                Translator::class => TranslatorFactory::class,
                TranslatorMiddleware::class => TranslatorMiddlewareFactory::class,
            ],
            'aliases' => [
                TranslatorInterface::class => Translator::class,
                LegacyTranslatorInterface::class => Translator::class,
            ],
        ];
    }
}
