<?php
/**
 * Created by PhpStorm.
 * User: mate
 * Date: 2019-03-18
 * Time: 22:39
 */

namespace App\Helper;


class Mixer
{

    /**
     * @param array $lista1
     * @param array $lista2
     * @param array|int[] $mixPattern
     *
     * beszurja a lista2 elemeit a lista1-be a mintazatban megadott mappeles szerint
     */
    public static function mix(&$lista1, &$lista2, $mixPattern)
    {
        foreach ($mixPattern as $mixIndex => $mixKey) {
            if(count($lista1)>=$mixKey && isset($lista2[$mixIndex])) {
                array_splice($lista1, $mixKey, 0, [$lista2[$mixIndex]]);
            }
        }
    }
}