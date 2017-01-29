<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
namespace NottsDigital\Event;


final class GroupInfo implements GroupInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $photo;

    /**
     * GroupInfo constructor.
     * @param string $name
     * @param string $description
     * @param string $photo
     */
    public function __construct($name, $description, $photo)
    {
        $this->name = $name;
        $this->description = $description;
        $this->photo = $photo;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getGroupDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getGroupPhoto()
    {
        return $this->photo;
    }
}