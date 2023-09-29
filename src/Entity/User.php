<?php

declare(strict_types=1);

namespace Alura\Mvc\Entity;

class User
{
    public readonly int $id;
    public readonly string $email;
    public readonly string $password;

    public function __construct(string $email, string $password) 
    {
        $this->setEmail($email);
        $this->setPassword($password);
    }

    private function setEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException();
        }

        $this->email = $email;
    }

    public function setUserId(int $id): void
    {
        $this->id = $id;
    }

    public function setPassword($password): void
    {
        $this->password = filter_var($password);
    }
}
