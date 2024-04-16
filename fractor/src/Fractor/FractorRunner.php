<?php

namespace a9f\Fractor\Fractor;

use a9f\Fractor\Configuration\FractorConfig;
use a9f\Fractor\Contract\FileProcessor;
use a9f\Fractor\FileSystem\FileCollector;
use a9f\Fractor\FileSystem\FileFinder;
use a9f\Fractor\ValueObject\File;
use Nette\Utils\FileSystem;

/**
 * Main Fractor class. This takes care of collecting a list of files, iterating over them and calling all registered
 * processors for them.
 */
final readonly class FractorRunner
{
    /**
     * @param list<FileProcessor> $processors
     */
    public function __construct(private FileFinder $fileFinder, private readonly FileCollector $fileCollector, private iterable $processors)
    {
    }

    public function run(FractorConfig $config): void
    {
        if ($config->getPaths() === []) {
            throw new \RuntimeException('No directories given');
        }

        $files = $this->fileFinder->findFiles($config->getPaths(), $config->getFileExtensions());

        foreach ($files as $file) {
            foreach ($this->processors as $processor) {
                if (!$processor->canHandle($file)) {
                    continue;
                }

                $fractorFile = new File($file->getRealPath(), FileSystem::read($file->getRealPath()));
                $this->fileCollector->addFile($fractorFile);

                $processor->handle($fractorFile);
            }
        }
    }
}
