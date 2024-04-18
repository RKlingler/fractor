<?php

declare(strict_types=1);

namespace a9f\Fractor\Tests\Helper\Rules;

use a9f\Fractor\Application\ValueObject\File;
use a9f\Fractor\Tests\Helper\Contract\TextRule;

final class ReplaceXXXTextRule implements TextRule
{
    public function apply(File $file): void
    {
        $newFileContent = str_replace('XXX', 'YYY', $file->getContent());

        $file->changeFileContent($newFileContent);
    }
}
