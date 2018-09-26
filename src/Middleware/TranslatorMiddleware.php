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

use Negotiation\AcceptLanguage;
use Negotiation\LanguageNegotiator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorMiddleware implements MiddlewareInterface
{
    public const TRANSLATOR_ATTRIBUTE = 'translator';

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var LanguageNegotiator $negotiator */
    protected $negotiator;

    /** @var array $priorities */
    protected $priorities;

    /**
     * LocalizationMiddleware constructor.
     *
     * @param TranslatorInterface $translator
     * @param array|null          $priorities
     */
    public function __construct(TranslatorInterface $translator, array $priorities = null)
    {
        $this->translator = $translator;
        $this->priorities = $priorities ?? [$translator->getLocale()];
        $this->negotiator = new LanguageNegotiator();
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $local = $this->getLocaleFromRequest($request);

        if (null !== $local) {
            $this->translator->setLocale($local);
        }

        return $handler->handle(
            $request->withAttribute(static::TRANSLATOR_ATTRIBUTE, $this->translator)
        );
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return null|string
     */
    protected function getLocaleFromRequest(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Accept-Language');

        if ('' === $header) {
            return null;
        }

        /** @var AcceptLanguage $language */
        $language = $this->negotiator->getBest($header, $this->priorities);

        return null === $language ? null : $language->getValue();
    }
}
