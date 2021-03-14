<?php

namespace Matriphe\Larinfo\Tests\Entities;

use Linfo\Linfo;
use Mockery;
use PHPUnit\Framework\TestCase;

abstract class LinfoEntityTestCase extends TestCase
{
    /**
     * @param  mixed $parser
     * @return Linfo
     */
    protected function setLinfo($parser): Linfo
    {
        return Mockery::mock(Linfo::class, [
            'getParser' => $parser,
        ]);
    }
}
