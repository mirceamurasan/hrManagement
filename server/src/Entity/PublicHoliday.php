<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PublicHolidayRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PublicHolidayRepository::class)]
#[ORM\Table(name: 'DaysoffsGlobal')]
#[UniqueEntity(fields: ['date'], message: 'A public holiday already exists on this date.')]
#[UniqueEntity(fields: ['name'], message: 'A public holiday with this name already exists.')]
class PublicHoliday
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'DaysoffsGlobalDate', type: 'date_immutable', unique: true)]
    private DateTimeImmutable $date;

    #[ORM\Column(name: 'DaysoffsGlobalName', type: 'string', length: 100, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $name;

    public function __construct(DateTimeImmutable $date, string $name)
    {
        $this->date = $date->setTime(0, 0);
        $this->name = trim($name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function reschedule(DateTimeImmutable $date): self
    {
        $this->date = $date->setTime(0, 0);

        return $this;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        return $this->reschedule($date);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): self
    {
        $this->name = trim($name);

        return $this;
    }

    public function setName(string $name): self
    {
        return $this->rename($name);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
