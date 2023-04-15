# Elastic APM Integration for Magento 2

Stability : Experimental, work (very much) in early stages.

![Screenshot of Magento Elastic APM trace](./public/apm-screenshot.png?raw=true "Screenshot of Magento Elastic APM trace")
![Screenshot of Magento Elastic APM trace showing MySQL query](./public/apm-screenshot-2.png?raw=true "Screenshot of Magento Elastic APM trace showing MySQL query.")

## Installation:

1) Install the module with composer
```bash
composer config repositories.cmtickle/module-elastic-apm vcs https://github.com/cmtickle/elastic-apm-magento.git
composer require --dev cmtickle/module-elastic-apm:dev-develop@dev
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

## Running Kibana + Elastic APM:

A sample docker-compose file is included which provisions Elastic APM, Elasticsearch and Kibana accordingly.

To use these, you will need to have a working Docker install and the docker-compose tool. Once these are in place, follow 
the instructions below.

* Go to this module folder (usually `cd ./vendor/cmtickle/module-elastic-apm`).
* Start the services with docker-compose `docker-compose -f docker/docker-compose.yml up`
* Access Kibana with a web browser [http://127.0.0.1:5601/](http://127.0.0.1:5601/)
* Under the 'Observability' menu, you will find APM. At this point, there will be no data logged.
* Configure this module in your instance of Magento, per the above instructions, to point at `'serverUrl' => 'http://127.0.0.1:8200',`
* Load a page in Magento a few times and check back under the APM tab in Kibana a few minutes. 

Barring any local networking issues with Magento > APM, you should now see APM data. For the MySQL traces (if you enabled 
these) you can click on these and see which query was being performed.

## Thanks

[Elastic APM: PHP Agent](https://github.com/nipwaayoni/elastic-apm-php-agent/): The integration agent this module depends 
on.

[Holdenovi_Profiler](https://github.com/perryholden/Holdenovi_Profiler): Really helpful as a reference for a Magento 2 
Profiler Driver.

[Elastic APM, Laravel Driver](https://github.com/arkaitzgarro/elastic-apm-laravel/): My reference for how to call Elastic 
APM from PHP. 
