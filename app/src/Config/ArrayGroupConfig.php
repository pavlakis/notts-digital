<?php

declare(strict_types=1);

namespace NottsDigital\Config;

final class ArrayGroupConfig implements GroupConfigInterface
{
    /**
     * @var string
     */
    private $groupConfigDir;

    public function __construct(string $groupConfigDir)
    {
        $this->groupConfigDir = $groupConfigDir;
    }

    public function fetchConfig(): array
    {
        $groupFilename = $this->groupConfigDir . '/groups.php';
        if (!\file_exists($groupFilename)) {
            throw new \LogicException(\sprintf('Group config not found on %s location', $groupFilename));
        }

        return include $groupFilename;
    }
}
