# Elastic APM Integration for Magento 2

Stability : Experimental, work (very much) in early stages.

## Installation:

1) Install the module with composer
```bash
composer config repositories.cmtickle/module-elastic-apm vcs https://github.com/cmtickle/elastic-apm-magento.git
composer config repositories.nipwaayoni/elastic-apm-php-agent vcs https://github.com/cmtickle/elastic-apm-php-agent.git
composer require cmtickle/module-elastic-apm:dev-develop  nipwaayoni/elastic-apm-php-agent:dev-elastic-apm-magento@dev
```

2) Create a file at `app/etc/apm.php` based on the below, containing values appropriate to your environment.
```php
 <?php

return [
    'serverUrl'             => 'http://apm-server:8200',
    'secretToken'           => null,
    'hostname'              => 'localhost',
    'serviceName'           => 'magento',
    'serviceVersion'        => null,
    'frameworkName'         => 'magento2',
    'frameworkVersion'      => '2.4.5-p1',
    'enabled'               => true,
    'timeout'               => 10,
    'environment'           => 'local',
    'stackTraceLimit'       => 1000,
    'transactionSampleRate' => 1,
];
```

3) Enable the profiler by running
```bash
bin/magento dev:profiler:enable '{"drivers":[{"type":"Cmtickle\\ElasticApm\\Profiler\\Driver"}]}'
```
