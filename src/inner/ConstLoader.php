<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 15:00
 */

namespace FirstW\Export\Inner;


class ConstLoader implements ClassLoaderInterface
{

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     */
    static public function dump(\ReflectionClass $reflectionClass)
    {
        $result = '';
        if (count($reflectionClass->getConstants()) > 0){
            foreach ($reflectionClass->getConstants() as $name =>  $constant){
                if(is_string($constant)){
                    $result .= sprintf("    const %s = \"%s\";\n", $name, $constant);
                }else{
                    $result .= sprintf("    const %s = %s;\n", $name, $constant);
                }
            }
        }
        return $result;
    }
}