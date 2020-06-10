<?php

declare(strict_types = 1);

namespace Brighte\Infrastructure\Log\Monolog;

use Monolog\ErrorHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RavenHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;
use Raven_Client;

class MonologFactory
{

    /** @var string */
    protected $configFile;

    /** @var \Brighte\Infrastructure\Log\Monolog\MonologConfigFactory */
    protected $configFactory;

    /**
     * MonologFactory constructor.
     *
     * @param string|null $configFile
     * @throws \Brighte\Infrastructure\Log\Monolog\Exceptions\MonologConfigException
     * @throws \ServiceSchema\Json\Exception\JsonException
     */
    public function __construct(string $configFile = null)
    {
        $this->configFile = $configFile;
        $this->configFactory = new MonologConfigFactory($this->configFile);
    }

    /**
     * @param string|null $configName
     * @return \Monolog\Logger
     * @throws \Brighte\Infrastructure\Log\Monolog\Exceptions\MonologConfigException
     * @throws \Exception
     */
    public function create(string $configName = null)
    {
        /** @var \Brighte\Infrastructure\Log\Monolog\MonologConfig $config */
        $config = $this->configFactory->get($configName);

        $logger = new Logger($config->getName());
        $handler = new ErrorHandler($logger);
        $handler->registerErrorHandler([], false);
        $handler->registerExceptionHandler();
        $handler->registerFatalHandler();

        if ($config->isSentry()) {
            $ravenHandler = new RavenHandler(new Raven_Client($config->getSentryConfig()));
            $logger->pushHandler(
                new FingersCrossedHandler(
                    $ravenHandler,
                    Logger::ERROR,
                    1000,
                    true,
                    false
                )
            );

            $logger->pushProcessor(new ProcessIdProcessor);
            $logger->pushProcessor(new UidProcessor);
            $logger->pushProcessor(new WebProcessor);
            $logger->pushProcessor(new MemoryUsageProcessor);
            $logger->pushProcessor(new IntrospectionProcessor);
        }

        $stream = new StreamHandler($config->getPath(), $config->getLevel());
        $stream->setFormatter(new \Monolog\Formatter\JsonFormatter());
        $logger->pushHandler($stream);

        return $logger;
    }//end create()

}//end class
