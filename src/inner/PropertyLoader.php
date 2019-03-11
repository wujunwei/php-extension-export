<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 15:00
 */

namespace FirstW\Export\Inner;


class PropertyLoader implements ClassLoaderInterface
{

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     */
    static public function dump(\ReflectionClass $reflectionClass)
    {
        $result = '    ';
        foreach ($reflectionClass->getProperties() as $property){
            if ($property->isStatic()){
                $result .= 'static ';
            }
            $result .= $property->isPublic() ? 'public ': ($property->isProtected() ? 'protected ': 'private ');
            $result .= '$'.$property->getName().";\n    ";
        }

        return rtrim($result, ' ');
    }
}