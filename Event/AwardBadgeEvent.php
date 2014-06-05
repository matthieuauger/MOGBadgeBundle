<?php

namespace MOG\Bundle\BadgeBundle\Event;

use MOG\Bundle\BadgeBundle\Model\BadgeableInterface;
use MOG\Bundle\BadgeBundle\Model\AwardBadgeEventInterface;

class AwardBadgeEvent extends BadgeEvent implements AwardBadgeEventInterface
{
    /**
     * @var \MOG\Bundle\BadgeBundle\Model\BadgeableInterface $target
     */
    private $badgeType;

    /**
     * @param BadgeableInterface $badgeable
     * @param string $badgeType
     */
    public function __construct(BadgeableInterface $badgeable, $badgeType)
    {
        parent::__construct($badgeable);
        $this->badgeType = $badgeType;
    }

    public function getBadgeType()
    {
        return $this->badgeType;
    }
}
