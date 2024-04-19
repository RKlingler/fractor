<?php

declare(strict_types=1);

namespace a9f\Fractor\Skipper\Matcher;

use a9f\Fractor\Skipper\FileSystem\FnMatchPathNormalizer;
use a9f\Fractor\Skipper\FileSystem\PathNormalizer;
use a9f\Fractor\Skipper\Fnmatcher;
use a9f\Fractor\Skipper\RealpathMatcher;

final readonly class FileInfoMatcher
{
    public function __construct(
        private FnMatchPathNormalizer $fnMatchPathNormalizer,
        private Fnmatcher $fnmatcher,
        private RealpathMatcher $realpathMatcher
    ) {
    }

    /**
     * @param string[] $filePatterns
     */
    public function doesFileInfoMatchPatterns(string $filePath, array $filePatterns): bool
    {
        $filePath = PathNormalizer::normalize($filePath);
        foreach ($filePatterns as $filePattern) {
            $filePattern = PathNormalizer::normalize($filePattern);
            if ($this->doesFileMatchPattern($filePath, $filePattern)) {
                return true;
            }
        }

        return false;
    }

    private function doesFileMatchPattern(string $filePath, string $ignoredPath): bool
    {
        if ($filePath === $ignoredPath) {
            return true;
        }

        $ignoredPath = $this->fnMatchPathNormalizer->normalizeForFnmatch($ignoredPath);
        if ($ignoredPath === '') {
            return false;
        }

        if (str_starts_with($filePath, $ignoredPath)) {
            return true;
        }

        if (str_ends_with($filePath, $ignoredPath)) {
            return true;
        }

        if ($this->fnmatcher->match($ignoredPath, $filePath)) {
            return true;
        }

        return $this->realpathMatcher->match($ignoredPath, $filePath);
    }
}
