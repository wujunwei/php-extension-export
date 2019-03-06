<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 14:48
 */

namespace FirstW\Export;


use ReflectionExtension;

class Extension
{

    private function __construct()
    {
    }

    /**
     * @param string $name
     * @param string $path
     * @throws \Exception
     */
    static public function dump($name = '', $path = './')
    {
        if (!extension_loaded($name)){
            throw new \Exception(printf('%s not loaded !', $name));
        }
        $extension = new ReflectionExtension($name);

        //const
        (new ConstLoader($extension->getConstants()))->dump($path);

        //functions
        (new FunctionLoader($extension->getFunctions()))->dump($path);
    }
}