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
                fwrite($handle,$this->writeLine($function->getNamespaceName(), $param));
            }
        }

        return fclose($handle);
    }

    private function writeLine($name, $value)
    {
        return sprintf($this->function_template, $name,$value);
    }

    /**
     * @param \ReflectionParameter[] $reflection
     * @return string
     */
    private function handleParameters($reflection = [])
    {//todo
        return ;
    }
}