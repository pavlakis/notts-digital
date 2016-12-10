<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Group;


final class GroupInfo implements GroupInterface
{
    /**
     * @var string
     */
    private $groupName;

    /**
     * @var string
     */
    private $groupInfo;

    /**
     * GroupInfo constructor.
     * @param $groupName
     * @param $groupInfo
     */
    public function __construct($groupName, $groupInfo)
    {
        $this->groupName = $groupName;
        $this->groupInfo = $groupInfo;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * @return string
     */
    public function getGroupInfo()
    {
        return $this->groupInfo;
    }
}