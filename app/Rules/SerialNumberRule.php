<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SerialNumberRule implements ValidationRule
{
    protected string $serialMask;

    public function __construct(string $serialMask)
    {
        $this->serialMask = $serialMask;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Логика проверки номера по маске
        if (!$this->matchesSerialMask($value, $this->serialMask)) {
            $fail("The $attribute format is invalid for the specified equipment type.");
        }
    }

    /**
     * Метод для проверки соответствия номера маске.
     *
     * @param  string  $serialNumber
     * @param  string  $mask
     * @return bool
     */
    protected function matchesSerialMask(string $serialNumber, string $mask): bool
    {
        // Пример простой проверки - замените на свою логику
        return preg_match("/$mask/", $serialNumber) === 1;
    }
}
