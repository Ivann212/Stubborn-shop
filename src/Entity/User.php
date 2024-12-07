<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $nema;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $deliveryAddress;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNema(): ?string
    {
        return $this->nema;
    }

    public function setNema(string $nema): self
    {
        $this->nema = $nema;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    /**
     * Get the username of the user (this method replaces getUsername())
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Get the roles of the user. Each user must have at least one role.
     *
     * @return array
     */
    public function getRoles(): array
    {
        // By default, every user has the ROLE_USER role
        $roles = ['ROLE_USER'];
        
        // You can add more roles depending on the user's permissions, for example:
        if ($this->role === 'admin') {
            $roles[] = 'ROLE_ADMIN';
        }
        
        return $roles;
    }

    /**
     * This method is used to erase sensitive data after authentication.
     * It can be left empty if you don't have additional sensitive data to erase.
     */
    public function eraseCredentials(): void
    {
        // If you store sensitive data (like plain text passwords or tokens) in your user, you should erase them here.
        // In this case, we don't have any additional sensitive data, so this can remain empty.
    }

    /**
     * You can also implement this method (getSalt) if you are using an encoder 
     * that requires a "salt". 
     * However, for bcrypt/argon2 this is not needed, so you can leave it empty.
     */
    public function getSalt(): ?string
    {
        return null;  // We don't need a salt for bcrypt/argon2.
    }

    /**
     * Symfony still expects getUsername() in some cases, so we'll implement it.
     * In this case, we return the same as getUserIdentifier().
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }
}
