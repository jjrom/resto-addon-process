# resto-addon-process
Process managament plugin for resto

## Test

Ingest a process

    curl -X POST -d@data/processExample.json "http://admin:admin@localhost:5252/oapi-p/processes"
    
Execute a process

    curl -X POST -d@data/executionExample.json "http://admin:admin@localhost:5252/oapi-p/processes/EchoProcess/execution"
    