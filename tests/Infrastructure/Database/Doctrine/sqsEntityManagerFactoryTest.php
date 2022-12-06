<?php
namespace Brighte\Test\Infrastructure\Aws\Sqs;
use Brighte\Infrastructure\Database\Doctrine\EntityManagerFactory;
use PHPUnit\Framework\TestCase;


class SqsEntityManagerTest extends TestCase
{
    private $testDir;
    private $configContents;
    private $connectionName;
    private $sqsConfigFilename;
    private $sqsConfigSettings;

    /** @var $config EntityManagerFactory*/
    private $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->connectionName = 'doctrine.ms.crm';
        $this->testDir = dirname(dirname(__FILE__));
        $this->sqsConfigFilename = '/../../configs/doctrine.configs.json';
        $this->sqsConfigSettings = $this->testDir . $this->sqsConfigFilename;
        $this->configContents =  json_decode(file_get_contents($this->sqsConfigSettings), true);
        $this->configContents = $this->configContents[$this->connectionName];
        $this->config =  new EntityManagerFactory($this->sqsConfigSettings);
    }

    public function testConfig()
    {
        $this->assertSame($this->config->getConfigFile(), $this->sqsConfigSettings);
    }
}