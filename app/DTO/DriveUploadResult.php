<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class DriveUploadResult
{
    public function __construct(
        public string $fileId,
        public ?string $webViewLink,
    ) {
    }
}
