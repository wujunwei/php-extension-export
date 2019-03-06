<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 15:33
 */

namespace FirstW\Export;


abstract class Loader
{
    /**
     * @param string $path
     * @return bool
     */
    abstract public function dump($path);

    protected function startPHPFile()
    {
        return <<<doc
<?php

doc;
    }
}