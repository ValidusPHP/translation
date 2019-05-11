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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Translation\Translator;
use Validus\Translation\Middleware\TranslatorMiddleware;

class TranslatorMiddlewareTest extends TestCase
{
    /** @var ServerRequestInterface|ObjectProphecy $request */
    private $request;

    /** @var RequestHandlerInterface|ObjectProphecy $handler */
    private $handler;

    /** @var Translator|ObjectProphecy $translator */
    private $translator;

    /** @var ContainerInterface|ObjectProphecy $container */
    private $container;

    public function setUp(): void
    {
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
        $this->translator = $this->prophesize(Translator::class);
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    public function testConstructor(): void
    {
        $middleware = new TranslatorMiddleware(
            $this->translator->reveal(),
            []
        );
        static::assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    public function testGetsLocaleFromRequest(): void
    {
        $this->request->getHeaderLine('Accept-Language')
            ->shouldBeCalledOnce()
            ->willReturn(
                'en; q=0.1, fr; q=0.4, fu; q=0.9, de; q=0.2'
            );

        $this->translator->setLocale('fu')
            ->shouldBeCalledOnce()
            ->willReturn(null);
        $translator = $this->translator->reveal();

        $request = $this->request->reveal();

        $this->handler->handle($request)
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(ResponseInterface::class)->reveal()
            );

        $handler = $this->handler->reveal();

        $middleware = new TranslatorMiddleware($translator, ['en', 'fu']);
        $response = $middleware->process($request, $handler);

        static::assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testDefaultPrioritiesBasedOnTheTranslatorLocale(): void
    {
        $this->request->getHeaderLine('Accept-Language')
            ->shouldBeCalledOnce()
            ->willReturn(
                'en; q=0.1, fr; q=0.4, fu; q=0.9, de; q=0.2'
            );

        $this->translator->setLocale('fu')
            ->shouldBeCalledOnce()
            ->willReturn(null);
        $this->translator->getLocale()
            ->shouldBeCalledOnce()
            ->willReturn('fu');
        $translator = $this->translator->reveal();

        $request = $this->request->reveal();

        $this->handler->handle($request)
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(ResponseInterface::class)->reveal()
            );

        $handler = $this->handler->reveal();

        $middleware = new TranslatorMiddleware($translator);
        $response = $middleware->process($request, $handler);

        static::assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testNoAcceptLanguageHeaderLine(): void
    {
        $this->request->getHeaderLine('Accept-Language')
            ->shouldBeCalledOnce()
            ->willReturn(
                ''
            );

        $this->translator->getLocale()
            ->shouldBeCalledOnce()
            ->willReturn('fu');
        $translator = $this->translator->reveal();

        $request = $this->request->reveal();

        $this->handler->handle($request)
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(ResponseInterface::class)->reveal()
            );

        $handler = $this->handler->reveal();

        $middleware = new TranslatorMiddleware($translator);
        $response = $middleware->process($request, $handler);

        static::assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testCantGetBestLocaleFromRequest(): void
    {
        $this->request->getHeaderLine('Accept-Language')
            ->shouldBeCalledOnce()
            ->willReturn(
                'en, de'
            );

        $translator = $this->translator->reveal();

        $request = $this->request->reveal();

        $this->handler->handle($request)
            ->shouldBeCalledOnce()
            ->willReturn(
                $this->prophesize(ResponseInterface::class)->reveal()
            );

        $handler = $this->handler->reveal();

        $middleware = new TranslatorMiddleware($translator, ['fr']);
        $response = $middleware->process($request, $handler);

        static::assertInstanceOf(ResponseInterface::class, $response);
    }
}
