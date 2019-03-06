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
     * @return string
     */
    public function dump();
    public function writeLine();
}