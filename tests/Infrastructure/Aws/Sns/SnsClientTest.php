<?php
namespace Brighte\Test\Infrastructure\Aws\Sns;

use Brighte\Infrastructure\Aws\Sns\SnsClientFactory;
use Brighte\Infrastructure\Aws\Sns\SnsConfig;
use Enqueue\Sns\SnsProducer;
use Enqueue\Sns\SnsConnectionFactory;
use Enqueue\Sns\SnsContext;
use PHPUnit\Framework\TestCase;

class SnsClientTest extends TestCase
{
    private $testDir;
    private $snsConfig;

    /** @var $snsClientFactory SnsClientFactory*/
    private $snsClientFactory;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $snsProducerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $snsContextMock;
    /**
     * @var \Brighte\Infrastructure\Aws\Sns\SnsClientInterface
     */
    private $snsClient;

    public function setUp()
    {
        parent::setUp();

        $this->sampleMessage = 'Sample Message';
        $this->testDir = dirname(dirname(__FILE__));
        $this->snsConfig = '/../../configs/sns.configs.json';
        $this->snsConfigSettings = $this->testDir . $this->snsConfig;
        $this->putEnvVariables();

        $this->snsProducerMock = $this->createMock(SnsProducer::class);
        $this->snsProducerMock->method('send')->willReturn('MessageId');
        $this->snsClientFactory = new SnsClientFactory($this->snsConfigSettings);
        $this->snsClient = $this->snsClientFactory->create('sns.ms.crm');

        $this->snsClientMock = $this->createMock(get_class($this->snsClient));
    }

    public function testGetterSetter()
    {
        $config = [
            'key' => 'CRM_AWS_KEY',
            'secret' => 'CRM_AWS_SECRET',
            'region' => 'CRM_AWS_REGION',
        ];
        $configMock = new SnsConfig('sns.ms.crm', $config);
        $snsConnectionFactory = $this->createMock(SnsConnectionFactory::class);
        $this->snsClient->setFactory($snsConnectionFactory);
        $this->snsClient->setConfig($configMock);

        $this->assertEquals($config, $this->snsClient->getConfig()->toArray());
        $this->assertEquals($config['key'], $this->snsClient->getConfig()->getKey());
        $this->assertEquals($config['secret'], $this->snsClient->getConfig()->getSecret());
        $this->assertEquals($config['region'], $this->snsClient->getConfig()->getRegion());
    }

    public function testConfig()
    {
        $snsClientConfig = $this->snsClient->getConfig()->toArray();
        $configContents = json_decode(file_get_contents($this->snsConfigSettings), true);
        $configContents = $configContents['sns.ms.crm'];

        foreach ($configContents as $key => $confSetting) {
            $this->assertSame((string)$confSetting, (string)$snsClientConfig[$key]);
        }
    }

    public function testSend()
    {
        $topic = 'topic';
        $body = 'Sample body';
        $properties = ['property' => 1];
        $snsClientConfig = $this->snsClient->getConfig()->toArray();
        $this->snsContextMock = $this->getMockBuilder(SnsContext::class)
            ->setConstructorArgs([$this->snsClient->getContext()->getSnsClient(), $snsClientConfig])
            ->setMethods(['declareTopic', 'createProducer'])
            ->getMock();
        $this->snsContextMock->expects($this->exactly(1))->method('declareTopic');
        $this->snsContextMock->expects($this->exactly(1))->method('createProducer')->willReturn($this->snsProducerMock);
        $this->snsClient->setContext($this->snsContextMock);
        $this->snsClient->send($topic, $body, $properties);
    }

    public function testSendException()
    {
        $topic = 'topic';
        $body = 'Sample body';
        $properties = ['property' => 1];
        try {
            $this->snsClient->send($topic, $body, $properties);
        } catch (\Exception $exception) {
            $this->assertContains('Error executing "CreateTopic"', $exception->getMessage());
        }
    }

    public function putEnvVariables()
    {
        $this->configContents =  json_decode(file_get_contents($this->snsConfigSettings));

        foreach ($this->configContents as $key => $para) {
            foreach ($para as $key => $value) {
                putenv((string)$value . '=' . $value);
            }
        }
    }
}
