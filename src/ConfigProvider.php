<?php
/**
 * This File is Part of the Polar Translation package.
 *
 * @copyright (c) 2018 Polar <https://github.com/PolarOSS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Polar\Translation;

use Polar\Translation\Middleware\TranslatorMiddleware;
use Polar\Translation\Middleware\TranslatorMiddlewareFactory;
use Symfony\Component\Translation\TranslatorInterface;

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
                TranslatorInterface::class => TranslatorFactory::class,
                TranslatorMiddleware::class => TranslatorMiddlewareFactory::class,
            ],
        ];
    }
}
