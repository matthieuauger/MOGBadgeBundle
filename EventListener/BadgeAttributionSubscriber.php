<?php

namespace MOG\Bundle\BadgeBundle\EventListener;

use MOG\Bundle\BadgeBundle\Entity\Badge;
use MOG\Bundle\BadgeBundle\Model\BadgeableInterface;
use MOG\Bundle\BadgeBundle\Model\BadgeEventInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BadgeAttributionSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $badgeDefinitions = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * The subscribed events are dynamically assigned with the tag maresidence_badge.definition
     * see MOG\Bundle\BadgeBundle\DependencyInjection\Compiler\RegisterBadgeDefinitionsPass
     */
    public static function getSubscribedEvents()
    {
        return array();
    }

    /**
     * @param $event
     */
    public function updateBadges($event)
    {
        if (!$event instanceof BadgeEventInterface) {
            throw new \Exception(sprintf('The event "%s" does not implement \MOG\Bundle\BadgeBundle\Model\BadgeEventInterface', get_class($event)));
        }

        $badgeable = $event->getBadgeable();

        if (!$badgeable instanceof BadgeableInterface) {
            return;
        }

        /* A badge definition is bound to an event. When the event is dispatched, we loop into each of theses definitions
         * to see if the badge is earned or not
         */
        foreach ($this->badgeDefinitions[$event->getName()] as $definitionId) {
            $definition = $this->container->get($definitionId);
            $relatedBadge = $definition->getRelatedBadge();

            /* Does the user already have this badge ? */
            $isBadgeAlreadyEarned = false;
            foreach ($badgeable->getBadges() as $alreadyEarnedBadge) {
                if ($alreadyEarnedBadge->getType() === $relatedBadge) {
                    $isBadgeAlreadyEarned = true;
                }
            }

            /* Does the user meet the badge requirements ? */
            $isUserMeetingBadgeRequirements = $definition->isApplicable($badgeable);

            /* We give the badge to the user if he doesn't have it yet AND he meets the requirements for this badge */
            $addBadgeToUser = !$isBadgeAlreadyEarned && $isUserMeetingBadgeRequirements;

            /* We remove the badge to the user if he already have it AND he doesn't meet the requirement for this badge anymore */
            $removeBadgeToUser =  $isBadgeAlreadyEarned && !$isUserMeetingBadgeRequirements;

            if ($addBadgeToUser || $removeBadgeToUser) {
                $badge = new Badge();
                $badge->setType($relatedBadge);
                $badge->setAwardingDate(new \DateTime());

                if ($addBadgeToUser) {
                    $badgeable->addBadge($badge);
                } else {
                    $badgeable->removeBadge($badge);
                }
            }
        }
    }

    /**
     * This method is used in the RegisterBadgeDefinitionsPass in order to construct the $this->badgeDefinitions
     * collection of event -> definitions.
     *
     * @param $event
     * @param $definitionId
     */
    public function addBadgeDefinition($event, $definitionId)
    {
        if (false === array_key_exists($event, $this->badgeDefinitions)) {
            $this->badgeDefinitions[$event] = array();
        }

        array_push($this->badgeDefinitions[$event], $definitionId);
    }
}
