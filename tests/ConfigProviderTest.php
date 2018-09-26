<?php

declare(strict_types=1);

namespace Validus\Tests\Translation;

use PHPStan\Testing\TestCase;
use Validus\Translation\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    /** @var ConfigProvider */
    private $provider;

    public function setUp(): void
    {
        $this->provider = new ConfigProvider();
    }

    public function testInvocationReturnsArray(): array
    {
        $config = ($this->provider)();

        static::assertInternalType('array', $config);

        return $config;
    }

    /**
     * @depends testInvocationReturnsArray
     *
     * @param array $config
     */
    public function testReturnedArrayContainsDependencies(array $config): void
    {
        static::assertArrayHasKey('dependencies', $config);
        static::assertInternalType('array', $config['dependencies']);
    }
}
