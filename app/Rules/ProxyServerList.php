<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ProxyServerList implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lines = explode("\n", $value);
        $linesCount = count($lines);
        if ($linesCount > 500) {
            $fail('service can handle up to 500 proxy servers at once, you passed ' . $linesCount);
        }

        foreach ($lines as $line) {
            $validationResult = $this->validateServer($line);
            if ($validationResult !== true) {
                $fail($validationResult); // Validation failed
            }
        }
    }

    private function validateServer($server): string | bool
    {
        $parts = explode(':', trim($server));
        if (count($parts) !== 2) {
            return "Invalid server format: $server should contain only one colon";
        }

        // Check if the IP and port are valid
        $ip = $parts[0];
        $port = $parts[1];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return "Invalid IP: ip:$ip in $server is invalid"; // Invalid IP
        }
        if (!is_numeric($port) || $port < 1 || $port > 65535) {
            return "Invalid Port: port:$port in $server is invalid"; // Invalid port
        }

        return true; // Valid server format
    }
}
