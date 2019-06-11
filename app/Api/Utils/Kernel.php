<?php
/**
 *  FileName: Kernel.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/11
 *  Time: 15:27
 */


namespace App\Api\Utils;
use Kernel\Kernel as KernelDC;

trait Kernel
{
    public $kernel;
    public function __construct()
    {
        $this->kernel = KernelDC::init();
    }
}