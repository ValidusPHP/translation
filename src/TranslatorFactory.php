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

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return TranslatorInterface
     */
    public function __invoke(ContainerInterface $container): TranslatorInterface
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $debug = $config['debug'] ?? true;
        $config = $config['translation'] ?? [];

        if (isset($config['formatter']) && $container->has($config['formatter'])) {
            $formatter = $container->get($config['formatter']);
        } else {
            $formatter = new MessageFormatter();
        }

        $translator = new Translator(
            $config['locale'] ?? 'en',
            $formatter,
            $debug ? null : ($config['cache_dir'] ?? null),
            $debug
        );

        $loaders = $config['loaders'] ?? [];

        foreach ($loaders as $format => $loader) {
            if (\is_string($loader)) {
                $loader = $container->has($loader) ? $container->get($loader) : new $loader();
            }

            $translator->addLoader($format, $loader);
        }

        $resources = $config['resources'] ?? [];

        foreach ($resources as $id => $resource) {
            if (!isset($resource['format'])) {
                throw new InvalidArgumentException("resource format is missing from resources configuration#{$id}.");
            }

            if (!isset($resource['resource'])) {
                throw new InvalidArgumentException("resource is missing from resources configuration#{$id}.");
            }

            $resource['locale'] = $resource['locale'] ?? $config['locale'] ?? 'en';
            $resource['domain'] = $resource['domain'] ?? 'messages';

            $translator->addResource(
                $resource['format'],
                $resource['resource'],
                $resource['locale'],
                $resource['domain']
            );
        }

        $translator->setFallbackLocales($config['fallback'] ?? []);

        $translator->setLocale($config['locale'] ?? 'en');

        return $translator;
    }
}
