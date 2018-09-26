<?php

declare(strict_types=1);

namespace Polar\Test\Translation;

use PHPStan\Testing\TestCase;
use Polar\Translation\ConfigProvider;

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
