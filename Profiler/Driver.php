<?php

namespace Cmtickle\ElasticApm\Profiler;

use GuzzleHttp\Client;
use Magento\Framework\Profiler\DriverInterface;
use Nipwaayoni\Config;
use Nipwaayoni\AgentBuilder;
use Nipwaayoni\ApmAgent;
use Nipwaayoni\Events\Transaction;
use Nipwaayoni\Exception\ConfigurationException;
use Nipwaayoni\Exception\MissingServiceNameException;
use Nipwaayoni\Exception\Helper\UnsupportedConfigurationValueException;

class Driver implements DriverInterface
{
    const APM_CONFIG = "app/etc/apm.php";

    /**
     * @var array
     */
    private array $config;

    /**
     * @var ApmAgent
     */
    protected ApmAgent $agent;

    /**
     * @var Transaction
     */
    protected Transaction $transaction;

    /**
     * @param array $config
     */
    public function __construct(
        array $config
    ) {
        $this->config = $config;
        $this->init();
        register_shutdown_function([$this, 'send']);
    }

    /**
     * @throws ConfigurationException
     * @throws MissingServiceNameException
     * @throws UnsupportedConfigurationValueException
     */
    public function init()
    {
        $apmConfigFile = $this->config['baseDir'] . DIRECTORY_SEPARATOR . self::APM_CONFIG;
        if (!file_exists($apmConfigFile)) {
            throw new ConfigurationException(self::APM_CONFIG . " not available");
        }

        $nipwaayoniConfig = new Config(include $apmConfigFile);

        $this->agent = (new AgentBuilder())
            ->withConfig($nipwaayoniConfig)
            ->withHttpClient(new Client() )
            ->build();

        $this->transaction = $this->agent->startTransaction('Test of module');
    }

    public function start($timerId, array $tags = null)
    {

    }

    public function stop($timerId)
    {
    }

    public function clear($timerId = null)
    {
    }

    public function send()
    {
        $this->agent->stopTransaction($this->transaction->getTransactionName(), [
            'status'  => '200',
            'payload' => [ 'foo' => 'bar' ],
        ]);

        $this->agent->send();
    }
}
