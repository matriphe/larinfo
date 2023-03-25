<?php

namespace Matriphe\Larinfo\Tests\unit\Entities;

use Matriphe\Larinfo\Wrapper\LinfoWrapperContract;
use Mockery;
use PHPUnit\Framework\TestCase;

abstract class LinfoEntityTestCase extends TestCase
{
    /**
     * @param  mixed                $parser
     * @return LinfoWrapperContract
     */
    protected function setLinfo($parser): LinfoWrapperContract
    {
        return Mockery::mock(LinfoWrapperContract::class, [
            'getParser' => $parser,
        ]);
    }
}
