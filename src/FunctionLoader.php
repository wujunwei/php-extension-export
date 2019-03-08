<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 14:53
 */

namespace FirstW\Export;


class FunctionLoader extends Loader
{
    const FILE_NAME = 'functions.php';
    private $lastNamespace = '';
    private $function_template = <<<tem

/**
 * %s
 * @return %s
 */
function %s(%s){}

tem;
    /**
     * @var array|\ReflectionFunction[]
     */
    private $functions;

    /**
     * FunctionLoader constructor.
     * @param \ReflectionFunction[] $functions
     */
    public function __construct($functions =[])
    {
        $this->functions = $functions;
    }


    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function dump($path)
    {
        $handle = fopen($path.self::FILE_NAME, 'w');
        if (!is_resource($handle)){
            throw new \Exception('can\'t open this file');
        }
        fwrite($handle, $this->startPHPFile());

        if (count($this->functions) === 0){
            fwrite($handle, '// no functions');
        }else{
            foreach ($this->functions as  $function){
                fwrite($handle, $this->handleNamespace($function));
                $doc = $this->handleDoc($function->getParameters());
                $param = $this->handleParameters($function->getParameters());
                $returnType = $function->getReturnType() ?: 'mixed';
                fwrite($handle,$this->writeLine($doc, $returnType, $function->getShortName(), $param));
            }
        }

        return fclose($handle);
    }

    /**
     * @param \ReflectionFunction $function
     * @return string
     */
    private function handleNamespace(\ReflectionFunction $function)
    {
        $namespace = $function->getNamespaceName();
        if ($namespace !== ''){
            if ($this->lastNamespace != $namespace){
                $this->lastNamespace = $namespace;
                return "\nnamespace {$namespace};\n";
            }else{
                return "";
            }
        }else{
            return $namespace;
        }
    }

    private function writeLine()
    {
        $args = func_get_args();
        return sprintf($this->function_template, ...$args);
    }

    /**
     * @param \ReflectionParameter[] $reflections
     * @return string
     * @throws \ReflectionException
     */
    private function handleParameters($reflections = [])
    {
        $result = '';
        foreach ($reflections as $reflection){
            $result .= $this->__handleParameter($reflection).', ';
        }
        return rtrim($result, ', ');
    }

    /**
     * @param \ReflectionParameter $reflection
     * @return string
     * @throws \ReflectionException
     */
    private function __handleParameter(\ReflectionParameter $reflection)
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
     * @param \ReflectionParameter[] $parameters
     * @return string
     */
    private function handleDoc(array $parameters)
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
            $result .= '$'.$reflection->getName()."\n * ";
        }
        return rtrim($result, "\n* ");
    }
}