<?php

namespace MOG\Bundle\BadgeBundle\Model;

use Symfony\Component\EventDispatcher\Event;

interface BadgeDefinitionInterface
{
    /**
     * @param Event $event
     *
     * @return bool
     */
    public function supports(Event $event);

    /**
     * Determines if the badge is granted or not
     *
     * @param Event $event
     *
     * @return bool
     */
    public function isApplicable(Event $event);

    /**
     * Return the type of badge which should be granted
     */
    public function getBadgeName();

    /**
     * @param Event $event
     *
     * @return BadgeableInterface
     */
    public function getBadgeable(Event $event);
}
