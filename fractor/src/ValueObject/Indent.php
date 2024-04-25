<?php

declare(strict_types=1);

namespace a9f\Fractor\ValueObject;

use a9f\Fractor\Application\ValueObject\File;
use Webmozart\Assert\Assert;

/**
 * @see https://github.com/ergebnis/json-normalizer/blob/main/src/Format/Indent.php
 */
final readonly class Indent
{
    /**
     * @var array<string, string>
     */
    public const CHARACTERS = [
        'space' => ' ',
        'tab' => "\t",
    ];

    private function __construct(private string $value)
    {
    }

    public static function fromFile(File $file): self
    {
        if (\preg_match('#^(?P<indent>( +|\t+)).*#m', (string) $file->getContent(), $match) === 1) {
            return self::fromString($match['indent']);
        }

        return self::fromSizeAndStyle(4, 'space');
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function isSpace(): bool
    {
        return \preg_match('#^( +).*#', $this->value) === 1;
    }

    public function length(): int
    {
        return strlen($this->value);
    }

    private static function fromSizeAndStyle(int $size, string $style): self
    {
        Assert::greaterThanEq($size, 1);
        Assert::keyExists(self::CHARACTERS, $style);

        $value = \str_repeat(self::CHARACTERS[$style], $size);

        return new self($value);
    }

    private static function fromString(string $value): self
    {
        Assert::regex($value, '/^( *|\t+)$/');

        return new self($value);
    }
}
