<?php

namespace Matriphe\Larinfo\Entities;

use Linfo\Linfo;
use Linfo\OS\OS;

abstract class LinfoEntity
{
    /**
     * @var OS|null
     */
    protected ?OS $linfo;

    /**
     * ServerInfo constructor.
     * @param Linfo $linfo
     */
    public function __construct(Linfo $linfo)
    {
        $this->linfo = $this->parse($linfo);
    }

    /**
     * @param  Linfo   $linfo
     * @return OS|null
     */
    protected function parse(Linfo $linfo): ?OS
    {
        $parser = $linfo->getParser();
        if ($parser === null || ! $parser instanceof OS) {
            return null;
        }

        return $parser;
    }
}
