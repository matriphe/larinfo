<?php

namespace Matriphe\Larinfo\Entities;

use Linfo\OS\OS;
use Matriphe\Larinfo\Wrapper\LinfoWrapperContract;

abstract class LinfoEntity
{
    /**
     * @var OS|null
     */
    protected ?OS $linfo;

    /**
     * ServerInfo constructor.
     * @param LinfoWrapperContract $linfo
     */
    public function __construct(LinfoWrapperContract $linfo)
    {
        $this->linfo = $this->parse($linfo);
    }

    /**
     * @param  LinfoWrapperContract $linfo
     * @return OS|null
     */
    protected function parse(LinfoWrapperContract $linfo): ?OS
    {
        return $linfo->getParser();
    }
}
