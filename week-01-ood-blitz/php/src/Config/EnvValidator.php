<?php

declare(strict_types=1);

namespace App\Config;

use RuntimeException;

class EnvValidator
{
    /**
     * @param array<string> $requiredKeys
     */
    public static function validate(array $requiredKeys): void
    {
        $missing = [];

        foreach ($requiredKeys as $key) {
            $value = getenv($key);
            if ($value === false || trim($value) === '') {
                $missing[] = $key;
            }
        }

        if (!empty($missing)) {
            throw new RuntimeException(
                "Environment Configuration Error: Missing required variables: " . implode(', ', $missing)
            );
        }
    }
}