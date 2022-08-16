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

use Siejka\UserBundle\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
//use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 *
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class User implements UserInterface
{
//    use TimestampableEntity;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $locked;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lockedDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfFailedSignIn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginFailure;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginSuccess;
    
    /**
     * Date/Time of the last activity
     *
     * @var \Datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastActivityAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $groups = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=false)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifiedAt;
    
    public function __construct()
    {
        $this->enabled = true;
        $this->locked = false;
        $this->deleted = false;
        $this->numberOfFailedSignIn = 0;
        $this->setCreatedAt(new \DateTime());
        
        if ($this->getModifiedAt() == null) {
            $this->setModifiedAt(new \DateTime());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(): self
    {
        $this->enabled = true;

        return $this;
    }

    public function setDisabled(): self
    {
        $this->enabled = false;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }
    
    public function getLockedDate(): self
    {
        return $this->lockedDate;
    }

    public function setLocked(): self
    {
        $this->locked = true;
        $this->lockedDate = new \DateTime();

        return $this;
    }

    public function setUnlocked(): self
    {
        $this->locked = false;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }
    
    public function getDeletedDate(): self
    {
        return $this->deletedDate;
    }

    public function setDeleted(): self
    {
        $this->deleted = true;
        $this->deletedDate = new \DateTime();

        return $this;
    }
    
    public function getNumberOfFailedSignIn(): int
    {
        return $this->numberOfFailedSignIn;
    }

    public function setNumberOfFailedSignIn(int $numberOfFailedSignIn): self
    {
        $this->numberOfFailedSignIn = $numberOfFailedSignIn;

        return $this;
    }
    
    public function getLastLoginFailure(): ?\DateTime
    {
        return $this->lastLoginFailure;
    }

    public function setLastLoginFailure(\DateTime $lastLoginFailure): self
    {
        $this->lastLoginFailure = $lastLoginFailure;

        return $this;
    }
    
    public function getLastLoginSuccess(): ?\DateTime
    {
        return $this->lastLoginSuccess;
    }

    public function setLastLoginSuccess(\DateTime $lastLoginSuccess): self
    {
        $this->lastLoginSuccess = $lastLoginSuccess;

        return $this;
    }
    
    /**
     * @param \Datetime $lastActivityAt
     */
    public function setLastActivityAt($lastActivityAt)
    {
        $this->lastActivityAt = $lastActivityAt;
    }

    /**
     * @return \Datetime
     */
    public function getLastActivityAt()
    {
        return $this->lastActivityAt;
    }

    /**
     * @return Bool Whether the user is active or not
     */
    public function isActiveNow()
    {
        // Delay during wich the user will be considered as still active
        $delay = new \DateTime('2 minutes ago');

        return ( $this->getLastActivityAt() > $delay );
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    
    public function hasRole($role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    
    public function addRole(string $role): self
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_USER) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }
    
    /**
     * @see UserInterface
     */
    public function getGroups(): array
    {
        $groups = $this->groups;

        return array_unique($groups);
    }
    
    public function isInGroup($group): bool
    {
        return in_array(strtoupper($group), $this->getGroups(), true);
    }

    public function setGroups(array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }
    
    public function addGroup(string $group): self
    {
        $role = strtoupper($group);

        if (!in_array($group, $this->groups, true)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    
    /**
     * @param \Datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \Datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @param \Datetime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
    }

    /**
     * @return \Datetime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateModifiedAtDatetime() 
    {
        $this->setModifiedAt(new \DateTime());
    }
}
