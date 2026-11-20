<?php

declare(strict_types=1);

namespace App\Import;

final readonly class ProjectCsvRow
{
    public function __construct(
        public int $lineNumber,
        public string $sapNumber,
        public string $status,
        public string $projectName,
        public string $customerSapId,
        public string $customerName,
    ) {
    }

    public function hasRequiredFields(): bool
    {
        return $this->sapNumber !== ''
            && $this->projectName !== ''
            && $this->customerSapId !== '';
    }
}
