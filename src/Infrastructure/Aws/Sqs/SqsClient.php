<?php

namespace Brighte\Infrastructure\Aws\Sqs;

use Enqueue\Sqs\SqsConnectionFactory;
use Enqueue\Sqs\SqsConsumer;
use Enqueue\Sqs\SqsContext;
use Enqueue\Sqs\SqsDestination;
use Enqueue\Sqs\SqsMessage;

class SqsClient implements SqsClientInterface
{
    /** @var \Brighte\Infrastructure\Aws\Sqs\SqsConfig */
    protected $config;

    /** @var \Enqueue\Sqs\SqsConnectionFactory */
    protected $factory;

    /** @var \Enqueue\Sqs\SqsContext */
    protected $context;

    /** @var \Enqueue\Sqs\SqsDestination */
    protected $queue;

    /** @var \Enqueue\Sqs\SqsConsumer */
    protected $consumer;

    /**
     * SqsClient constructor.
     *
     * @param \Brighte\Infrastructure\Aws\Sqs\SqsConfig|null $config config
     */
    public function __construct(SqsConfig $config = null)
    {
        $this->config = $config;
        $this->factory = new SqsConnectionFactory($this->config->toArray());
        $this->context = $this->factory->createContext();
        $this->queue = $this->context->createQueue($this->config->getQueue());
        $this->queue->setFifoQueue($this->config ->isFifo());
        $this->consumer = $this->context->createConsumer($this->queue);
    }//end __construct()

    /**
     * @param string $body
     * @param string $groupId
     * @param array|null $properties
     * @return \Enqueue\Sqs\SqsMessage|null
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function publish(string $body, string $groupId, array $properties = [])
    {
        $message = $this->context->createMessage($body, $properties);
        $message->setMessageGroupId($groupId);

        $this->context->createProducer()->send($this->queue, $message);

        return $message;
    }//end publish()

    /**
     * @param int $timeOut millisecond
     * @return \Enqueue\Sqs\SqsMessage|null
     */
    public function receive(int $timeOut = 1000)
    {
        return $this->consumer->receive($timeOut);
    }//end receive()

    public function acknowledge(SqsMessage $message = null)
    {
        $this->consumer->acknowledge($message);
    }//end acknowledge()

    public function reject(SqsMessage $message = null, bool $requeue = false)
    {
        $this->consumer->reject($message, $requeue);
    }

    /**
     * @return \Brighte\Infrastructure\Aws\Sqs\SqsConfig
*/
    public function getConfig(): SqsConfig
    {
        return $this->config;
    }

    /**
     * @param \Brighte\Infrastructure\Aws\Sqs\SqsConfig $config
     * @return \Brighte\Infrastructure\Aws\Sqs\SqsClient
     */
    public function setConfig(SqsConfig $config): SqsClient
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return \Enqueue\Sqs\SqsConnectionFactory
     */
    public function getFactory(): SqsConnectionFactory
    {
        return $this->factory;
    }

    /**
     * @param \Enqueue\Sqs\SqsConnectionFactory $factory
     * @return \Brighte\Infrastructure\Aws\Sqs\SqsClient
     */
    public function setFactory(SqsConnectionFactory $factory): SqsClient
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * @return \Enqueue\Sqs\SqsContext
     */
    public function getContext(): SqsContext
    {
        return $this->context;
    }

    /**
     * @param \Enqueue\Sqs\SqsContext $context
     * @return \Brighte\Infrastructure\Aws\Sqs\SqsClient
     */
    public function setContext(SqsContext $context): SqsClient
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return \Enqueue\Sqs\SqsDestination
     */
    public function getQueue(): SqsDestination
    {
        return $this->queue;
    }

    /**
     * @param \Enqueue\Sqs\SqsDestination $queue
     * @return \Brighte\Infrastructure\Aws\Sqs\SqsClient
     */
    public function setQueue(SqsDestination $queue): SqsClient
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return \Enqueue\Sqs\SqsConsumer
     */
    public function getConsumer(): SqsConsumer
    {
        return $this->consumer;
    }

    /**
     * @param \Enqueue\Sqs\SqsConsumer $consumer
     * @return \Brighte\Infrastructure\Aws\Sqs\SqsClient
     */
    public function setConsumer(SqsConsumer $consumer): SqsClient
    {
        $this->consumer = $consumer;

        return $this;
    }//end reject()

}//end class
