<?php

namespace MOG\Bundle\BadgeBundle\Entity;

class Badge
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \DateTime
     */
    private $awardingDate;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $type
     * @return Badge
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * The type of the badge is the key defined in the config.yml file (ex: profile_completion, skills, super_relay...)
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \DateTime $awardingDate
     *
     * @return Badge
     */
    public function setAwardingDate($awardingDate)
    {
        $this->awardingDate = $awardingDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAwardingDate()
    {
        return $this->awardingDate;
    }
}
