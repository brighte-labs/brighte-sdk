<?php
namespace Brighte\Test\Infrastructure\Cache\Redis;

use Brighte\Infrastructure\Cache\Redis\RedisClientFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RedisClientFactoryTest extends TestCase
{
    private $configContents;
    private $redisConfigFilename;
    private $redisConfigSettings;

    /** @var $redis RedisClientFactory*/
    private $redis;

    public function setUp(): void
    {
        parent::setUp();
        $testDir = dirname(dirname(__FILE__));
        $this->redisConfigFilename = '/../../configs/redis.configs.json';
        $this->redisConfigSettings = $testDir . $this->redisConfigFilename;
        $this->configContents =  json_decode(file_get_contents($this->redisConfigSettings), true);
        $this->configContents = $this->configContents['redis.ms.crm'];
        $this->redis = new RedisClientFactory($this->redisConfigSettings);

        putenv("REDIS_SCHEME=REDIS_SCHEME");
        putenv("REDIS_HOST=REDIS_HOST");
        putenv("REDIS_PORT=REDIS_PORT");
    }

    public function tearDown(): void
    {
        putenv("REDIS_SCHEME");
        putenv("REDIS_HOST");
        putenv("REDIS_PORT");
        parent::tearDown();
    }

    public function testConfig()
    {
        $class = new ReflectionClass($this->redis);
        $property = $class->getProperty('configFactory');
        $property->setAccessible(true);

        $this->assertSame(
            $property->getValue($this->redis)->get('redis.ms.crm')->toArray(),
            $this->configContents
        );
    }
}