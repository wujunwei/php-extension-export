<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 14:53
 */

namespace FirstW\Export;


class ConstLoader extends Loader
{
    const FILE_NAME = 'const.php';
    private $constArray;
    public function __construct($constArray)
    {
        $this->constArray = $constArray;
    }

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function dump($path = './')
    {
        $handle = fopen($path.self::FILE_NAME, 'w');
        if (!is_resource($handle)){
            throw new \Exception('can\'t open this file');
        }
        fwrite($handle, $this->startPHPFile());
        if (count($this->constArray) === 0){
            fwrite($handle, '// no const');
        }else{
            foreach ($this->constArray as $name => $value){
                fwrite($handle,$this->writeLine($name, $value));
            }
        }

        return fclose($handle);
    }

    private function writeLine($name, $value)
    {
        if(is_string($value)){
            return sprintf("define(\"%s\", \"%s\");\r\n", $name, $value);
        }else{
            if (is_bool($value)){
                $value = $value? 'true': 'false';
            }
            return sprintf("define(\"%s\", %s);\r\n", $name, $value);
        }

    }

}