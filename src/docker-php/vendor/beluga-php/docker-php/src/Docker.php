<?php

declare(strict_types=1);

namespace Docker;

use Docker\API\Client;
use Docker\API\Model\AuthConfig;
use Docker\Endpoint\ImagePush;
use Docker\API\Runtime\Client\Client as RuntimeClient;
use Docker\API\Exception\BadRequestException;

/**
 * Docker\Docker.
 */
class Docker extends Client
{
    public function imagePush(string $name, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        if (isset($headerParameters['X-Registry-Auth']) && $headerParameters['X-Registry-Auth'] instanceof AuthConfig) {
            $headerParameters['X-Registry-Auth'] = base64_encode($this->serializer->serialize($headerParameters['X-Registry-Auth'], 'json'));
        }

        return $this->executeEndpoint(new ImagePush($name, $queryParameters, $headerParameters, $accept), $fetch);
    }

    public static function create($httpClient = null, array $additionalPlugins = [], array $additionalNormalizers = [])
    {
        if (null === $httpClient) {
            $httpClient = DockerClientFactory::createFromEnv();
        }

        $client = parent::create($httpClient, $additionalPlugins, $additionalNormalizers);
        $testClient = $client->systemInfo(RuntimeClient::FETCH_RESPONSE)->getBody()->getContents();
        $jsonObj = json_decode($testClient);

        if ($jsonObj !== null) {
            if (isset($jsonObj->message)) {
                // Check if the client is too new
                if (strpos($jsonObj->message, 'client version') !== false && strpos($jsonObj->message, 'is too new') !== false) {
                    throw new BadRequestException("The client version is not supported by your version of Docker. Message: {$jsonObj->message}");
                } else {
                    throw new BadRequestException($jsonObj->message);
                }
            }
        } else {
            throw new BadRequestException("Failed to decode JSON.");
        }
        return $client;
    }
}
