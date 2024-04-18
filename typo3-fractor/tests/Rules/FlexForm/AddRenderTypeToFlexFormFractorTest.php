<?php

namespace a9f\Typo3Fractor\Tests\Rules\FlexForm;

use a9f\Fractor\Testing\PHPUnit\AbstractFractorTestCase;

final class AddRenderTypeToFlexFormFractorTest extends AbstractFractorTestCase
{
    public function test(): void
    {
        $this->doTest();
    }

    protected function additionalConfigurationFiles(): array
    {
        return [
            __DIR__ . '/config/config.php',
        ];
    }
}
