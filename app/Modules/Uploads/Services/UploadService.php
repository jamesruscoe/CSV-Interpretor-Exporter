<?php
namespace App\Modules\Uploads\Services;

use Illuminate\Http\UploadedFile;
use Exception;

class UploadService
{
    public function processUploadedFile(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if (!file_exists($path)) {
            throw new Exception('File not found');
        }

        $records = array_map('str_getcsv', file($path));
        if (empty($records)) {
            throw new Exception('CSV file is empty');
        }

        array_shift($records); // Remove header
        $records = array_filter($records, fn($record) => !empty($record[0]));

        $people = [];
        foreach ($records as $record) {
            $nameString = trim($record[0]);
            if (!empty($nameString)) {
                $parsed = $this->processName($nameString);
                $people = array_merge($people, $parsed);
            }
        }

        return $people;
    }

    private function processName(string $nameString): array
    {
        // check to split if its muliple people in one entry
        if (preg_match('/\s+(?:and|&)\s+/i', $nameString)) {
            return $this->processMultiplePeople($nameString);
        }

        return [$this->processSingleName($nameString)];
    }

    private function processMultiplePeople(string $nameString): array
    {
        $people = [];
        $parts = preg_split('/\s+(?:and|&)\s+/i', $nameString);

        // shared last name case
        if (count($parts) === 2) {
            $firstPerson = $this->processSingleName($parts[0]);
            $secondPerson = $this->processSingleName($parts[1]);

            // If second person has no last name use first
            if (!$secondPerson['last_name'] && $firstPerson['last_name']) {
                $secondPerson['last_name'] = $firstPerson['last_name'];
            }

            $people[] = $firstPerson;
            $people[] = $secondPerson;
        } else {
            foreach ($parts as $part) {
                $people[] = $this->processSingleName($part);
            }
        }

        return array_filter($people);
    }

    private function processSingleName(string $nameString): array
    {
        $parts = preg_split('/\s+/', trim($nameString));

        $person = [
            'title' => null,
            'first_name' => null,
            'initial' => null,
            'last_name' => null
        ];

        // extract & normalize title
        if (!empty($parts)) {
            $person['title'] = $this->normalizeTitle($parts[0]);
            if ($person['title']) {
                array_shift($parts);
            }
        }

        if (empty($parts)) {
            return $person;
        }

        $lastPart = array_pop($parts);
        $person['last_name'] = $this->formatName($lastPart);

        if (!empty($parts)) {
            $nextPart = $parts[0];

            // check if its an initial or single letter with .
            if (preg_match('/^[A-Z]\.?$/i', $nextPart)) {
                $person['initial'] = strtoupper($nextPart[0]);
            } else {
                $person['first_name'] = $this->formatName($nextPart);
            }
        }

        return $person;
    }

    private function normalizeTitle(string $word): ?string
    {
        $word = rtrim($word, '.');

        // title variations
        $titlePatterns = [
            '/^(?:Mr|Mister)$/i' => 'Mr',
            '/^(?:Mrs|Misses)$/i' => 'Mrs',
            '/^(?:Ms|Miss)$/i' => 'Ms',
            '/^(?:Dr|Doctor)$/i' => 'Dr',
            '/^(?:Prof|Professor)$/i' => 'Prof',
        ];

        foreach ($titlePatterns as $pattern => $normalized) {
            if (preg_match($pattern, $word)) {
                return $normalized;
            }
        }

        // Special case: if it looks like a title but isn't in our patterns,
        // return it capitalized
        if (preg_match('/^[A-Z][a-z]+\.?$/i', $word)) {
            return ucfirst(strtolower($word));
        }

        return null;
    }

    private function formatName(string $name): string
    {
        // Handle hyphenated names
        if (strpos($name, '-') !== false) {
            return implode('-', array_map(
                fn($part) => ucfirst(strtolower($part)),
                explode('-', $name)
            ));
        }

        return ucfirst(strtolower($name));
    }
}
