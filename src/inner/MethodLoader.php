<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 14:59
 */

namespace FirstW\Export\Inner;


class MethodLoader implements ClassLoaderInterface
{
    static private $methodTemplate = <<<tem

    /**
     * %s
     * @return %s
     */
    %s function %s(%s){}

tem;

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string
     * @throws \ReflectionException
     */
    static public function dump(\ReflectionClass $reflectionClass)
    {
        $result = '';
        foreach ($reflectionClass->getMethods() as  $function){
            $doc = self::handleDoc($function->getParameters());
            $param = self::handleParameters($function->getParameters());
            $returnType = $function->getReturnType() ?: 'mixed';
            $result .= sprintf(self::$methodTemplate, $doc, $returnType, self::handleVisible($function), $function->getShortName(), $param);
        }

        return $result;
    }

    /**
     * @param \ReflectionParameter[] $parameters
     * @return string
     */
    static private function handleDoc(array $parameters)
    {
        if (count($parameters) === 0){
            return '';
        }
        $result = '';
        foreach ($parameters as $reflection){
            $result .='@param ';
            if ($reflection->hasType()){
                $type = $reflection->getType();
                if ($type->isBuiltin() || $reflection->getClass()=== null){
                    $result .= $type.' ';
                }else {
                    $result .= '\\'.$reflection->getClass()->getName().' ';
                }
            }else{
                $result .= '';
            }
            $result .= '$'.$reflection->getName()."\n     * ";
        }
        return rtrim($result, "\n* ");
    }

    /**
     * @param \ReflectionParameter $reflection
     * @return string
     * @throws \ReflectionException
     */
    static private function __handleParameter(\ReflectionParameter $reflection)
    {

        if ($reflection->isVariadic()){
            return '...$'.$reflection->getName();
        }
        if ($reflection->hasType()){
            $type = $reflection->getType();
            if ($type->isBuiltin() || $reflection->getClass()=== null){
                $result = $type.' ';
            }else {
                $result = '\\'.$reflection->getClass()->getName().' ';
            }
        }else{
            $result = '';
        }

        if ($reflection->isPassedByReference()){
            $result .= '&';
        }
        $result .= '$'.$reflection->getName();

        if ($reflection->isOptional() && $reflection->isDefaultValueAvailable()){
            $value = $reflection->getDefaultValue();
            if (is_string($value)){
                $result .= "='{$value}'";
            }else{
                $result .= "={$value}";
            }

        }
        return $result;
    }

    /**
     * @param \ReflectionParameter[] $reflections
     * @return string
     * @throws \ReflectionException
     */
    static private function handleParameters($reflections = [])
    {
        $result = '';
        foreach ($reflections as $reflection){
            $result .= self::__handleParameter($reflection).', ';
        }
        return rtrim($result, ', ');
    }

    static private function handleVisible(\ReflectionMethod $method)
    {
        $result = '';
        if ($method->isStatic()){
            $result .= 'static ';
        }
        $result .= $method->isPublic() ? 'public ': ($method->isProtected() ? 'protected ': 'private ');
        return $result;
    }
}