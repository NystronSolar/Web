<?php

namespace App\Entity;

use App\Repository\BillRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillRepository::class)]
class Bill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $price = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $actualReadingDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $nextReadingDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $previousReadingDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $generationBalance = null;

    #[ORM\Column(length: 255)]
    private ?string $energyConsumed = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $energyExcess = null;

    #[ORM\ManyToOne(inversedBy: 'bills')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $dateMonth = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $dateYear = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getActualReadingDate(): ?\DateTimeInterface
    {
        return $this->actualReadingDate;
    }

    public function setActualReadingDate(\DateTimeInterface $actualReadingDate): self
    {
        $this->actualReadingDate = $actualReadingDate;

        return $this;
    }

    public function getNextReadingDate(): ?\DateTimeInterface
    {
        return $this->nextReadingDate;
    }

    public function setNextReadingDate(\DateTimeInterface $nextReadingDate): self
    {
        $this->nextReadingDate = $nextReadingDate;

        return $this;
    }

    public function getPreviousReadingDate(): ?\DateTimeInterface
    {
        return $this->previousReadingDate;
    }

    public function setPreviousReadingDate(?\DateTimeInterface $previousReadingDate): self
    {
        $this->previousReadingDate = $previousReadingDate;

        return $this;
    }

    public function getGenerationBalance(): ?string
    {
        return $this->generationBalance;
    }

    public function setGenerationBalance(string $generationBalance): self
    {
        $this->generationBalance = $generationBalance;

        return $this;
    }

    public function getEnergyConsumed(): ?string
    {
        return $this->energyConsumed;
    }

    public function setEnergyConsumed(string $energyConsumed): self
    {
        $this->energyConsumed = $energyConsumed;

        return $this;
    }

    public function getEnergyExcess(): ?string
    {
        return $this->energyExcess;
    }

    public function setEnergyExcess(string $energyExcess): self
    {
        $this->energyExcess = $energyExcess;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDateMonth(): ?int
    {
        return $this->dateMonth;
    }

    public function setDateMonth(int $dateMonth): self
    {
        $this->dateMonth = $dateMonth;

        return $this;
    }

    public function getDateYear(): ?int
    {
        return $this->dateYear;
    }

    public function setDateYear(int $dateYear): self
    {
        $this->dateYear = $dateYear;

        return $this;
    }

    /**
     * Generate an DateTime object based in dateYear and dateMonth properties.
     */
    public function generateDateTimeObject(): \DateTime
    {
        $month = $this->getDateMonth();
        $year = $this->getDateYear();
        $date = \DateTime::createFromFormat('d/m/Y', "1/$month/$year");

        return $date;
    }
}
