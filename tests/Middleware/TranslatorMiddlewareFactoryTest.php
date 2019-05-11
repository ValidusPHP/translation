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

namespace Validus\Tests\Translation\Middleware;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use ReflectionObject;
use Symfony\Component\Translation\Translator;
use Validus\Translation\Middleware\TranslatorMiddlewareFactory;

class TranslatorMiddlewareFactoryTest extends TestCase
{
    /** @var ObjectProphecy|ContainerInterface $container */
    protected $container;

    /** @var ObjectProphecy|Translator $translator */
    protected $translator;

    public function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->translator = $this->prophesize(Translator::class);
    }

    public function testInvokeWithContainerEmptyConfig(): void
    {
        $this->container->has('config')
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->container->get(Translator::class)
            ->shouldBeCalledOnce()
            ->willReturn($this->translator->reveal());

        $this->translator->getLocale()
            ->shouldBeCalledOnce()
            ->willReturn('fr');

        $factory = new TranslatorMiddlewareFactory();
        $middleware = $factory($this->container->reveal());
        static::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    /**
     * @dataProvider provideConfig
     *
     * @param array $config
     */
    public function testInvoke(array $config): void
    {
        $this->container->has('config')
            ->shouldBeCalledOnce()
            ->willReturn(true);

        $this->container->get('config')
            ->shouldBeCalledOnce()
            ->willReturn($config);

        $translator = $this->translator->reveal();

        $this->container->get(Translator::class)
            ->shouldBeCalledOnce()
            ->willReturn($translator);

        if (!isset($config['translator']['priorities']) && !isset($config['translator']['fallback'])) {
            $this->translator->getLocale()
                ->shouldBeCalledOnce()
                ->willReturn('en');
            $priorities = ['en'];
        } elseif (isset($config['translator']['priorities'])) {
            $priorities = $config['translator']['priorities'];
        } else {
            $priorities = $config['translator']['fallback'];
        }

        $factory = new TranslatorMiddlewareFactory();
        $middleware = $factory($this->container->reveal());
        static::assertInstanceOf(MiddlewareInterface::class, $middleware);

        $reflection = new ReflectionObject($middleware);

        $rf = $reflection->getProperty('translator');
        $rf->setAccessible(true);
        static::assertSame(
            $translator,
            $rf->getValue($middleware)
        );

        $rf = $reflection->getProperty('priorities');
        $rf->setAccessible(true);
        static::assertSame(
            $priorities,
            $rf->getValue($middleware)
        );
    }

    public function provideConfig(): array
    {
        return [
            [[]],
            [['translator' => []]],
            [['translator' => ['fallback' => ['en', 'fr']]]],
            [['translator' => ['priorities' => ['fr', 'en']]]],
            [['translator' => ['fallback' => ['en'], 'priorities' => ['en']]]],
        ];
    }
}
