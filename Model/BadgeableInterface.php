<?php

namespace MOG\Bundle\BadgeBundle\Model;

use MOG\Bundle\BadgeBundle\Entity\Badge;

interface BadgeableInterface
{
    /**
     * @param Badge $badge
     *
     * @return $this
     */
    public function addBadge(Badge $badge);

    /**
     * @param Badge $badge
     */
    public function removeBadge(Badge $badge);

    /**
     * @return array
     */
    public function getBadges();
}
