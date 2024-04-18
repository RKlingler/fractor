<?php

declare(strict_types=1);

namespace a9f\Fractor\Application\ValueObject;

final class File
{
    private bool $hasChanged = false;
    private readonly string $originalContent;
    private string $directoryName;
    private string $fileName;
    private string $fileExtension;

    public function __construct(private readonly string $filePath, private string $content)
    {
        $this->originalContent = $this->content;
        $this->directoryName = dirname($this->filePath);
        $this->fileName = basename($this->filePath);
        $this->fileExtension = pathinfo($this->fileName, PATHINFO_EXTENSION);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getDirectoryName(): string
    {
        return $this->directoryName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    public function changeFileContent(string $newFileContent): void
    {
        if ($this->content === $newFileContent) {
            return;
        }

        $this->content = $newFileContent;
        $this->hasChanged = true;
    }

    public function hasChanged(): bool
    {
        return $this->hasChanged;
    }

    public function getOriginalContent(): string
    {
        return $this->originalContent;
    }
}
