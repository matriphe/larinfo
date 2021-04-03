<?php

namespace Matriphe\Larinfo\Wrapper;

use Linfo\Linfo;
use Linfo\OS\OS;

class LinfoWrapper implements LinfoWrapperContract
{
    /**
     * @var Linfo
     */
    private $linfo;

    /**
     * @param Linfo $linfo
     */
    public function __construct(Linfo $linfo)
    {
        $this->linfo = $linfo;
    }

    /**
     * @return OS|null
     */
    public function getParser(): ?OS
    {
        $parser = $this->linfo->getParser();
        if ($parser instanceof OS) {
            return $parser;
        }

        return null;
    }
}
