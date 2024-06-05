<?php

declare(strict_types=1);

namespace Docker\API\Model;

class VolumeCreateOptions extends \ArrayObject
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
     * The new volume's name. If not specified, Docker generates a name.
     *
     * @var string|null
     */
    protected $name;
    /**
     * Name of the volume driver to use.
     *
     * @var string|null
     */
    protected $driver = 'local';
    /**
     * A mapping of driver options and values. These options are
     * passed directly to the driver and are driver specific.
     *
     * @var array<string, string>|null
     */
    protected $driverOpts;
    /**
     * User-defined key/value metadata.
     *
     * @var array<string, string>|null
     */
    protected $labels;
    /**
     * Cluster-specific options used to create the volume.
     *
     * @var ClusterVolumeSpec|null
     */
    protected $clusterVolumeSpec;

    /**
     * The new volume's name. If not specified, Docker generates a name.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * The new volume's name. If not specified, Docker generates a name.
     */
    public function setName(?string $name): self
    {
        $this->initialized['name'] = true;
        $this->name = $name;

        return $this;
    }

    /**
     * Name of the volume driver to use.
     */
    public function getDriver(): ?string
    {
        return $this->driver;
    }

    /**
     * Name of the volume driver to use.
     */
    public function setDriver(?string $driver): self
    {
        $this->initialized['driver'] = true;
        $this->driver = $driver;

        return $this;
    }

    /**
     * A mapping of driver options and values. These options are
     * passed directly to the driver and are driver specific.
     *
     * @return array<string, string>|null
     */
    public function getDriverOpts(): ?iterable
    {
        return $this->driverOpts;
    }

    /**
     * A mapping of driver options and values. These options are
     * passed directly to the driver and are driver specific.
     *
     * @param array<string, string>|null $driverOpts
     */
    public function setDriverOpts(?iterable $driverOpts): self
    {
        $this->initialized['driverOpts'] = true;
        $this->driverOpts = $driverOpts;

        return $this;
    }

    /**
     * User-defined key/value metadata.
     *
     * @return array<string, string>|null
     */
    public function getLabels(): ?iterable
    {
        return $this->labels;
    }

    /**
     * User-defined key/value metadata.
     *
     * @param array<string, string>|null $labels
     */
    public function setLabels(?iterable $labels): self
    {
        $this->initialized['labels'] = true;
        $this->labels = $labels;

        return $this;
    }

    /**
     * Cluster-specific options used to create the volume.
     */
    public function getClusterVolumeSpec(): ?ClusterVolumeSpec
    {
        return $this->clusterVolumeSpec;
    }

    /**
     * Cluster-specific options used to create the volume.
     */
    public function setClusterVolumeSpec(?ClusterVolumeSpec $clusterVolumeSpec): self
    {
        $this->initialized['clusterVolumeSpec'] = true;
        $this->clusterVolumeSpec = $clusterVolumeSpec;

        return $this;
    }
}
