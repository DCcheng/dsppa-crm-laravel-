<?php
/**
 *  FileName: Kernel.php
 *  Description :
 *  Author: DC
 *  Date: 2019/6/11
 *  Time: 12:02
 *
 * You must use 'Kernel::init()' in the project bootstrap to initialize this program
 * If you have not initialize this program , you will get a error message "Trying to get property of non-object"
 * 
 */


namespace Kernel;

use Kernel\Container;

class Kernel
{
    public $container;

    public static $app = null;

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

//    public function __get($name)
//    {
//        if(is_null(self::$app)){
//           throw new Exception("Please initialize the kernel program and you can use 'Kernel::init()' to initialize the program");
//        }
//        return $this->$name;
//    }

    public static function init($arr = [])
    {
        if(is_null(self::$app)) {
            self::$app = new Kernel(new Container(), $arr);
        }
        return self::$app;
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