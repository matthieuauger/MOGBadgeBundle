<?php

namespace MOG\Bundle\BadgeBundle\EventListener;

use MOG\Bundle\BadgeBundle\Model\Badge;
use MOG\Bundle\BadgeBundle\Event\AwardBadgeEvent;
use MOG\Bundle\BadgeBundle\Event\BadgeEvents;
use MOG\Bundle\BadgeBundle\Model\BadgeFactory;
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
     * @var BadgeFactory
     */
    private $badgeFactory;

    /**
     * @var array
     */
    private $badgeDefinitions = array();

    public function __construct(ContainerInterface $container, BadgeFactory $badgeFactory)
    {
        $this->container = $container;
        $this->badgeFactory = $badgeFactory;
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
        foreach ($this->badgeDefinitions[$event->getName()] as $definitionId) {
            $definition = $this->container->get($definitionId);

            if ($definition->supports($event)) {
                $badge = $this->badgeFactory->create();
                $badge->setType($definition->getBadgeName());

                $badgeable = $definition->getBadgeable($event);

                $isAlreadyEarned = $this->isAlreadyEarned($badgeable, $badge);

                $isApplicable = $definition->isApplicable($event);

                if (!$isAlreadyEarned && $isApplicable) {
                    $badge->setAwardingDate(new \DateTime());
                    $badgeable->addBadge($badge);
                    $addedEvent = new AwardBadgeEvent($badgeable, $badge->getType());
                    $this->container->get('event_dispatcher')->dispatch(BadgeEvents::AWARD_BADGE_EVENT, $addedEvent);
                } elseif ($isAlreadyEarned && !$isApplicable) {
                    $badgeable->removeBadge($badge);
                }
            }
        }
    }

    private function isAlreadyEarned(BadgeableInterface $badgeable, Badge $badge)
    {
        foreach ($badgeable->getBadges() as $alreadyEarnedBadge) {
            if ($alreadyEarnedBadge->getType() === $badge->getType()) {
                return true;
            }
        }

        return false;
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
