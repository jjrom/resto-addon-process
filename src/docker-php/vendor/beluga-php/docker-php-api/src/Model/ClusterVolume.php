<?php

declare(strict_types=1);

namespace Docker\API\Model;

class ClusterVolume extends \ArrayObject
{
    /**
     * @var array
     */
    protected $initialized = [];

    public function isInitialized($property): bool
    {
        return \array_key_exists($property, $this->initialized);
    }
    /**
     * The Swarm ID of this volume. Because cluster volumes are Swarm
     * objects, they have an ID, unlike non-cluster volumes. This ID can
     * be used to refer to the Volume instead of the name.
     *
     * @var string|null
     */
    protected $iD;
    /**
     * The version number of the object such as node, service, etc. This is needed
     * to avoid conflicting writes. The client must send the version number along
     * with the modified specification when updating these objects.
     *
     * This approach ensures safe concurrency and determinism in that the change
     * on the object may not be applied if the version number has changed from the
     * last read. In other words, if two update requests specify the same base
     * version, only one of the requests can succeed. As a result, two separate
     * update requests that happen at the same time will not unintentionally
     * overwrite each other.
     *
     * @var ObjectVersion|null
     */
    protected $version;
    /**
     * @var string|null
     */
    protected $createdAt;
    /**
     * @var string|null
     */
    protected $updatedAt;
    /**
     * Cluster-specific options used to create the volume.
     *
     * @var ClusterVolumeSpec|null
     */
    protected $spec;
    /**
     * Information about the global status of the volume.
     *
     * @var ClusterVolumeInfo|null
     */
    protected $info;
    /**
     * The status of the volume as it pertains to its publishing and use on
     * specific nodes.
     *
     * @var ClusterVolumePublishStatusItem[]|null
     */
    protected $publishStatus;

    /**
     * The Swarm ID of this volume. Because cluster volumes are Swarm
     * objects, they have an ID, unlike non-cluster volumes. This ID can
     * be used to refer to the Volume instead of the name.
     */
    public function getID(): ?string
    {
        return $this->iD;
    }

    /**
     * The Swarm ID of this volume. Because cluster volumes are Swarm
     * objects, they have an ID, unlike non-cluster volumes. This ID can
     * be used to refer to the Volume instead of the name.
     */
    public function setID(?string $iD): self
    {
        $this->initialized['iD'] = true;
        $this->iD = $iD;

        return $this;
    }

    /**
     * The version number of the object such as node, service, etc. This is needed
     * to avoid conflicting writes. The client must send the version number along
     * with the modified specification when updating these objects.
     *
     * This approach ensures safe concurrency and determinism in that the change
     * on the object may not be applied if the version number has changed from the
     * last read. In other words, if two update requests specify the same base
     * version, only one of the requests can succeed. As a result, two separate
     * update requests that happen at the same time will not unintentionally
     * overwrite each other.
     */
    public function getVersion(): ?ObjectVersion
    {
        return $this->version;
    }

    /**
     * The version number of the object such as node, service, etc. This is needed
     * to avoid conflicting writes. The client must send the version number along
     * with the modified specification when updating these objects.
     *
     * This approach ensures safe concurrency and determinism in that the change
     * on the object may not be applied if the version number has changed from the
     * last read. In other words, if two update requests specify the same base
     * version, only one of the requests can succeed. As a result, two separate
     * update requests that happen at the same time will not unintentionally
     * overwrite each other.
     */
    public function setVersion(?ObjectVersion $version): self
    {
        $this->initialized['version'] = true;
        $this->version = $version;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): self
    {
        $this->initialized['createdAt'] = true;
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->initialized['updatedAt'] = true;
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Cluster-specific options used to create the volume.
     */
    public function getSpec(): ?ClusterVolumeSpec
    {
        return $this->spec;
    }

    /**
     * Cluster-specific options used to create the volume.
     */
    public function setSpec(?ClusterVolumeSpec $spec): self
    {
        $this->initialized['spec'] = true;
        $this->spec = $spec;

        return $this;
    }

    /**
     * Information about the global status of the volume.
     */
    public function getInfo(): ?ClusterVolumeInfo
    {
        return $this->info;
    }

    /**
     * Information about the global status of the volume.
     */
    public function setInfo(?ClusterVolumeInfo $info): self
    {
        $this->initialized['info'] = true;
        $this->info = $info;

        return $this;
    }

    /**
     * The status of the volume as it pertains to its publishing and use on
     * specific nodes.
     *
     * @return ClusterVolumePublishStatusItem[]|null
     */
    public function getPublishStatus(): ?array
    {
        return $this->publishStatus;
    }

    /**
     * The status of the volume as it pertains to its publishing and use on
     * specific nodes.
     *
     * @param ClusterVolumePublishStatusItem[]|null $publishStatus
     */
    public function setPublishStatus(?array $publishStatus): self
    {
        $this->initialized['publishStatus'] = true;
        $this->publishStatus = $publishStatus;

        return $this;
    }
}
