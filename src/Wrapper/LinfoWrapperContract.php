<?php

namespace Matriphe\Larinfo\Wrapper;

use Linfo\OS\OS;

interface LinfoWrapperContract
{
    /**
     * Wrap Linfo and return the parser to handle FatalException.
     * @return OS|null
     */
    public function getParser(): ?OS;
}
