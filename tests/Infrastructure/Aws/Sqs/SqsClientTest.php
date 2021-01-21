<?php
namespace Brighte\Test\Infrastructure\Aws\Sqs;
use Brighte\Infrastructure\Aws\Sqs\SqsClient;
use Brighte\Infrastructure\Aws\Sqs\SqsClientFactory;
use Enqueue\Sqs\SqsProducer;
use Enqueue\Sqs\SqsConsumer;
use Enqueue\Sqs\SqsMessage;
use PHPUnit\Framework\TestCase;

class SqsClientTest extends TestCase
{
    private $testDir;
    private $sqsConfig;

    /** @var $sqsClientFactory SqsClientFactory*/
    private $sqsClientFactory;

    /** @var $sqsClient SqsClient*/
    private $sqsClient;

    private $sampleMessage;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $sqsClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $sqsProducerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $sqsConsumerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $sqsMessageMock;

    public function setUp()
    {
        parent::setUp();

        $this->sampleMessage = 'Sample Message';
        $this->testDir = dirname(dirname(__FILE__));
        $this->sqsConfig = '/../../configs/sqs.configs.json';
        $this->sqsConfigSettings = $this->testDir . $this->sqsConfig;
        $this->putEnvVariables();
        $this->sqsClientFactory = new SqsClientFactory($this->sqsConfigSettings);
        $this->sqsClient = $this->sqsClientFactory->create('sqs.ms.crm');
        $this->sqsClientMock = $this->createMock(get_class($this->sqsClient));
        $this->sqsClientMock->method('publish')->willReturn($this->sampleMessage);
        $this->sqsProducerMock = $this->createMock(SqsProducer::class);
        $this->sqsProducerMock->method('send')->willReturn('MessageId');
        $this->sqsConsumerMock = $this->createMock(SqsConsumer::class);
        $this->sqsMessageMock = $this->createMock(SqsMessage::class);
        $context = $this->sqsClient->getContext();
        //$context->setSqsProducer($this->sqsProducerMock);
        $this->sqsClient->setContext($context);
        $this->sqsClient->setConsumer($this->sqsConsumerMock);
        $config = $this->sqsClient->getConfig();
        $this->sqsClient->setConfig($config);
        $factory = $this->sqsClient->getFactory();
        $this->sqsClient->setFactory($factory);
        $queue = $this->sqsClient->getQueue();
        $this->sqsClient->setQueue($queue);
        $this->assertSame($this->sqsConsumerMock,  $this->sqsClient->getConsumer());
    }

    public function testConfig()
    {
        $sqlClientConfig = $this->sqsClient->getConfig()->toArray();
        $configContents = json_decode(file_get_contents($this->sqsConfigSettings), true);
        $configContents = $configContents['sqs.ms.crm'];

        foreach ($configContents as $key => $confSetting) {
            // if the configuration is set 'fifo' to true if its set.
            if ($confSetting === 'CRM_AWS_QUEUE_FIFO') {
                $confSetting = 1;
                $sqlClientConfig[$key] = 1;
            }
            $this->assertSame((string)$confSetting, (string)$sqlClientConfig[$key]);
        }
    }

    public function testPublish()
    {
        $body = 'Sample body';
        $groupId = 1;
        $properties = ['property' => 1];
        try {
            // $this->sqsClient->publish($body, $groupId, $properties);
        } catch (\Exception $exception) {
            $this->assertContains('Error executing "GetQueueUrl"', $exception->getMessage());
        }
    //
    // $context = $this->sqsClient->getContext();
    // $context->setSqsProducer($this->sqsProducerMock);
    // $this->sqsClient->setContext($context);
    // $result = $this->sqsClient->publish($body, $groupId, $properties);
    // $this->assertSame($result->getBody(), $body);
    }

    public function testRecieve()
    {
        $result = $this->sqsClient->receive(1000);
        $this->assertNull($result);
    }

    public function testAcknowledge()
    {
        $result = $this->sqsClient->acknowledge($this->sqsMessageMock);
        $this->assertNull($result);
    }

    public function testReject()
    {
        $result = $this->sqsClient->reject($this->sqsMessageMock);
        $this->assertNull($result);
    }

    public function putEnvVariables()
    {
        $this->configContents =  json_decode(file_get_contents($this->sqsConfigSettings));

        foreach ($this->configContents as $key => $para) {
            foreach ($para as $key => $value) {
                putenv((string)$value . '=' . $value);
            }
        }
    }
}
