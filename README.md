# resto-addon-process
Process managament plugin for resto

This addon uses:

* [beluga-php/docker-php](https://github.com/beluga-php/docker-php)
* [beluga-php/docker-php-api](https://github.com/beluga-php/docker-php-api)

Installation trough composer under src/docker-php

    composer require beluga-php/docker-php
    composer require beluga-php/docker-php-api

Test it within php:

        $client = DockerClientFactory::create([
            'remote_socket' => 'tcp://host.docker.internal:1234',
            'ssl' => false,
        ]);
        $docker = Docker::create($client);

        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['sleep', '1m']);
        $containerCreateResult = $docker->containerCreate($containerConfig);
        $containerId = $containerCreateResult->getId();
        $docker->containerStart($containerId);
        return array(
            'containerId' => $containerId
        );
    
## Docker host configuration
MacOS does not provide docker TCP connection by default. Needs to run socat

docker run -d -v /var/run/docker.sock:/var/run/docker.sock -p 127.0.0.1:1234:1234 bobrik/socat TCP-LISTEN:1234,fork UNIX-CONNECT:/var/run/docker.sock

## Test

Ingest a process

    curl -X POST -d@data/processExample.json "http://admin:admin@localhost:5252/oapi-p/processes"
    
Execute a process

    curl -X POST -d@data/executionExample.json "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess/execution"
    

