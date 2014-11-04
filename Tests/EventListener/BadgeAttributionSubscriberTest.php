<?php

namespace MOG\Bundle\BadgeBundle\Tests;

use Behat\Behat\Definition\DefinitionInterface;
use MOG\Bundle\BadgeBundle\EventListener\BadgeAttributionSubscriber;
use MOG\Bundle\BadgeBundle\Model\BadgeDefinitionInterface;
use MOG\Bundle\BadgeBundle\Model\BadgeEventInterface;
use MOG\Bundle\BadgeBundle\Model\BadgeableInterface;
use MOG\Bundle\BadgeBundle\Model\Badge;
use MOG\Bundle\BadgeBundle\Model\BadgeFactory;
use Phake;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class BadgeAttributionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var BadgeableInterface
     */
    private $badgeable;

    /**
     * @var DefinitionInterface
     */
    private $definition;

    /**
     * @var BadgeFactory
     */
    private $badgeFactory;

    /**
     * @var Event
     */
    private $event;

    public function setup()
    {
        $this->dispatcher = Phake::mock(ContainerAwareEventDispatcher::class);
        Phake::when($this->dispatcher)->dispatch()->thenReturn(Phake::anyParameters());

        $this->badgeFactory = new BadgeFactory();

        $this->container = Phake::mock(ContainerInterface::class);

        $this->badgeable = Phake::mock(BadgeableInterface::class);

        $this->event = Phake::mock(Event::class);
        Phake::when($this->event)->getName()->thenReturn('dummy_event');

        $this->definition = Phake::mock(BadgeDefinitionInterface::class);
        Phake::when($this->definition)->getBadgeable($this->event)->thenReturn($this->badgeable);
        Phake::when($this->definition)->getBadgeName()->thenReturn('dummy_badge');
        Phake::when($this->definition)->supports($this->event)->thenReturn(true);

        Phake::when($this->container)->get('dummy_definition')->thenReturn($this->definition);
    }

    /**
     * If a definition is bound to an event, when the event is received,
     * the Subscriber asks the definition if the badge is earned or not
     */
    public function testDefinitionIsCalledWhenEventIsReceived()
    {
        Phake::when($this->badgeable)->getBadges()->thenReturn(array());

        $bas = new BadgeAttributionSubscriber($this->container, $this->badgeFactory);
        $bas->addBadgeDefinition('dummy_event', 'dummy_definition');

        $bas->updateBadges($this->event);

        Phake::verify($this->definition, Phake::times(1))->supports($this->event);
        Phake::verify($this->definition, Phake::times(1))->isApplicable($this->event);
    }

    /**
     * If multiple definitions are bound to an event, when the event is received,
     * the Subscriber asks all the definitions if the badge is earned or not
     */
    public function testMultipleDefinitionsAreCalledWhenEventIsReceived()
    {
        Phake::when($this->badgeable)->getBadges()->thenReturn(array());

        $definition2 = Phake::mock(BadgeDefinitionInterface::class);
        Phake::when($definition2)->getBadgeable($this->event)->thenReturn($this->badgeable);
        Phake::when($definition2)->getBadgeName()->thenReturn('dummy_badge_2');
        Phake::when($definition2)->supports($this->event)->thenReturn(true);

        Phake::when($this->container)->get('dummy_definition_2')->thenReturn($definition2);

        $bas = new BadgeAttributionSubscriber($this->container, $this->badgeFactory);
        $bas->addBadgeDefinition('dummy_event', 'dummy_definition');
        $bas->addBadgeDefinition('dummy_event', 'dummy_definition_2');

        $bas->updateBadges($this->event);

        Phake::verify($this->definition, Phake::times(1))->supports($this->event);
        Phake::verify($this->definition, Phake::times(1))->isApplicable($this->event);
        Phake::verify($definition2, Phake::times(1))->supports($this->event);
        Phake::verify($definition2, Phake::times(1))->isApplicable($this->event);
    }

    /**
     * If a definition says yes and the user does not have the badge
     * The badge is given to him
     */
    public function testAddBadgeIfUserMeetsRequirements()
    {
        Phake::when($this->badgeable)->getBadges()->thenReturn(array());
        Phake::when($this->definition)->isApplicable($this->event)->thenReturn(true);

        $bas = new BadgeAttributionSubscriber($this->container, $this->badgeFactory);
        $bas->addBadgeDefinition('dummy_event', 'dummy_definition');

        $bas->updateBadges($this->event);

        Phake::verify($this->badgeable, Phake::times(1))->addBadge(Phake::anyParameters());
    }

    /**
     * If a definition says no and the user does not have the badge
     * The badge is NOT given to him
     */
    public function testAddBadgeIfUserDoesNotMeetRequirements()
    {
        Phake::when($this->badgeable)->getBadges()->thenReturn(array());
        Phake::when($this->definition)->isApplicable($this->event)->thenReturn(false);

        $bas = new BadgeAttributionSubscriber($this->container, $this->badgeFactory);
        $bas->addBadgeDefinition('dummy_event', 'dummy_definition');

        $bas->updateBadges($this->event);

        Phake::verify($this->badgeable, Phake::times(0))->addBadge(Phake::anyParameters());
    }

    /**
     * A badge cannot be given twice to the same user
     */
    public function testCannotAddSameBadgeTwice()
    {
        $badge = Phake::mock(Badge::class);
        Phake::when($badge)->getType()->thenReturn('dummy_badge');

        Phake::when($this->badgeable)->getBadges()->thenReturn(array($badge));
        Phake::when($this->definition)->isApplicable($this->event)->thenReturn(true);

        $bas = new BadgeAttributionSubscriber($this->container, $this->badgeFactory);
        $bas->addBadgeDefinition('dummy_event', 'dummy_definition');

        $bas->updateBadges($this->event);

        Phake::verify($this->badgeable, Phake::times(0))->addBadge(Phake::anyParameters());
    }

    /**
     * If a user has a badge but does not meet the requirement for it, the badge
     * is removed
     */
    public function testBadgeIsRemovedIfUserDoesNotMeetRequirementsAnymore()
    {
        $badge = Phake::mock(Badge::class);
        Phake::when($badge)->getType()->thenReturn('dummy_badge');

        Phake::when($this->badgeable)->getBadges()->thenReturn(array($badge));
        Phake::when($this->definition)->isApplicable($this->event)->thenReturn(false);

        $bas = new BadgeAttributionSubscriber($this->container, $this->badgeFactory);
        $bas->addBadgeDefinition('dummy_event', 'dummy_definition');

        $bas->updateBadges($this->event);

        Phake::verify($this->badgeable, Phake::times(1))->removeBadge(Phake::anyParameters());
    }
}
