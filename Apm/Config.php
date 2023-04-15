<?php
namespace Cmtickle\ElasticApm\Apm;

use Nipwaayoni\Exception\ConfigurationException;


class Config extends \Nipwaayoni\Config
{

    const APM_CONFIG = "app/etc/apm.php";

    /**
     * @param array $driverConfig
     * @throws ConfigurationException
     * @throws \Nipwaayoni\Exception\Helper\UnsupportedConfigurationValueException
     * @throws \Nipwaayoni\Exception\MissingServiceNameException
     */
    public function __construct(array $driverConfig = [])
    {
        $apmConfigFile = $driverConfig['baseDir'] . DIRECTORY_SEPARATOR . self::APM_CONFIG;
        if (!file_exists($apmConfigFile)) {
            throw new ConfigurationException(self::APM_CONFIG . " not available");
        }

        $apmConfig = include $apmConfigFile;
        $apmConfig['serviceName'] = self::getServiceName() ?? $apmConfig['serviceName'];
        $apmConfig['hostname'] = self::getHostname() ?? $apmConfig['hostname'];

        parent::__construct($apmConfig);
    }

    /**
     * @return array|string|string[]|null
     */
    public static function getServiceName()
    {
        return preg_replace('/[^A-Za-z0-9]/', '-', $_SERVER['HTTP_HOST']);
    }

    /**
     * @return mixed|null
     */
    public static function getHostname()
    {
        return $_SERVER['HOSTNAME'] ?? null;
    }
}
