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
%s%s%s

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
        $interfaces = $this->reflectionClass->getInterfaces();
        $inters = $this->reflectionClass->getInterfaceNames();
        $parentInterfaces = [];
        foreach ($interfaces as $interface){
            $parentInterfaces = $parentInterfaces + $this->getAllInterface($interface);
        }
        $inters = array_diff($inters, $parentInterfaces);
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

    /**
     * @param \ReflectionClass $reflectionClass
     * @return string[]
     */
    private function getAllInterface(\ReflectionClass $reflectionClass)
    {
        $parent = $reflectionClass->getInterfaces();
        if (count($parent) === 0){
            return [$reflectionClass->getName()];
        }else{
            $result = [];
            foreach ($parent as $item) {
                $result += $this->getAllInterface($item);
            }
        }
        return $result;
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