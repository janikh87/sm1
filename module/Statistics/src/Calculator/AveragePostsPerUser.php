<?php

declare(strict_types = 1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AveragePostsPerUser extends AbstractCalculator
{
    protected const UNITS = 'posts per user';

    private array $authors = [];
    private int $postCount = 0;

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $this->postCount++;
        $this->authors[] = $postTo->getAuthorId();
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        $value = $this->postCount > 0
            ? round($this->postCount / count(array_unique($this->authors)))
            : 0;
        return (new StatisticsTo())->setValue($value);
    }
}
