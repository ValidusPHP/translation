<?php

declare(strict_types=1);

namespace Validus\Tests\Translation\Middleware;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Validus\Translation\Middleware\TranslatorMiddlewareFactory;

class TranslatorMiddlewareFactoryTest extends TestCase
{
    /** @var ContainerInterface|ObjectProphecy $container */
    protected $container;

    /** @var TranslatorInterface|ObjectProphecy $translator */
    protected $translator;

    public function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->translator = $this->prophesize(TranslatorInterface::class);
    }

    public function testInvokeWithContainerEmptyConfig(): void
    {
        $this->container->has('config')
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->container
            ->get(TranslatorInterface::class)
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->translator->reveal()
            );

        $this->translator->getLocale()
            ->shouldBeCalledOnce()
            ->willReturn('fr');

        $factory = new TranslatorMiddlewareFactory();
        $middleware = $factory($this->container->reveal());
        static::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }
}
