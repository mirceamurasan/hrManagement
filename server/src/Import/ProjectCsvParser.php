<?php

declare(strict_types=1);

namespace App\Import;

use Generator;
use RuntimeException;

/** Reads the semicolon-delimited project export used by the legacy importer. */
final class ProjectCsvParser
{
    private const int SAP_NUMBER_COLUMN = 5;
    private const int STATUS_COLUMN = 7;
    private const int PROJECT_NAME_COLUMN = 13;
    private const int CUSTOMER_SAP_ID_COLUMN = 18;
    private const int CUSTOMER_NAME_COLUMN = 29;

    /**
     * Stream rows from a CSV file without loading the entire import into memory.
     *
     * @return Generator<int, ProjectCsvRow>
     */
    public function parseFile(string $path): Generator
    {
        $file = fopen($path, 'rb');
        if ($file === false) {
            throw new RuntimeException(sprintf('Unable to open CSV file "%s".', $path));
        }

        try {
            $lineNumber = 0;
            while (($columns = fgetcsv($file, null, ';', '"', '')) !== false) {
                ++$lineNumber;
                if ($columns === [null]) {
                    continue;
                }

                if ($lineNumber === 1 && isset($columns[0])) {
                    $columns[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $columns[0]) ?? (string) $columns[0];
                }

                yield $lineNumber => new ProjectCsvRow(
                    $lineNumber,
                    $this->column($columns, self::SAP_NUMBER_COLUMN),
                    $this->column($columns, self::STATUS_COLUMN),
                    $this->column($columns, self::PROJECT_NAME_COLUMN),
                    $this->column($columns, self::CUSTOMER_SAP_ID_COLUMN),
                    $this->column($columns, self::CUSTOMER_NAME_COLUMN),
                );
            }
        } finally {
            fclose($file);
        }
    }

    private function column(array $columns, int $oneBasedIndex): string
    {
        $index = $oneBasedIndex - 1;
        if (!array_key_exists($index, $columns)) {
            return '';
        }

        return trim((string) $columns[$index]);
    }
}
