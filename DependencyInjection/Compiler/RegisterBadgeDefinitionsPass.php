<?php

namespace MOG\Bundle\BadgeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * The RegisterBadgeDefinitionsPass will fetch all the services tagged with the tag 'maresidence_badge.definition'
 * This tag also embed an attribute 'event' which defined for which event we should check the badge winning
 *
 * The pass will automatically register this event in the events subscribed by the BadgeAttributionSubscriber of this bundle
 */
class RegisterBadgeDefinitionsPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $dispatcherService;

    /**
     * @var string
     */
    private $badgeDefinitionTag;

    /**
     * @var string
     */
    private $badgeAttributionSubscriberService;

    /**
     * @var string
     */
    private $badgeAttributionSubscriberMethod;

    /**
     * @param string $dispatcherService
     * @param string $badgeDefinitionTag
     * @param string $badgeAttributionSubscriberService
     * @param string $badgeAttributionSubscriberMethod
     */
    public function __construct(
        $dispatcherService = 'event_dispatcher',
        $badgeDefinitionTag = 'maresidence_badge.definition',
        $badgeAttributionSubscriberService = 'mog_badge.event_subscriber.badge_attribution',
        $badgeAttributionSubscriberMethod = 'updateBadges'
    ) {
        $this->dispatcherService = $dispatcherService;
        $this->badgeDefinitionTag = $badgeDefinitionTag;
        $this->badgeAttributionSubscriberService = $badgeAttributionSubscriberService;
        $this->badgeAttributionSubscriberMethod = $badgeAttributionSubscriberMethod;
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->dispatcherService)) {
            return;
        }

        $eventDispatcherDefinition = $container->getDefinition($this->dispatcherService);
        $badgeAttributionSubscriberDefinition = $container->getDefinition($this->badgeAttributionSubscriberService);

        foreach ($container->findTaggedServiceIds($this->badgeDefinitionTag) as $id => $events) {
            foreach ($events as $event) {
                $priority = isset($event['priority']) ? $event['priority'] : 0;

                if (!isset($event['event'])) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Service "%s" must define the "event" attribute on "maresidence_badge.definition" tags.',
                            $id
                        )
                    );
                }

                /**
                 * We make badge attribution service aware of the event specified in the tag
                 */
                $eventDispatcherDefinition->addMethodCall(
                    'addListenerService',
                    array(
                        $event['event'],
                        array($this->badgeAttributionSubscriberService, $this->badgeAttributionSubscriberMethod),
                        $priority
                    )
                );

                /**
                 * We add the definitions to the definition collection of the badge attribution subscriber
                 */
                $badgeAttributionSubscriberDefinition->addMethodCall(
                    'addBadgeDefinition',
                    array(
                        $event['event'],
                        $id
                    )
                );
            }
        }
    }
}
