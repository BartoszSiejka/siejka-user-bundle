<?php

/*
 * This file is part of the ||application-name|| app.
 *
 * (c) Bartosz Siejka
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siejka\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 *
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class RemembermeToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=88, unique=true, nullable=false)
     */
    private $series;
    
    /**
     * @ORM\Column(type="string", length=88, nullable=false)
     */
    private $value;
    
    /**
     * @ORM\Column(name="lastUsed", type="datetime", nullable=false)
     */
    private $lastUsed;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $class;
    
    /**
     * @ORM\Column(type="string", length=200, nullable=false)
     */
    private $username;
    
    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): self
    {
        $this->series = $series;

        return $this;
    }
    
    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
    
    public function getLastUsed(): self
    {
        return $this->lastUsed;
    }

    public function setLastUsed(\DateTime $lastUsed): self
    {
        $this->lastUsed = $lastUsed;

        return $this;
    }
    
    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }
    
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
}
