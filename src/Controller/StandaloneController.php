<?php
/**
 * Created by PhpStorm.
 * User: mate
 * Date: 2019-03-17
 * Time: 09:59
 */

namespace App\Controller;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Helper\Mixer;
use App\Model\NewsItem;
use App\Repository\IcndbReader;
use App\Repository\TwitterReader;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StandaloneController extends AbstractController
{

    /**
     * matches API
     *
     * @Route("/{handle1}/{handle2}/{mod}", name="one_and_only_api")
     */
    public function api($handle1, $handle2, $mod='fib')
    {


        // todo, ennek az init resznek DI-containerben vagy serviceLocatorban a helye
        $icndb = new IcndbReader();

        if(!isset($_ENV['TWITTER_KEY'])) {
            throw new \Exception('missing TWITTER_KEY param from .env.local');
        }
        if(!isset($_ENV['TWITTER_SECRET'])) {
            throw new \Exception('missing TWITTER_SECRET param from .env.local');
        }

        $twitter = new TwitterReader($_ENV['TWITTER_KEY'],$_ENV['TWITTER_SECRET']);
        $handleWhitelist = ['knplabs','symfony'];
        $modWhitelist = ['mod','fib'];
        $mixingPatterns = [];
        for($i=2;$i<100;$i+=3) {
            $mixingPatterns['mod'][] = $i;
        }
        //$mixingPatterns['fib'] = [ 3, 5, 8, 13, 21, 34, 55, 89, 144]; // 1-el el kell tolni
        $mixingPatterns['fib'] = [ 2, 4, 7, 12, 20, 33, 54, 88, 143]; // 20-ig eleg



        if(!in_array($handle1,$handleWhitelist)) {
            throw new \Exception('ervenytelen forras: '.$handle1);
        }
        if(!in_array($handle2,$handleWhitelist)) {
            throw new \Exception('ervenytelen forras: '.$handle2);
        }
        if(!in_array($mod,$modWhitelist)) {
            throw new \Exception('ervenytelen mod: '.$mod);
        }
        if($handle1==$handle2) {
            throw new \Exception('a forrasok nem egyezhetnek');
        }



        $icndbArray = $icndb->read(5);
        $twitterArray1 = $twitter->read($handle1, 20);
        $twitterArray2 = $twitter->read($handle2, 20);
        $twitterArrayAllSorting = [];
        foreach($twitterArray1 as $newsItem) {
            $key = $newsItem->getTime().'-'.$newsItem->getSource().'-'.$newsItem->getSourceId();
            $twitterArrayAllSorting[$key] = $newsItem;
        }
        foreach($twitterArray2 as $newsItem) {
            $key = $newsItem->getTime().'-'.$newsItem->getSource().'-'.$newsItem->getSourceId();
            $twitterArrayAllSorting[$key] = $newsItem;
        }
        krsort($twitterArrayAllSorting);
        $twitterArray = array_values($twitterArrayAllSorting);


        //mixing
        Mixer::mix($twitterArray,$icndbArray,$mixingPatterns[$mod]);


        /**
         * @var NewsItem[] $twitterArray
         */


        echo '<table border="1">';
        foreach($twitterArray as $index => $item) {
            echo '<tr>';
            echo '<td>'.$index.'</td>';
            echo '<td>'.$item->getSource().'</td>';
            echo '<td>'.$item->getTime().'</td>';
            echo '<td>'.$item->getText().'</td>';
            echo '</tr>';
        }
        echo '</table>';


        $resultTxt = '';
        //$resultTxt = '<hr><pre>'.print_r($twitterArray, 1);
        //$resultTxt = $handle1.'<hr>'.$handle2.'<hr>'.$mod.'<hr><pre>'.print_r($icndbArray, 1).'<hr>'.print_r($twitterArray1, 1);
        return new Response($resultTxt);
    }

    /**
     * matches everithing else
     *
     * @Route("/", name="default_page")
     */
    public function defaultPage()
    {
        $message = 'usage handle1/handle2/[mod]';
        return new Response($message);
    }
}