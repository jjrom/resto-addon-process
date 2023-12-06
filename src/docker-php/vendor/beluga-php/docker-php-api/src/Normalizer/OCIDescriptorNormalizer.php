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

class OCIDescriptorNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use CheckArray;
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use ValidatorTrait;

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return 'Docker\\API\\Model\\OCIDescriptor' === $type;
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return \is_object($data) && 'Docker\\API\\Model\\OCIDescriptor' === $data::class;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['$ref'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        $object = new \Docker\API\Model\OCIDescriptor();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (\array_key_exists('mediaType', $data) && null !== $data['mediaType']) {
            $object->setMediaType($data['mediaType']);
            unset($data['mediaType']);
        } elseif (\array_key_exists('mediaType', $data) && null === $data['mediaType']) {
            $object->setMediaType(null);
        }
        if (\array_key_exists('digest', $data) && null !== $data['digest']) {
            $object->setDigest($data['digest']);
            unset($data['digest']);
        } elseif (\array_key_exists('digest', $data) && null === $data['digest']) {
            $object->setDigest(null);
        }
        if (\array_key_exists('size', $data) && null !== $data['size']) {
            $object->setSize($data['size']);
            unset($data['size']);
        } elseif (\array_key_exists('size', $data) && null === $data['size']) {
            $object->setSize(null);
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
        if ($object->isInitialized('mediaType') && null !== $object->getMediaType()) {
            $data['mediaType'] = $object->getMediaType();
        }
        if ($object->isInitialized('digest') && null !== $object->getDigest()) {
            $data['digest'] = $object->getDigest();
        }
        if ($object->isInitialized('size') && null !== $object->getSize()) {
            $data['size'] = $object->getSize();
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
        return ['Docker\\API\\Model\\OCIDescriptor' => false];
    }
}
