<?php

namespace MOG\Bundle\BadgeBundle\Model;

interface BadgeDefinitionInterface
{
    /**
     * Determines if the badge is granted or not
     *
     * @param $subject
     *
     * @return bool
     */
    public function isApplicable($subject);

    /**
     * Return the type of badge which should be granted
     */
    public function getRelatedBadge();
}
