<?php

namespace Abramenko\Experiments;

class Email
{
    public function __construct(private string $email)
    {
        $this->isValidEmail($email);
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    private function isValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '"%s" is not a valid email address',
                    $email
                )
            );
        }
    }
    public function __toString(): string
    {
        return $this->email;
    }
}
