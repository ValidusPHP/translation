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

namespace Validus\Tests\Translation;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Validus\Translation\TranslatorFactory;

class TranslatorFactoryTest extends TestCase
{
    /** @var ContainerInterface|ObjectProphecy $container */
    protected $container;

    public function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function testDefaultTranslator(): void
    {
        $this->container->has('config')
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->container->has(MessageFormatter::class)
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $factory = new TranslatorFactory();
        /** @var Translator $translator */
        $translator = $factory($this->container->reveal());

        static::assertInstanceOf(Translator::class, $translator);
        static::assertSame('en', $translator->getLocale());
        static::assertSame([], $translator->getFallbackLocales());
        static::assertCount(0, (array) $translator->getCatalogue('en')->getResources());
    }

    public function testFullyConfiguredTranslator(): void
    {
        $this->container->has('config')
            ->shouldBeCalledOnce()
            ->willReturn(true);
        $this->container->get('config')
            ->shouldBeCalledOnce()
            ->willReturn(
                $config = include __DIR__ . '/../examples/full-config.php'
            );

        foreach ($config['translation']['loaders'] as $loader) {
            $this->container->has($loader)
                ->shouldBeCalledOnce()
                ->willReturn(false);
        }

        $this->container->has($config['translation']['formatter'])
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $factory = new TranslatorFactory();
        /** @var Translator $translator */
        $translator = $factory($this->container->reveal());

        static::assertInstanceOf(Translator::class, $translator);
        static::assertSame('en', $translator->getLocale());
        static::assertSame(['en', 'fr', 'pl'], $translator->getFallbackLocales());

        static::assertSame('Hello Saif', $translator->trans('Hello %name%', ['%name%' => 'Saif']));
        $translator->setLocale('es');
        static::assertSame('Hola Saif', $translator->trans('Hello %name%', ['%name%' => 'Saif']));
        $translator->setLocale('pl');
        static::assertSame('Cześć Saif', $translator->trans('Hello %name%', ['%name%' => 'Saif']));
        $translator->setLocale('fr');
        static::assertSame('Bonjour Saif', $translator->trans('Hello %name%', ['%name%' => 'Saif']));
        $translator->setLocale('ar');
        static::assertSame('مرحبا Saif', $translator->trans('Hello %name%', ['%name%' => 'Saif']));
    }

    public function testSimpleTranslator(): void
    {
        $this->container->has('config')
            ->shouldBeCalledOnce()
            ->willReturn(true);
        $this->container->get('config')
            ->shouldBeCalledOnce()
            ->willReturn(
                $config = include __DIR__ . '/../examples/simple-config.php'
            );

        foreach ($config['translation']['loaders'] as $loader) {
            $this->container->has($loader)
                ->shouldBeCalledOnce()
                ->willReturn(false);
        }

        $this->container->has(MessageFormatter::class)
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $factory = new TranslatorFactory();
        /** @var Translator $translator */
        $translator = $factory($this->container->reveal());

        static::assertInstanceOf(Translator::class, $translator);
        static::assertSame('en', $translator->getLocale());
        static::assertSame(['en', 'fr'], $translator->getFallbackLocales());

        $translator->setLocale('en');
        static::assertSame('Hello Saif', $translator->trans('Hello %name%', ['%name%' => 'Saif']));
        $translator->setLocale('fr');
        static::assertSame('Bonjour Saif', $translator->trans('Hello %name%', ['%name%' => 'Saif']));

        static::assertEquals('The email address is invalid.', $translator->trans('email', [], 'validations', 'en'));
        static::assertEquals('L\'adresse mail est invalide.', $translator->trans('email', [], 'validations', 'fr'));
    }

    public function testResourceFormatIsMissing(): void
    {
        $config = [
            'translation' => [
                'loaders' => [
                    YamlFileLoader::class,
                ],
                'resources' => [
                    [
                        'resource' => 'path/to/messages.en.yaml',
                        'domain' => 'messages',
                        'locale' => 'en',
                    ],
                ],
            ],
        ];

        $this->container->has('config')
            ->shouldBeCalledOnce()
            ->willReturn(true);
        $this->container->get('config')
            ->shouldBeCalledOnce()
            ->willReturn($config);
        $this->container->has(MessageFormatter::class)
            ->shouldBeCalledOnce()
            ->willReturn(false);
        $this->container->has(YamlFileLoader::class)
            ->shouldBeCalledOnce()
            ->willReturn(false);
        $factory = new TranslatorFactory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('resource format is missing from resources configuration#0.');

        $factory($this->container->reveal());
    }

    public function testResourceIsMissing(): void
    {
        $config = [
            'translation' => [
                'loaders' => [
                    YamlFileLoader::class,
                ],
                'resources' => [
                    [
                        'format' => 'yaml',
                        'domain' => 'messages',
                        'locale' => 'en',
                    ],
                ],
            ],
        ];
        $this->container->has('config')
            ->shouldBeCalledOnce()
            ->willReturn(true);
        $this->container->get('config')
            ->shouldBeCalledOnce()
            ->willReturn($config);
        $this->container->has(MessageFormatter::class)
            ->shouldBeCalledOnce()
            ->willReturn(false);
        $this->container->has(YamlFileLoader::class)
            ->shouldBeCalledOnce()
            ->willReturn(false);
        $factory = new TranslatorFactory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('resource is missing from resources configuration#0.');

        $factory($this->container->reveal());
    }
}
