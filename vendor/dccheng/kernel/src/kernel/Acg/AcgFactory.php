<?php
/**
 *  FileName: AcgFactory.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/14
 *  Time: 14:50
 */


namespace Kernel\Acg;

use Exception;

class AcgFactory
{
    public $acg;

    public function __construct(AcgInterface $acg)
    {
        $this->acg = $acg;
    }

    public function run($config)
    {
        $this->acg->run($config);
    }
}