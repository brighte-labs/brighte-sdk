<?php
namespace Brighte\Test\Infrastructure\Aws\Sqs;
use Brighte\Infrastructure\Aws\Sqs\SqsClient;
use Brighte\Infrastructure\Aws\Sqs\SqsClientFactory;
use Brighte\Infrastructure\Database\Doctrine\DoctrineConfig;
use PHPUnit\Framework\TestCase;


class SqsDoctrineConfigTest extends TestCase
{
    private $testDir;
    private $configContents;
    private $connectionName;

    /** @var $config DoctrineConfig*/
    private $doctrineConfig;

    public function setUp(): void
    {
        parent::setUp();
        $this->connectionName = 'doctrine.ms.crm';
        $this->testDir = dirname(dirname(__FILE__));
        $this->sqsConfigFilename = '/../../configs/doctrine.configs.json';
        $this->sqsConfigSettings = $this->testDir . $this->sqsConfigFilename;
        $this->configContents =  json_decode(file_get_contents($this->sqsConfigSettings), true);
        $this->configContents = $this->configContents[$this->connectionName];
        $this->doctrineConfig =  new DoctrineConfig($this->connectionName, $this->configContents);
    }

    public function testConfig()
    {
        $result = $this->doctrineConfig->getConnectionName();
        $this->doctrineConfig->setConnectionName($result);
        $this->assertSame($result, 'doctrine.ms.crm');

        $result = $this->doctrineConfig->getDriver();
        $this->doctrineConfig->setDriver($result);
        $this->assertSame($result, $this->configContents['driver']);

        $result = $this->doctrineConfig->getPassword();
        $this->doctrineConfig->setPassword($result);
        $this->assertSame($result, $this->configContents['password']);

        $result = $this->doctrineConfig->getConnection();
        $this->assertSame($result['driver'], $this->configContents['driver']);

        $result = $this->doctrineConfig->getHost();
        $this->doctrineConfig->setHost($result);
        $this->assertSame($result, $this->configContents['host']);

        $result = $this->doctrineConfig->getPort();
        $this->doctrineConfig->setPort($result);
        $this->assertSame($result, $this->configContents['port']);

        $result = $this->doctrineConfig->getDbname();
        $this->doctrineConfig->setDbname($result);
        $this->assertSame($result, $this->configContents['dbname']);

        $result = $this->doctrineConfig->getUser();
        $this->doctrineConfig->setUser($result);
        $this->assertSame($result, $this->configContents['user']);

        $result = $this->doctrineConfig->getCharset();
        $this->doctrineConfig->setCharset($result);
        $this->assertSame($result, $this->configContents['charset']);

        $result = $this->doctrineConfig->getCollation();
        $this->doctrineConfig->setCollation($result);
        $this->assertSame($result, $this->configContents['collation']);

        $result = $this->doctrineConfig->getPrefix();
        $this->doctrineConfig->setPrefix($result);
        $this->assertSame($result, $this->configContents['prefix']);

        $result = $this->doctrineConfig->isMemory();
        $this->doctrineConfig->setMemory($result);
        $this->assertSame($result, false);

        $result = $this->doctrineConfig->isDevMode();
        $this->doctrineConfig->setDevMode($result);
        $this->assertSame($result, false);

        $result = $this->doctrineConfig->isCache();
        $this->doctrineConfig->setCache($result);
        $this->assertSame($result, false);

        $result = $this->doctrineConfig->getCacheDir();
        $this->doctrineConfig->setCacheDir($result);
        $this->assertSame($result, $this->configContents['cache_dir']);

        $result = $this->doctrineConfig->getMetadataDirs();
        $this->doctrineConfig->setMetadataDirs($result);
        $this->assertSame($result, [$this->configContents['metadata_dirs']]);

        $result = $this->doctrineConfig->getIgnoredNamespaces();
        $this->doctrineConfig->setIgnoredNamespaces($result[0]);
        $this->assertSame($result, [$this->configContents['ignored_namespaces']]);
    }
}