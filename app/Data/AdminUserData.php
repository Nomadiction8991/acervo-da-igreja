<?php

declare(strict_types=1);

namespace App\Data;

use InvalidArgumentException;

final readonly class AdminUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }

    /**
     * @param array{name?: mixed, email?: mixed, password?: mixed} $data
     */
    public static function fromArray(array $data): self
    {
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (! is_string($name) || trim($name) === '') {
            throw new InvalidArgumentException('Defina ADMIN_NAME, ADMIN_EMAIL e ADMIN_PASSWORD para gerar o usuario inicial.');
        }

        if (! is_string($email) || trim($email) === '') {
            throw new InvalidArgumentException('Defina ADMIN_NAME, ADMIN_EMAIL e ADMIN_PASSWORD para gerar o usuario inicial.');
        }

        if (! is_string($password) || $password === '') {
            throw new InvalidArgumentException('Defina ADMIN_NAME, ADMIN_EMAIL e ADMIN_PASSWORD para gerar o usuario inicial.');
        }

        return new self(
            name: trim($name),
            email: trim($email),
            password: $password,
        );
    }
}
