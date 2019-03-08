<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 15:35
 */

namespace FirstW\Export\Inner;


interface ClassLoaderInterface
{
    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     */
    static public function dump(\ReflectionClass $reflectionClass);
}