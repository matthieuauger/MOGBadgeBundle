<?php

namespace MOG\Bundle\BadgeBundle\Event;

use MOG\Bundle\BadgeBundle\Model\BadgeableInterface;
use MOG\Bundle\BadgeBundle\Model\BadgeEventInterface;
use Symfony\Component\EventDispatcher\Event;

class BadgeEvent extends Event implements BadgeEventInterface
{
    /**
     * @var \MOG\Bundle\BadgeBundle\Model\BadgeableInterface
     */
    private $badgeable;

    /**
     * @param BadgeableInterface $badgeable
     */
    public function __construct(BadgeableInterface $badgeable)
    {
        $this->badgeable = $badgeable;
    }

    /**
     * @return BadgeableInterface
     */
    public function getBadgeable()
    {
        return $this->badgeable;
    }
}
