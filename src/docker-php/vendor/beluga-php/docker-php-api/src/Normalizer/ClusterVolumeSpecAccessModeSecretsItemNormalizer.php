<?php

declare(strict_types=1);

namespace Docker\API\Normalizer;

use Docker\API\Runtime\Normalizer\CheckArray;
use Docker\API\Runtime\Normalizer\ValidatorTrait;
use Jane\Component\JsonSchemaRuntime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ClusterVolumeSpecAccessModeSecretsItemNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use CheckArray;
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use ValidatorTrait;

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return 'Docker\\API\\Model\\ClusterVolumeSpecAccessModeSecretsItem' === $type;
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return \is_object($data) && 'Docker\\API\\Model\\ClusterVolumeSpecAccessModeSecretsItem' === $data::class;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['$ref'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        $object = new \Docker\API\Model\ClusterVolumeSpecAccessModeSecretsItem();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('Key', $data) && null !== $data['Key']) {
            $object->setKey($data['Key']);
            unset($data['Key']);
        } elseif (\array_key_exists('Key', $data) && null === $data['Key']) {
            $object->setKey(null);
        }
        if (\array_key_exists('Secret', $data) && null !== $data['Secret']) {
            $object->setSecret($data['Secret']);
            unset($data['Secret']);
        } elseif (\array_key_exists('Secret', $data) && null === $data['Secret']) {
            $object->setSecret(null);
        }
        foreach ($data as $key => $value) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value;
            }
        }

        return $object;
    }

    /**
     * @return array|string|int|float|bool|\ArrayObject|null
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = [];
        if ($object->isInitialized('key') && null !== $object->getKey()) {
            $data['Key'] = $object->getKey();
        }
        if ($object->isInitialized('secret') && null !== $object->getSecret()) {
            $data['Secret'] = $object->getSecret();
        }
        foreach ($object as $key => $value) {
            if (preg_match('/.*/', (string) $key)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    public function getSupportedTypes(string $format = null): array
    {
        return ['Docker\\API\\Model\\ClusterVolumeSpecAccessModeSecretsItem' => false];
    }
}
