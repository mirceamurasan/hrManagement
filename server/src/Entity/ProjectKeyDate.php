<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectKeyDateRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectKeyDateRepository::class)]
#[ORM\Table(name: 'KeyDates')]
class ProjectKeyDate
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'ProjectsIdProjects', referencedColumnName: 'Id', nullable: false, onDelete: 'CASCADE')]
    private Project $project;
    #[ORM\Column(name: 'date', type: 'date_immutable')]
    private DateTimeImmutable $date;
    #[ORM\Column(name: 'details', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $details;

    public function __construct(Project $project, DateTimeImmutable $date, string $details)
    {
        $this->project = $project;
        $this->date = $date->setTime(0, 0);
        $this->details = trim($details);
    }
    public function getId(): ?int { return $this->id; }
    public function getProject(): Project { return $this->project; }
    public function setProject(Project $project): self { $this->project = $project; return $this; }
    public function getDate(): DateTimeImmutable { return $this->date; }
    public function setDate(DateTimeImmutable $date): self { $this->date = $date->setTime(0, 0); return $this; }
    public function getDetails(): string { return $this->details; }
    public function setDetails(string $details): self { $this->details = trim($details); return $this; }
}
