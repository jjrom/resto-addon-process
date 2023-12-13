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

Deploy a process (user must be admin or belonging to group Process::RESTO_PROCESS_ADMIN_ID)

    curl -v -X POST -d@data/echoProcess.json "http://admin:admin@localhost:5252/oapi-p/processes"
    
Replace a process (user must be admin or belonging to group Process::RESTO_PROCESS_ADMIN_ID)

    curl -v -X PUT -d@data/echoProcess.json "http://admin:admin@localhost:5252/oapi-p/processes"

Get a process

    curl -v -X GET "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess"
    
Undeploy a process (user must be admin or belonging to group Process::RESTO_PROCESS_ADMIN_ID)

    curl -X DELETE "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess"
    
Execute a process (user must be admin or belonging to group Process::RESTO_PROCESS_ADMIN_ID)

    curl -X POST -d@data/echoProcessExecution.json "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess/execution"

Update a job (user must be admin or belonging to group Process::RESTO_PROCESS_ADMIN_ID)

    curl -X PUT -d@data/updateJobBad.json "http://localhost:5252/oapi-p/jobs/5504808c-6655-57ff-8472-92bd9c7363f9" -H 'Authorization: Bearer eyJzdWIiOiIyMjMxNDUyOTU1NDg1MTQzMDciLCJpYXQiOjE3MDI0NjM3NTgsImV4cCI6MTc4ODg2Mzc1OH0.J9jp4jyWuicy7OQ8R7UdbmH9tOOra9V_53gYhxmRi7Y'

## Create a process-admin user with Process:RESTO_PROCESS_ADMIN_ID group

    RANDOM_PASSWORD=$(cat /dev/urandom | base64 | tr -dc 'a-zA-Z0-9' | fold -w 16 | head -n 1);
    curl -X POST "http://localhost:5252/users" \
         -H "Content-Type: application/json" \
         -d '{"email":"process-admin","password":"'${RANDOM_PASSWORD}'","firstname":"process-admin","groups":[10]}'

