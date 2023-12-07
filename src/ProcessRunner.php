<?php
/*
 * Copyright (C) 2023 Jérôme Gasperi <jerome.gasperi@gmail.com>
 *
 * This file is subject to the terms and conditions defined in
 * file 'LICENSE.txt', which is part of this source code package.
 */

require_once(realpath(dirname(__FILE__)) . '/docker-php/vendor/autoload.php');
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\API\Model\ContainersCreatePostBody;

/** 
 * Docker utility class
 */
class ProcessRunner
{
    
    /**
     * Reference to docker object
     */
    private $docker;

    /**
     * Constructor
     */
    public function __construct($options)
    {
        $client = DockerClientFactory::create([
            'remote_socket' => $options['dockerHost'] ?? 'tcp://host.docker.internal:1234',
            'ssl' => $options['ssl'] ?? false,
        ]);
        
        $this->docker = Docker::create($client);
        
    }

    /**
     * Start container with the given params
     */
    public function startContainer($imageName, $env, $cmd)
    {

        if ( !isset($imageName) ) {
            return RestoUtil::httpError(400, 'Process docker image name is not set');
        }

        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage($imageName);
        
        if ( !empty($env) ) {
            $containerConfig->setEnv($env);
        }

        if ( !empty($cmd) ) {
            $containerConfig->setCmd($cmd);
        }

        $containerCreateResult = $this->docker->containerCreate($containerConfig);
        $containerId = $containerCreateResult->getId();
        $this->docker->containerStart($containerId);
        
        return array(
            'containerId' => $containerId
        );

    }

    /**
     * Stop container
     */
    public function stopContainer($containerId)
    {

    }


}
