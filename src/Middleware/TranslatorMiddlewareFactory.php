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

namespace Polar\Translation\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): TranslatorMiddleware
    {
        /** @var TranslatorInterface $translator */
        $translator = $container->get(TranslatorInterface::class);
        $config = $container->has('config') ? $container->get('config') : [];

        $config = $config['translator'] ?? [];

        $priorities = $config['priorities'] ?? $config['fallbacks'] ?? [$translator->getLocale()];

        return new TranslatorMiddleware($translator, $priorities);
    }
}
