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

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use function array_merge;
use function is_string;

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

        $config = $this->getTranslationConfig($config);

        $formatter = $container->has($config['formatter']) ? $container->get($config['formatter']) : new $config['formatter']();

        $cache = $debug ? null : $config['cache_dir'];
        $translator = new Translator($config['locale'], $formatter, $cache, $debug);

        $this->addLoaders($container, $translator, $config['loaders']);
        $this->addResources($translator, $config['resources']);

        $translator->setFallbackLocales($config['fallback']);
        $translator->setLocale($config['locale']);

        return $translator;
    }

    /**
     * Add resources to the given translator instance.
     *
     * @param Translator $translator
     * @param array      $resources
     */
    protected function addResources(Translator $translator, array $resources = []): void
    {
        foreach ($resources as $id => $resource) {
            if (!isset($resource['format'])) {
                throw new InvalidArgumentException("resource format is missing from resources configuration#{$id}.");
            }

            if (!isset($resource['resource'])) {
                throw new InvalidArgumentException("resource is missing from resources configuration#{$id}.");
            }

            $resource['locale'] = $resource['locale'] ?? $config['locale'] ?? 'en';
            $resource['domain'] = $resource['domain'] ?? 'messages';

            $translator->addResource($resource['format'], $resource['resource'], $resource['locale'], $resource['domain']);
        }
    }

    /**
     * Add loaders to the given translator instance.
     *
     * @param ContainerInterface $container
     * @param Translator         $translator
     * @param array              $loaders
     */
    protected function addLoaders(ContainerInterface $container, Translator $translator, array $loaders = []): void
    {
        foreach ($loaders as $format => $loader) {
            if (is_string($loader)) {
                $loader = $container->has($loader) ? $container->get($loader) : new $loader();
            }

            $translator->addLoader($format, $loader);
        }
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function getTranslationConfig(array $config): array
    {
        return array_merge([
            'locale' => 'en',
            'cache_dir' => null,
            'fallback' => [],
            'formatter' => MessageFormatter::class,
            'loaders' => [],
            'resources' => [],
        ], $config['translation'] ?? []);
    }
}
