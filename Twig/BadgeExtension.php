<?php

namespace MOG\Bundle\BadgeBundle\Twig;

use MOG\Bundle\BadgeBundle\Loader\BadgeConfigLoader;
use MOG\Bundle\BadgeBundle\Model\BadgeableInterface;
use MOG\Bundle\BadgeBundle\Model\BadgeConfig;
use Twig_Environment;

class BadgeExtension extends \Twig_Extension
{
    /**
     * @var Twig_Environment
     */
    private $twigEnvironment;

    /**
     * @var BadgeConfigLoader
     */
    private $badgeConfigLoader;

    /**
     * @param Twig_Environment $twigEnvironment
     * @param BadgeConfigLoader $badgeConfigLoader
     */
    public function __construct(Twig_Environment $twigEnvironment, BadgeConfigLoader $badgeConfigLoader)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->badgeConfigLoader = $badgeConfigLoader;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'displayBadges' => new \Twig_Function_Method($this, 'displayBadges'),
            'displayBadgesTeaser' => new \Twig_Function_Method($this, 'displayBadgesTeaser'),
        );
    }

    /**
     * @param BadgeableInterface $badgeable
     * @param int $size
     * @param null|int $limit
     */
    public function displayBadges(BadgeableInterface $badgeable, $size = 80, $limit = null)
    {
        $badges = array_map(
            function ($badge) {
                return $this->badgeConfigLoader->load($badge->getType());
            },
            $badgeable->getBadges()
        );

        if (null !== $limit && is_numeric($limit)) {
            $badges = array_slice($badges, 0, $limit);
        }

        $this->twigEnvironment->display(
            'MOGBadgeBundle:Badge:list.html.twig',
            array(
                'badges' => $badges,
                'size' => $size,
                'colorized' => true,
            )
        );
    }

    /**
     * Display the grey badges if the user does not have enough badges
     *
     * If the user has 1 badge and the teaser has the $number set to 3, we will display 2 grey badges
     * If the user has 2 badges and the teaser has the $number set to 3, we will display 1 grey badges
     * If the user has 3 badges and the teaser has the $number set to 3, we will display 0 grey badges
     *
     * @param BadgeableInterface $badgeable
     * @param $size
     * @param $number
     */
    public function displayBadgesTeaser(BadgeableInterface $badgeable, $size, $number)
    {
        $badges = $badgeable->getBadges();

        $teaserBadgesNumberToShow = $number - count($badges);

        /* If the teaser has to show 3 elements but the user already have 3 badges, the teaser should not show anything */
        if ($teaserBadgesNumberToShow <= 0) {
            return;
        }

        $defaultBadges = array(
            'super_solidary',
            'super_sharer',
            'super_bambino'
        );

        $defaultBadgesToShow = array_slice($defaultBadges, 0, $teaserBadgesNumberToShow);

        $badges = array_map(
            function ($badge) {
                return $this->badgeConfigLoader->load($badge);
            },
            $defaultBadgesToShow
        );

        $this->twigEnvironment->display(
            'MOGBadgeBundle:Badge:list.html.twig',
            array(
                'badges' => $badges,
                'size' => $size,
                'colorized' => false,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'badge_extension';
    }
}
