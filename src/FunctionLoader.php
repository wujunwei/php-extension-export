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
    const FILE_NAME = '/functions.php';

    private $function_template = <<<tem
/**
 * 
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
                $param = $this->handleParameters($function->getParameters());
                $returnType = $function->getReturnType() ?: 'mix';
                fwrite($handle,$this->writeLine($returnType, $function->getName(), $param));
            }
        }

        return fclose($handle);
    }

    private function writeLine()
    {
        $args = func_get_args();
        return sprintf($this->function_template, ...$args);
    }

    /**
     * @param \ReflectionParameter[] $reflections
     * @return string
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
     */
    private function __handleParameter(\ReflectionParameter $reflection)
    {

        if ($reflection->isVariadic()){
            return '...'.$reflection->getName();
        }
        if ($reflection->hasType()){
            $result = $reflection->hasType().' ';
        }else if (!is_null($reflection->getClass())){
            $result = $reflection->getClass()->getName().' ';
        }else{
            $result = '';
        }

        if ($reflection->isPassedByReference()){
            $result .= '&';
        }
        $result .= $reflection->getName();

        if ($reflection->isOptional() && $reflection->isDefaultValueAvailable()){
            $result .= '='.$reflection->getDefaultValue();
        }
        return $result;
    }
}