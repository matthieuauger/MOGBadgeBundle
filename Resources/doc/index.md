# How to use it ?

1. Add the bad in the config.yml

Ex:

    skills:
       name: Je peux aider
       picture: /images/badge/skills.png

2. Create a BadgeDefinition class into your bundle

Ex:

    class SkillsBadgeDefinition implements BadgeDefinitionInterface
    {
        /**
         * @param $subject
         * @return bool|void
         *
         * @throws \Exception
         */
        public function isApplicable($subject)
        {
            if (!$subject instanceof PhysicalPerson) {
                throw new \Exception('The skills badge is only earnable to Physical Persons');
            }

            return $subject->hasSkills();
        }

        public function getBadgeName()
        {
            return 'skills';
        }
    }

The function isApplicable() defines the logic need to acquire the badge.
The method getBadgeName() should return the name of the badge (the key in the config.yml file. In our example, it's 'skills')

3. A badge definition is a service. Tag this service with 'maresidence_badge.definition':

    `<service id="mog_badge.definition.skills" class="MOG\Bundle\PersonBundle\Badge\SkillsBadgeDefinition">
        <tag name="maresidence_badge.definition" event="physical_person_profile_updated" />
    </service>`

You must set the Event which will be listened by the BadgeAttributionSubscriber. You have 2 choices :
    - Bind the AttributionSubscriber onto one of YOUR business events (in which case your event need to implement BadgeEventInterface)
    - Bind the AttributionSubscriber onto a generic event of this bundle (BadgeEvent)

4. Now all you have to do is to dispatch the Event when you want the Subscriber to determine if the badge is acquired or not

/!\ Be careful, the Subcriber add the Badge to your entity, but does not flush the changes. YOU have to do it after the event is dispatched
