<?php
/**
 * Created by PhpStorm.
 * User: mate
 * Date: 2019-03-17
 * Time: 23:37
 */

namespace App\Repository;


use Abraham\TwitterOAuth\TwitterOAuth;
use App\Model\NewsItem;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Simple\FilesystemCache;

class TwitterReader
{
    protected $consumerKey = '';
    protected $consumerSecret = '';
    protected $clientToken = '';
    protected $clientTokenSecret = '';
    protected $cache = null;

    public function __construct($consumerKey, $consumerSecret, $clientToken=null, $clientTokenSecret=null)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret =  $consumerSecret;
        $this->clientToken = $clientToken;
        $this->clientTokenSecret = $clientTokenSecret;
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @param $name
     * @param int $num
     * @return array|NewsItem[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function read($name, $num=20) {
        // https://www.madebymagnitude.com/blog/displaying-latest-tweets-using-the-twitter-api-v11-in-php/
        $cacheKey = 'twitter-'.$name.'-'.$num;
        $cacheItem = $this->cache->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }
        try {
            $twitter = new TwitterOAuth($this->consumerKey, $this->consumerSecret, $this->clientToken, $this->clientTokenSecret);
            //$twitter->ssl_verifypeer = true;
            //$content = $twitter->get("account/verify_credentials");
            $tweets = $twitter->get('statuses/user_timeline', array('screen_name' => $name, 'exclude_replies' => 'true', 'include_rts' => 'false', 'count' => $num));
            $newsItems = [];
            foreach($tweets as $tweet) {
                $newsItems[] = new NewsItem($name,$tweet->id_str,$tweet->created_at,$tweet->text);
            }
            $cacheItem->set($newsItems);
            $cacheItem->expiresAfter(600); // sec
            $this->cache->save($cacheItem);
            return $newsItems;
        } catch(\Exception $ex) {
            // todo logging
            echo '<pre>';
            print_r($ex);
        }
        return null;
    }
}