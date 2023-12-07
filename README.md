# resto-addon-process
Process managament plugin for resto

This addon uses:

* [beluga-php/docker-php](https://github.com/beluga-php/docker-php)
* [beluga-php/docker-php-api](https://github.com/beluga-php/docker-php-api)

Installation trough composer under src/docker-php

    composer require beluga-php/docker-php
    composer require beluga-php/docker-php-api

## Docker host configuration
MacOS does not provide docker TCP connection by default. Needs to run socat

docker run -d -v /var/run/docker.sock:/var/run/docker.sock -p 127.0.0.1:1234:1234 bobrik/socat TCP-LISTEN:1234,fork UNIX-CONNECT:/var/run/docker.sock

## Test

Ingest a process

    curl -v -X POST -d@data/processExample.json "http://admin:admin@localhost:5252/oapi-p/processes"
    
Delete a process

    curl -X DELETE "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess"
    
Execute a process

    curl -X POST -d@data/executionExample.json "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess/execution"
    

