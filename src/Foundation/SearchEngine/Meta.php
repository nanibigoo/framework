<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, iBenchu.org
 * @datetime 2016-11-04 10:57
 */
namespace Notadd\Foundation\SearchEngine;

use Illuminate\Support\Collection;

/**
 * Class Meta.
 */
class Meta
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $keywords;

    /**
     * Meta constructor.
     */
    public function __construct()
    {
        $this->title = '{sitename}';
        $this->description = '{description}';
        $this->keywords = '{keywords}';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getData()
    {
        $data = new Collection();
        $data->put('title', $this->title);
        $data->put('description', $this->description);
        $data->put('keywords', $this->keywords);

        return $data;
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = trim($title);
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = trim($description);
    }

    /**
     * @param $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = trim($keywords);
    }
}