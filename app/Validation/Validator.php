<?php

declare(strict_types=1);

namespace App\Validation;

/**
 * Rule strings: 'required|max:100', 'nullable|email', 'in:a,b', 'int|min_value:1|max_value:5',
 * 'phone', 'slug', 'date', 'accepted', 'min:8'.
 * Returns field => first error message.
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @param array<string, string> $labels
     */
    public function __construct(private readonly array $data, private readonly array $rules, private readonly array $labels = [])
    {
        $this->run();
    }

    public static function make(array $data, array $rules, array $labels = []): self
    {
        return new self($data, $rules, $labels);
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? '';
            $value = is_string($value) ? trim($value) : $value;
            $rules = explode('|', $ruleString);
            $label = $this->labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            $empty = $value === '' || $value === null || $value === [];

            if ($empty) {
                if (in_array('required', $rules, true)) {
                    $this->errors[$field] = "{$label} is required.";
                } elseif (in_array('accepted', $rules, true)) {
                    $this->errors[$field] = "Please confirm {$label}.";
                }
                continue;
            }
            if (!is_string($value)) {
                $this->errors[$field] = "{$label} is invalid.";
                continue;
            }

            foreach ($rules as $rule) {
                [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
                $error = $this->check($name, $arg, $value, $label);
                if ($error !== null) {
                    $this->errors[$field] = $error;
                    break;
                }
            }
        }
    }

    private function check(string $rule, ?string $arg, string $value, string $label): ?string
    {
        return match ($rule) {
            'required', 'nullable' => null,
            'max' => mb_strlen($value) > (int) $arg ? "{$label} must be {$arg} characters or fewer." : null,
            'min' => mb_strlen($value) < (int) $arg ? "{$label} must be at least {$arg} characters." : null,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) === false ? "Enter a valid email address." : null,
            'int' => filter_var($value, FILTER_VALIDATE_INT) === false ? "{$label} must be a whole number." : null,
            'min_value' => (int) $value < (int) $arg ? "{$label} must be at least {$arg}." : null,
            'max_value' => (int) $value > (int) $arg ? "{$label} must be at most {$arg}." : null,
            'in' => !in_array($value, explode(',', (string) $arg), true) ? "Choose a valid {$label}." : null,
            // UAE mobile/landline or international: digits, spaces, +, -, (), 7–15 digits.
            'phone' => (!preg_match('/^\+?[0-9\s\-()]{7,20}$/', $value) || strlen((string) preg_replace('/\D/', '', $value)) < 7 || strlen((string) preg_replace('/\D/', '', $value)) > 15)
                ? 'Enter a valid phone number, e.g. 050 123 4567.' : null,
            'slug' => !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value) ? "{$label} may contain only lowercase letters, numbers and hyphens." : null,
            'date' => (\DateTime::createFromFormat('Y-m-d', $value) === false) ? "{$label} must be a valid date." : null,
            'url' => filter_var($value, FILTER_VALIDATE_URL) === false || !preg_match('#^https?://#i', $value) ? "{$label} must be a valid URL." : null,
            'accepted' => !in_array($value, ['1', 'on', 'yes'], true) ? "Please confirm {$label}." : null,
            'no_links' => preg_match('#https?://|www\.#i', $value) ? "{$label} cannot contain links." : null,
            default => throw new \InvalidArgumentException("Unknown validation rule: {$rule}"),
        };
    }
}
