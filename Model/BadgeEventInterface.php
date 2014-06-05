<?php

namespace MOG\Bundle\BadgeBundle\Model;

/**
 * All the events which will be managed by the BadgeAttributionSubscriber have to implements this interface
 */
interface BadgeEventInterface
{
    /**
     * @return BadgeableInterface
     */
    public function getBadgeable();

    /**
     * @return string
     */
    public function getName();
}
