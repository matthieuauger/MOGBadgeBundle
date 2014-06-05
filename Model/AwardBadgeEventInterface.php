<?php

namespace MOG\Bundle\BadgeBundle\Model;

interface AwardBadgeEventInterface extends BadgeEventInterface
{
    /**
     * @return string
     */
    public function getBadgeType();
}
