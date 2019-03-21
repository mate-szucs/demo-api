<?php
/**
 * Created by PhpStorm.
 * User: mate
 * Date: 2019-03-17
 * Time: 23:40
 */

namespace App\Repository;


use App\Model\NewsItem;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class IcndbReader
{

    protected $cache = null;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @param int $num
     * @return array|NewsItem[]
     */
    public function read($num=10) {
        // cache randomizalas
        // rovid tavon nem lesz egyenletes az eloszlas
        $cacheKey = 'icndb-'.$num.'-'.rand(1,9);
        $cacheItem = $this->cache->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }
        $name = 'icndb';
        $url = 'http://api.icndb.com/jokes/random/'.$num;
        try {
            $json = file_get_contents($url);
            $jokes =json_decode($json);
            $newsItems = [];
            foreach($jokes->value as $joke) {
                $newsItems[] = new NewsItem($name,$joke->id,'',$joke->joke);
            }
            $cacheItem->set($newsItems);
            $cacheItem->expiresAfter(600); // sec
            $this->cache->save($cacheItem);
            return $newsItems;
        } catch(\Exception $ex) {
            // todo logging
        }
        return null;
    }
}