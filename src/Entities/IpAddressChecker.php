<?php

namespace Matriphe\Larinfo\Entities;

class IpAddressChecker
{
    /**
     * @var string|null
     */
    private ?string $ipAddress;

    /**
     * @param string|null $ipAddress
     */
    public function __construct(?string $ipAddress = null)
    {
        $this->setIpAddress($ipAddress);
    }

    /**
     * @param  string|null $ipAddress
     * @return $this
     */
    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return filter_var($this->ipAddress, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * @return bool|null
     */
    public function isPrivate(): ?bool
    {
        if (! $this->isValid()) {
            return null;
        }

        return ! filter_var(
            $this->ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
