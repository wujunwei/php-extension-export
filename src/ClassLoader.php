<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 14:53
 */

namespace FirstW\Export;


use FirstW\Export\Inner\ConstLoader;
use FirstW\Export\Inner\MethodLoader;
use FirstW\Export\Inner\PropertyLoader;

class ClassLoader extends Loader
{

    private $class_template = <<<tem
class %s %s
{
%s
%s
%s

%s

}
tem;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    public function __construct(\ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function dump($path)
    {
        $handle = fopen($path.$this->reflectionClass->getShortName().'.php', 'w');
        if (!is_resource($handle)){
            throw new \Exception('can\'t open this file');
        }
        fwrite($handle, $this->startPHPFile());
        fwrite($handle, $this->handleNamespace());
        $dependency = $this->handleDependency();
        fwrite($handle, sprintf($this->class_template, $this->reflectionClass->getShortName(), $dependency,$this->handleTrait(), ConstLoader::dump($this->reflectionClass), PropertyLoader::dump($this->reflectionClass), MethodLoader::dump($this->reflectionClass)));
        return fclose($handle);
    }

    protected function handleNamespace()
    {
        $namespace = $this->reflectionClass->getNamespaceName();
        if ($namespace !== ''){
            return "\nnamespace {$namespace};\n";
        }else{
            return $namespace;
        }
    }

    protected function handleDependency()
    {
        $inters = $this->reflectionClass->getInterfaceNames();
        $result = '';
        $subClass = $this->reflectionClass->getParentClass();
        if ($subClass !== false){
            $result .= ' extends \\'.$subClass->getName();
        }
        if (count($inters) > 0){
            $result .= ' implements ';
            foreach ($inters as $inter){
                $result .= '\\'.$inter.', ';
            }
        }
       return rtrim($result, ', ');
    }

    protected function handleTrait()
    {
        $result = '';
        $traits = $this->reflectionClass->getTraitNames();
        if (count($traits) > 0){
            $result .= "    use {$traits};\n";
        }

        return $result;
    }
}