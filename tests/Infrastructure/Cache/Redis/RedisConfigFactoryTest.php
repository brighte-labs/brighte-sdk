<?php

namespace Brighte\Test\Infrastructure\Cache\Redis;

use Brighte\Infrastructure\Cache\Redis\RedisConfigFactory;
use Brighte\Infrastructure\Cache\Redis\RedisConfig;
use Brighte\Infrastructure\Cache\Redis\Exceptions\RedisConfigException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RedisConfigFactoryTest extends TestCase
{
    /** @var String */
    protected $redisConfigSettings;

    /** @var RedisConfigFactory */
    protected $redisConfigFactory;

    /** @var \stdClass */
    protected $configContents;

    public function setUp(): void
    {
        parent::setUp();
        $testDir = dirname(dirname(__FILE__));
        $redisConfigFilename = '/../../configs/redis.configs.json';
        $this->redisConfigSettings = $testDir . $redisConfigFilename;

        $this->configContents = json_decode(file_get_contents($this->redisConfigSettings), true);
        $this->configContents = $this->configContents['redis.ms.crm'];
        $this->redisConfigFactory = $this->getMockBuilder(RedisConfigFactory::class)
            ->setConstructorArgs([$this->redisConfigSettings])
            ->getMock();

        putenv("REDIS_SCHEME=REDIS_SCHEME");
        putenv("REDIS_HOST=REDIS_HOST");
        putenv("REDIS_PORT=REDIS_PORT");
    }

    public function tearDown(): void
    {
        putenv("REDIS_SCHEME");
        putenv("REDIS_HOST");
        putenv("REDIS_PORT");
        unset($this->redisConfigFactory);
        parent::tearDown();
    }

    public function testLoadConfigs()
    {
        $connection = 'redis.ms.crm';
        $class = new ReflectionClass($this->redisConfigFactory);
        $method = $class->getMethod('loadConfigs');
        $method->setAccessible(true);

        $method->invoke($this->redisConfigFactory);

        $property = $class->getProperty('configs');
        $property->setAccessible(true);

        $this->assertSame(
            (array) $property->getValue($this->redisConfigFactory)->$connection,
            $this->configContents
        );
    }

    public function testGet()
    {
        $class = new RedisConfigFactory($this->redisConfigSettings);

        try {
            $class->get(null);
        } catch (RedisConfigException $exception) {
            $this->assertStringContainsString('Unknown connection name', $exception->getMessage());
        }

        $testRedisConfig = $class->get('redis.ms.crm');
        $configArr = [
            'scheme' => 'REDIS_SCHEME',
            'host' => 'REDIS_HOST',
            'port' => 'REDIS_PORT'
        ];
        $newRedisConfig = new RedisConfig($configArr);

        $this->assertEquals($newRedisConfig->toArray(), $testRedisConfig->toArray());
    }
}
