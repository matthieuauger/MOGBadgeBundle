<?php

namespace MOG\Bundle\BadgeBundle\Loader;

use MOG\Bundle\BadgeBundle\Model\BadgeConfig;

/**
 * The BadgeConfigLoader role is to construct a BadgeConfig object when a badgeType is given to it
 * It will only get the corresponding entry in the config.yml file and create an object from it
 */
class BadgeConfigLoader
{
    /**
     * @var array
     */
    private $badgesConfiguration;

    public function __construct(array $badgesConfiguration)
    {
        $this->badgesConfiguration = $badgesConfiguration;
    }

    /**
     * Load the configuration of given badge
     *
     * @param $badgeType
     *
     * @return BadgeConfig
     * @throws \Exception
     */
    public function load($badgeType)
    {
        if (false === array_key_exists($badgeType, $this->badgesConfiguration)) {
            throw new \Exception(
                sprintf(
                    'Unable to load configuration for badge type %s. The configuration does not exist',
                    $badgeType
                )
            );
        }

        $badgeConfiguration = $this->badgesConfiguration[$badgeType];

        return new BadgeConfig($badgeConfiguration['name'], $badgeConfiguration['picture']);
    }
}
