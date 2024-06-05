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

class ClusterVolumeSpecAccessModeCapacityRangeNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use CheckArray;
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use ValidatorTrait;

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return 'Docker\\API\\Model\\ClusterVolumeSpecAccessModeCapacityRange' === $type;
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return \is_object($data) && 'Docker\\API\\Model\\ClusterVolumeSpecAccessModeCapacityRange' === $data::class;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['$ref'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        $object = new \Docker\API\Model\ClusterVolumeSpecAccessModeCapacityRange();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('RequiredBytes', $data) && null !== $data['RequiredBytes']) {
            $object->setRequiredBytes($data['RequiredBytes']);
            unset($data['RequiredBytes']);
        } elseif (\array_key_exists('RequiredBytes', $data) && null === $data['RequiredBytes']) {
            $object->setRequiredBytes(null);
        }
        if (\array_key_exists('LimitBytes', $data) && null !== $data['LimitBytes']) {
            $object->setLimitBytes($data['LimitBytes']);
            unset($data['LimitBytes']);
        } elseif (\array_key_exists('LimitBytes', $data) && null === $data['LimitBytes']) {
            $object->setLimitBytes(null);
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
        if ($object->isInitialized('requiredBytes') && null !== $object->getRequiredBytes()) {
            $data['RequiredBytes'] = $object->getRequiredBytes();
        }
        if ($object->isInitialized('limitBytes') && null !== $object->getLimitBytes()) {
            $data['LimitBytes'] = $object->getLimitBytes();
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
        return ['Docker\\API\\Model\\ClusterVolumeSpecAccessModeCapacityRange' => false];
    }
}
