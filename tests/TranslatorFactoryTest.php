<?php
/*──────────────────────────────────────────────────────────────────────────────
 ─ @author Saif Eddin Gmati <azjezz@azjezz.me>
 ─
 ─ Unauthorized copying of this file, via any medium is strictly prohibited
 ─ Proprietary and confidential
 ─ Written by Saif Eddin Gmati.
 ─
 ─ Validus Copyright (c) 2018.
 ─────────────────────────────────────────────────────────────────────────────*/

declare(strict_types=1);

namespace Validus\Tests\Translation;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
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
}
