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
    'enabled'               => true,
    'transactionSampleRate' => 1, // Decimal, 0 to 1 .. e.g. 0.5 = 50% of transactions traced, 1 = 100%.
    'serviceName'           => 'magento', // Overridden by $_SERVER['HTTP_HOST'], special characters replaced with hyphens.
    'hostname'              => 'localhost', // Overridden by $_SERVER['HOSTNAME'].
    'environment'           => 'local',
    'stackTraceLimit'       => 1000,
    /*'secretToken'           => null,
    'serviceVersion'        => null,
    'frameworkName'         => 'magento2',
    'frameworkVersion'      => '2.4.5-p1',
    'timeout'               => 10,*/
];
```

3) Enable the profiler by running
```bash
bin/magento dev:profiler:enable '{"drivers":[{"type":"Cmtickle\\ElasticApm\\Profiler\\Driver"}]}'
```

4) (Optionally) Enable the database profiler
Edit `app/etc/env.php` and add the below underneath `db > connection > default`
```php
                'profiler' => [
                    'class' => '\\Cmtickle\\ElasticApm\\Profiler\\Db',
                    'enabled' => true
                ],
```
## Thanks

[Elastic APM: PHP Agent](https://github.com/nipwaayoni/elastic-apm-php-agent/): The integration agent this module depends 
on.

[Holdenovi_Profiler](https://github.com/perryholden/Holdenovi_Profiler): Really helpful as a reference for a Magento 2 
Profiler Driver.

[Elastic APM, Laravel Driver](https://github.com/arkaitzgarro/elastic-apm-laravel/): My reference for how to call Elastic 
APM from PHP. 
