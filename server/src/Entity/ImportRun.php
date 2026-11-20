<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImportRunRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportRunRepository::class)]
#[ORM\Table(name: 'ImportRuns')]
class ImportRun
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(length: 30)]
    private string $kind;
    #[ORM\Column(length: 255)]
    private string $source;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $importedAt;
    #[ORM\Column(type: 'boolean')]
    private bool $success;
    #[ORM\Column(type: 'integer')]
    private int $createdCount = 0;
    #[ORM\Column(type: 'integer')]
    private int $updatedCount = 0;
    #[ORM\Column(type: 'integer')]
    private int $errorCount = 0;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;
    /** @var list<array{line:int, message:string}> */
    #[ORM\Column(type: 'json')]
    private array $errors = [];

    public function __construct(string $kind, string $source, bool $success, int $createdCount, int $updatedCount, array $errors, ?string $message = null)
    {
        $this->kind = $kind;
        $this->source = $source;
        $this->importedAt = new DateTimeImmutable();
        $this->success = $success;
        $this->createdCount = $createdCount;
        $this->updatedCount = $updatedCount;
        $this->errors = array_slice($errors, 0, 500);
        $this->errorCount = count($errors);
        $this->message = $message;
    }
    public function getId(): ?int { return $this->id; }
    public function getKind(): string { return $this->kind; }
    public function getSource(): string { return $this->source; }
    public function getImportedAt(): DateTimeImmutable { return $this->importedAt; }
    public function isSuccess(): bool { return $this->success; }
    public function getCreatedCount(): int { return $this->createdCount; }
    public function getUpdatedCount(): int { return $this->updatedCount; }
    public function getErrorCount(): int { return $this->errorCount; }
    public function getMessage(): ?string { return $this->message; }
    /** @return list<array{line:int, message:string}> */
    public function getErrors(): array { return $this->errors; }
}
