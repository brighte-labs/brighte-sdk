<?php
namespace Brighte\Test\Infrastructure\Aws\Sns;

use Brighte\Infrastructure\Aws\Sns\SnsClientFactory;
use PHPUnit\Framework\TestCase;


class SnsConfigTest extends TestCase
{
    private $testDir;
    private $connectionName;

    /** @var $snsClientFactory SnsClientFactory*/
    private $snsClientFactory;

    /** @var $snsClient SnsClient*/
    private $snsClient;

    /** @var $config \Brighte\Infrastructure\Aws\Sns\SnsConfig*/
    private $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->connectionName = 'sns.ms.crm';
        $this->testDir = dirname(dirname(__FILE__));
        $this->snsConfigFilename = '/../../configs/sns.configs.json';
        $this->snsConfigSettings = $this->testDir . $this->snsConfigFilename;
        $this->configContents =  json_decode(file_get_contents($this->snsConfigSettings), true);
        $this->configContents = $this->configContents[$this->connectionName];
        $this->snsClientFactory = new SnsClientFactory($this->snsConfigSettings);
        $this->snsClient = $this->snsClientFactory->create($this->connectionName);
        $this->config = $this->snsClient->getConfig();
    }

    public function testConfig()
    {
        $result = $this->config->getRegion();
        $this->config->setRegion($result);
        $this->assertSame($this->config->getRegion(), $this->configContents['region']);

        $result = $this->config->getKey();
        $this->config->setKey($result);
        $this->assertSame($this->config->getKey(), $this->configContents['key']);

        $result = $this->config->getSecret();
        $this->config->setSecret($result);
        $this->assertSame($this->config->getSecret(), $this->configContents['secret']);

        $result = $this->config->getConnectionName();
        $this->config->setConnectionName($result);
        $this->assertSame($this->config->getConnectionName(), $this->connectionName);
    }
}