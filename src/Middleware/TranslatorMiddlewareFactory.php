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

namespace Validus\Translation\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Translator;

class TranslatorMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): TranslatorMiddleware
    {
        /** @var Translator $translator */
        $translator = $container->get(Translator::class);
        $config = $container->has('config') ? $container->get('config') : [];

        $config = $config['translator'] ?? [];

        $priorities = $config['priorities'] ?? $config['fallback'] ?? [$translator->getLocale()];

        return new TranslatorMiddleware($translator, $priorities);
    }
}
