<?php

namespace AppBundle\Models;

use Doctrine\Common\Collections\ArrayCollection;

class Invitation
{
    /** @var int */
    private $id;

    /** @var string */
    private $invitationToken;

    /** @var string */
    private $email;

    /** @var string */
    private $role;

    /** @var ArrayCollection[Campaign] */
    private $assignedCampaigns;

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getInvitationToken(): string
    {
        return $this->invitationToken;
    }

    /**
     * @param string $invitationToken
     */
    public function setInvitationToken(string $invitationToken)
    {
        $this->invitationToken = $invitationToken;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role)
    {
        $this->role = $role;
    }

    /**
     * @return ArrayCollection[Campaign]
     */
    public function getAssignedCampaigns()
    {
        return $this->assignedCampaigns;
    }

    /**
     * @param Campaign $assignedCampaigns
     */
    public function addAssignedCampaigns(Campaign $assignedCampaigns)
    {
        $this->assignedCampaigns[] = $assignedCampaigns;
    }

    /**
     * @param \AppBundle\Models\Campaign $campaign
     */
    public function removeAssignedCampaign(Campaign $campaign)
    {
        $this->assignedCampaigns->removeElement($campaign);
    }
}