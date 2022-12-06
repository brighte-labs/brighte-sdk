<?php
namespace Brighte\Test\Infrastructure\Aws\Sqs;
use Brighte\Infrastructure\Aws\Sqs\SqsClient;
use Brighte\Infrastructure\Aws\Sqs\SqsClientFactory;
use PHPUnit\Framework\TestCase;


class SqsConfigTest extends TestCase
{
    private $testDir;
    private $sqsConfig;
    private $connectionName;

    /** @var $sqsClientFactory SqsClientFactory*/
    private $sqsClientFactory;

    /** @var $sqsClient SqsClient*/
    private $sqsClient;

    /** @var $config \Brighte\Infrastructure\Aws\Sqs\SqsConfig*/
    private $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->connectionName = 'sqs.ms.crm';
        $this->testDir = dirname(dirname(__FILE__));
        $this->sqsConfigFilename = '/../../configs/sqs.configs.json';
        $this->sqsConfigSettings = $this->testDir . $this->sqsConfigFilename;
        $this->configContents =  json_decode(file_get_contents($this->sqsConfigSettings), true);
        $this->configContents = $this->configContents[$this->connectionName];
        $this->sqsClientFactory = new SqsClientFactory($this->sqsConfigSettings);
        $this->sqsClient = $this->sqsClientFactory->create($this->connectionName);
        $this->config = $this->sqsClient->getConfig();
    }

    public function testConfig()
    {
        $result = $this->config->getQueue();
        $this->config->setQueue($result);
        $this->assertSame($this->config->getQueue(), $this->configContents['queue']);

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

        $result = $this->config->setFifo(false);
        $this  ->assertFalse($result->isFifo());
    }
}