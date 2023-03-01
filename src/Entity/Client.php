<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'invalid_email')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 11)]
    #[AppAssert\CPF(message: 'invalid_cpf')]
    private ?string $cpf = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $growattName = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: DayGeneration::class)]
    private Collection $dayGenerations;

    public function __construct()
    {
        $this->dayGenerations = new ArrayCollection();
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

    public function getCPF(): ?string
    {
        return $this->cpf;
    }

    public function setCPF(string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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

    public function getRolesToString(): string
    {
        $str = '';
        $len = sizeof($this->getRoles()) - 1;

        foreach ($this->getRoles() as $key => $value) {
            $str .= $key === $len ? $value : sprintf('%s, ', $value);
        }

        return $str;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGrowattName(): ?string
    {
        return $this->growattName;
    }

    public function setGrowattName(string $growattName): self
    {
        $this->growattName = $growattName;

        return $this;
    }

    public function toArray(bool $strings = false)
    {
        return [
            'Id' => $this->getId(),
            'Email' => $this->getEmail(),
            'Name' => $this->getName(),
            'CPF' => $this->getCPF(),
            'Roles' => $strings ? $this->getRolesToString() : $this->getRoles(),
            'Password' => $this->getPassword(),
            'GrowattName' => $this->getGrowattName(),
        ];
    }

    /**
     * @return Collection<int, DayGeneration>
     */
    public function getDayGenerations(): Collection
    {
        return $this->dayGenerations;
    }

    public function addDayGeneration(DayGeneration $dayGeneration): self
    {
        if (!$this->dayGenerations->contains($dayGeneration)) {
            $this->dayGenerations->add($dayGeneration);
            $dayGeneration->setClient($this);
        }

        return $this;
    }

    public function removeDayGeneration(DayGeneration $dayGeneration): self
    {
        if ($this->dayGenerations->removeElement($dayGeneration)) {
            // set the owning side to null (unless already changed)
            if ($dayGeneration->getClient() === $this) {
                $dayGeneration->setClient(null);
            }
        }

        return $this;
    }
}
