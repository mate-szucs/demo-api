<?php
/**
 * Created by PhpStorm.
 * User: mate
 * Date: 2019-03-20
 * Time: 01:31
 */

use App\Helper\Mixer;

class MixerTest extends \Codeception\Test\Unit
{
    public function testMix() {
        $expect='0,1,a,2,3,b,4,c,5,6,7,d,e,8,9,0,1,2,3,4,5,6,7,8,9,0,f';
        $lista1='0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9,0'; //3 4 5 6
        $lista2='a,b,c,d,e,f';
        $mixPattern = [0=>2,1=>5,2=>7,3=>11,4=>12,5=>26];
        //
        $arr1=explode(',', $lista1);
        $arr2=explode(',', $lista2);
        Mixer::mix($arr1, $arr2, $mixPattern);
        $result = implode(',', $arr1);
        $this->assertEquals($expect,$result,'msg mmm');
    }
}
