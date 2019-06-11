<?php
/**
 *  FileName: AcgInterface.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/11
 *  Time: 11:52
 */


namespace Kernel\Acg;


interface AcgInterface
{
    public function run($config);
    public function setTpl($namespace,$classname,$tpl,$callback);
    public function getPath($namespace);
}