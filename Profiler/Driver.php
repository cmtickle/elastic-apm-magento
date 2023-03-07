<?php

namespace Cmtickle\ElasticApm\Profiler;

use GuzzleHttp\Client;
use Magento\Framework\Profiler\DriverInterface;
use Cmtickle\ElasticApm\Apm\Config;
use Nipwaayoni\AgentBuilder;
use Nipwaayoni\ApmAgent;
use Nipwaayoni\Events\Span;
use Nipwaayoni\Events\Transaction;
use Nipwaayoni\Exception\ConfigurationException;
use Nipwaayoni\Exception\MissingServiceNameException;
use Nipwaayoni\Exception\Helper\UnsupportedConfigurationValueException;

class Driver implements DriverInterface
{
    /**
     * @var array
     */
    private array $driverConfig;
    /**
     * @var Config
     */
    private Config $apmConfig;

    /**
     * @var ApmAgent|null
     */
    private ApmAgent $agent;

    /**
     * @var Transaction
     */
    private Transaction $transaction;

    /**
     * @var Span[]
     */
    public static array $callStack = [];

    /**
     * @param array $config
     */
    public function __construct(
        array $config
    ) {
        $this->driverConfig = $config;
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
        $this->apmConfig = new Config($this->driverConfig);
        $this->agent = (new AgentBuilder())
            ->withConfig($this->apmConfig)
            ->withHttpClient(new Client() )
            ->build();

        $this->transaction = $this->agent->startTransaction($_SERVER['REQUEST_URI']);
    }

    /**
     * @param $timerId
     * @return string
     */
    private function shortenTimerId($timerId)
    {
        list($timerId) = array_reverse(explode('>', $timerId));
        return $timerId;
    }

    /**
     * @return Span
     * @throws \Nipwaayoni\Exception\Timer\AlreadyRunningException
     */
    public function createSpan($timerId, array $tags)
    {
        $timerId = $this->shortenTimerId($timerId);
        $callDepth = count(self::$callStack);
        $parent = $callDepth ? self::$callStack[$callDepth - 1] : $this->transaction;
        $event = $this->agent->factory()->newSpan($timerId, $parent);
        $event->setType('app.internal');

        if (strstr($timerId, 'DB_QUERY') && isset($tags['statement'])) {
            $event->setType('db.mysql.query');
            $event->setAction('query');
            $event->setCustomContext(['db' => [
                'statement' => $tags['statement'], // the query being executed
                'type'      => 'sql',
            ]]);
        }

        return $event;
    }

    /**
     * @param $timerId
     * @param array|null $tags
     * @return void
     * @throws \Nipwaayoni\Exception\Timer\AlreadyRunningException
     */
    public function start($timerId, array $tags = null)
    {
        if ($this->apmConfig->notEnabled()) {
            return;
        }

        $event = $this->createSpan($timerId, $tags ?? []);
        $event->start();

        self::$callStack[] = $event;
    }

    /**
     * @param $timerId
     * @return void
     */
    public function stop($timerId)
    {
        if ($this->apmConfig->notEnabled()) {
            return;
        }
        $event = array_pop(self::$callStack);
        $event->stop();
        $this->agent->putEvent($event);
        $callDepth = count(self::$callStack);
    }

    /**
     * @param $timerId
     * @return void
     */
    public function clear($timerId = null)
    {
    }

    /**
     * @return void
     */
    public function send()
    {
        $this->agent->stopTransaction($this->transaction->getTransactionName(), [
            'status'  => '200'
        ]);

        $this->agent->send();
    }
}
