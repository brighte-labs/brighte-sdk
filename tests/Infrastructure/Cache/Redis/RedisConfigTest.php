<?php

namespace Brighte\Test\Infrastructure\Cache\Redis;

use Brighte\Infrastructure\Cache\Redis\RedisConfig;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RedisConfigTest extends TestCase
{
    /** @var RedisConfig */
    protected $redisConfig;

    /** @var Array */
    protected $configArr;

    public function setUp(): void
    {
        parent::setUp();
        $this->configArr = [
            'scheme' => 'REDIS_SCHEME',
            'host' => 'REDIS_HOST',
            'port' => 'REDIS_PORT'
        ];
        $this->redisConfig = new RedisConfig($this->configArr);
    }

    public function tearDown(): void
    {
        unset($this->redisConfig);
        parent::tearDown();
    }

    /**
     * Get access to private or protected properties
     *
     * @param object $class
     * @param string $prop
     * @return string
     */
    protected function getProtectedProperty($class, string $prop): string
    {
        $refClass = new ReflectionClass($class);
        $property = $refClass->getProperty($prop);
        $property->setAccessible(true);

        return $property->getValue($class);
    }

    public function testConstructor()
    {
        $scheme = $this->getProtectedProperty($this->redisConfig, 'scheme');
        $host = $this->getProtectedProperty($this->redisConfig, 'host');
        $port = $this->getProtectedProperty($this->redisConfig, 'port');

        $this->assertEquals($scheme, $this->configArr['scheme']);
        $this->assertEquals($host, $this->configArr['host']);
        $this->assertEquals($port, $this->configArr['port']);
    }

    public function testToArray()
    {
        $this->assertEquals($this->redisConfig->toArray(), $this->configArr);
    }

    public function testGetterSetters()
    {
        $this->redisConfig->setScheme('new_scheme');
        $this->assertEquals($this->redisConfig->getScheme(), 'new_scheme');

        $this->redisConfig->setHost('new_host');
        $this->assertEquals($this->redisConfig->getHost(), 'new_host');

        $this->redisConfig->setPort(9999);
        $this->assertEquals($this->redisConfig->getPort(), 9999);
    }
}
