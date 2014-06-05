<?php

namespace MOG\Bundle\BadgeBundle\Model;

class BadgeConfig
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $picture;

    public function __construct($name = null, $picture = null)
    {
        $this->name = $name;
        $this->picture = $picture;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $picture
     * 
     * @return $this
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
