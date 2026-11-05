<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && (is_null($value) || trim((string)$value) === '')) {
                    $this->errors[$field][] = "Field {$field} wajib diisi.";
                }

                if ($rule === 'numeric' && !is_numeric($value)) {
                    $this->errors[$field][] = "Field {$field} harus berupa angka.";
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
