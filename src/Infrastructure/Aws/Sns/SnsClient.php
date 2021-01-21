<?php

namespace Brighte\Infrastructure\Aws\Sns;

use Enqueue\Sns\SnsConnectionFactory;
use Enqueue\Sns\SnsContext;

class SnsClient implements SnsClientInterface
{

    /** @var \Brighte\Infrastructure\Aws\Sns\SnsConfig */
    protected $config;

    /** @var \Enqueue\Sns\SnsConnectionFactory */
    protected $factory;

    /** @var \Enqueue\Sns\SnsContext */
    protected $context;

    /**
     * SqsClient constructor.
     *
     * @param \Brighte\Infrastructure\Aws\Sns\SnsConfig|null $config config
     */
    public function __construct(SnsConfig $config = null)
    {
        $this->config = $config;
        $this->factory = new SnsConnectionFactory($this->config->toArray());
        $this->context = $this->factory->createContext();
    }//end __construct()

    /**
     * @param string $topicName topic name
     * @param string $body message body
     * @param array $properties message properties
     * @return \Enqueue\Sns\SnsMessage|\Interop\Queue\Message
     * @throws \Interop\Queue\Exception
     * @throws \Interop\Queue\Exception\InvalidDestinationException
     * @throws \Interop\Queue\Exception\InvalidMessageException
     */
    public function send(string $topicName, string $body, array $properties = [])
    {
        $topic = $this->context->createTopic($topicName);
        $this->context->declareTopic($topic);

        $message = $this->context->createMessage($body, $properties);
        $this->context->createProducer()->send($topic, $message);

        return $message;
    }

    /**
     * @return \Brighte\Infrastructure\Aws\Sns\SnsConfig
     */
    public function getConfig(): SnsConfig
    {
        return $this->config;
    }

    /**
     * @param \Brighte\Infrastructure\Aws\Sns\SnsConfig $config
     * @return SnsClient
     */
    public function setConfig(SnsConfig $config): SnsClient
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return \Enqueue\Sns\SnsConnectionFactory
     */
    public function getFactory(): SnsConnectionFactory
    {
        return $this->factory;
    }

    /**
     * @param \Enqueue\Sns\SnsConnectionFactory $factory
     * @return SnsClient
     */
    public function setFactory(SnsConnectionFactory $factory): SnsClient
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * @return \Enqueue\Sns\SnsContext
     */
    public function getContext(): SnsContext
    {
        return $this->context;
    }

    /**
     * @param \Enqueue\Sns\SnsContext $context
     * @return SnsClient
     */
    public function setContext(SnsContext $context): SnsClient
    {
        $this->context = $context;

        return $this;
    }//end publish()

}//end class
