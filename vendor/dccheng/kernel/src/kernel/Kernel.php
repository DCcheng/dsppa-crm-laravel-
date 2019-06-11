<?php
/**
 *  FileName: Kernel.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/11
 *  Time: 12:02
 */


namespace Kernel;

use Kernel\Container;

class Kernel
{
    public $container;

    public function __construct(Container $container,$arr = [])
    {
        $this->container = $container;
        $abstracts = $this->setClass();
        foreach ($arr as $key=>$value){
            $abstracts[$key] = $value;
        }
        foreach ($abstracts as $abstract => $concrete){
            $this->container->bind($abstract, $concrete);
            $this->$abstract = $this->container->make($abstract);
        }
    }

    public static function init($arr = [])
    {
        $kerenl = new Kernel(new Container(),$arr);
        return $kerenl;
    }

    public function setClass()
    {
        return [
            "token" => \Kernel\Ftoken\Token::class,
            "validation" => \Kernel\Validation\Validation::class,
            "curl" => \Kernel\Fcurl\Curl::class,
            "maps" => \Kernel\Maps\Maps::class,
            "serial" => \Kernel\Serial\Serial::class,
            "acg" => \Kernel\Acg\AcgLaravel5::class,
            "qrcode"=>\Kernel\Qrcode\QRcode::class
        ];
    }
}