<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PublicHolidayRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PublicHolidayRepository::class)]
#[ORM\Table(name: 'DaysoffsGlobal', indexes: [
    new ORM\Index(name: 'date_idx', columns: ['DaysoffsGlobalDate']),
], uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_public_holiday_date', columns: ['DaysoffsGlobalDate']),
    new ORM\UniqueConstraint(name: 'uniq_public_holiday_name', columns: ['DaysoffsGlobalName']),
])]
#[UniqueEntity(fields: ['date'], message: 'A public holiday already exists on this date.')]
#[UniqueEntity(fields: ['name'], message: 'A public holiday with this name already exists.')]
class PublicHoliday
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'DaysoffsGlobalDate', type: 'date_immutable')]
    private DateTimeImmutable $date;

    #[ORM\Column(name: 'DaysoffsGlobalName', type: 'string', length: 100)]
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

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): self
    {
        $this->name = trim($name);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
