<?php
/**
 * Created by PhpStorm.
 * User: mate
 * Date: 2019-03-18
 * Time: 21:14
 */

namespace App\Model;


class NewsItem
{

    /**
     * @var string $source
     */
    private $source;

    /**
     * @var string $sourceId
     */
    private $sourceId;

    /**
     * @var string $time
     */
    private $time;

    /**
     * @var string $text
     */
    private $text;

    public function __construct($source, $sourceId, $time, $text)
    {
        $this->setSource($source);
        $this->setSourceId($sourceId);
        $this->setTime($time);
        $this->setText($text);
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * @param string $sourceId
     */
    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime(string $time): void
    {
        if($time) {
            $this->time = date('Y-m-d H:i:s',strtotime($time));
        } else {
            $this->time = '';
        }
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }



}