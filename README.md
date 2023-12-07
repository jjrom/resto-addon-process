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

docker run -d -v /var/run/docker.sock:/var/run/docker.sock -p 127.0.0.1:1234:2375 bobrik/socat TCP-LISTEN:2375,fork UNIX-CONNECT:/var/run/docker.sock

## Test

Deploy a process

    curl -v -X POST -d@data/echoProcess.json "http://admin:admin@localhost:5252/oapi-p/processes"
    
Replace a process

    curl -v -X PUT -d@data/echoProcess.json "http://admin:admin@localhost:5252/oapi-p/processes"

Get a process

    curl -v -X GET "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess"
    
Undeploy a process

    curl -X DELETE "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess"
    
Execute a process

    curl -X POST -d@data/echoProcessExecution.json "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess/execution"
    

