<?php

namespace MOG\Bundle\BadgeBundle\Model;

class Badge
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var \DateTime
     */
    protected $awardingDate;

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
    public function setAwardingDate(\DateTime $awardingDate)
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }
}
