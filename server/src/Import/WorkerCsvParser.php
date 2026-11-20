<?php

declare(strict_types=1);

namespace App\Import;

use Generator;
use RuntimeException;

/** Reads UTF-8 semicolon-separated rows: email, first name, last name, initials. */
final class WorkerCsvParser
{
    /** @return Generator<int, WorkerCsvRow> */
    public function parseFile(string $path): Generator
    {
        $file = fopen($path, 'rb');
        if ($file === false) {
            throw new RuntimeException(sprintf('Unable to open CSV file "%s".', $path));
        }

        try {
            $line = 0;
            while (($columns = fgetcsv($file, null, ';', '"', '')) !== false) {
                ++$line;
                if ($columns === [null]) {
                    continue;
                }
                if ($line === 1 && isset($columns[0])) {
                    $columns[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $columns[0]) ?? (string) $columns[0];
                    if (mb_strtolower(trim((string) $columns[0])) === 'email') {
                        continue;
                    }
                }

                yield new WorkerCsvRow(
                    $line,
                    mb_strtolower(trim((string) ($columns[0] ?? ''))),
                    trim((string) ($columns[1] ?? '')),
                    trim((string) ($columns[2] ?? '')),
                    trim((string) ($columns[3] ?? '')),
                );
            }
        } finally {
            fclose($file);
        }
    }
}
