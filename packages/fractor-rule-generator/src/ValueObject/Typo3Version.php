<?php

declare(strict_types=1);

namespace a9f\FractorRuleGenerator\ValueObject;

final readonly class Typo3Version
{
    private function __construct(
        private int $major,
        private int $minor
    ) {
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public static function createFromString(string $version): self
    {
        if (! str_contains($version, '.')) {
            $version .= '.0';
        }

        [$major, $minor] = explode('.', $version, 2);

        return new self((int) $major, (int) $minor);
    }

    public function getFullVersion(): string
    {
        return sprintf('%d%d', $this->major, $this->minor);
    }
}
