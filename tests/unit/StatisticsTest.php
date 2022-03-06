<?php

declare(strict_types=1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use Statistics\Builder\ParamsBuilder;
use Statistics\Dto\StatisticsTo;
use SocialPost\Hydrator\FictionalPostHydrator;
use Statistics\Service\Factory\StatisticsServiceFactory;
use Statistics\Enum;


/**
 * Class StatisticsTest
 *
 * @package Tests\unit
 */
class StatisticsTest extends TestCase
{
    /**
     * @var array
     */
    private array $exceptedResults = [
        Enum\StatsEnum::AVERAGE_POST_NUMBER_PER_USER => 1,
        Enum\StatsEnum::AVERAGE_POST_LENGTH => 495.25,
    ];

    /**
     *
     */
    public function testAveragePostsPerUser(): void
    {
        $key = Enum\StatsEnum::AVERAGE_POST_NUMBER_PER_USER;
        $searchedStatistic = $this->getSearchedStatistic($key);
        $this->assertEquals(
            $this->exceptedResults[$key],
            $searchedStatistic->getValue()
        );
    }

    /**
     *
     */
    public function testAveragePostLength(): void
    {
        $key = Enum\StatsEnum::AVERAGE_POST_LENGTH;
        $searchedStatistic = $this->getSearchedStatistic($key);
        $this->assertEquals(
            $this->exceptedResults[$key],
            $searchedStatistic->getValue()
        );
    }

    /**
     * @return StatisticsTo
     */
    private function getStatisticsTo(): StatisticsTo
    {
        $data = $this->getTestData();
        $dateTime = new \DateTime($data['meta']['response_end']['time']);
        $params = ParamsBuilder::reportStatsParams($dateTime);
        $posts = [];
        $hydrator = new FictionalPostHydrator();
        foreach ($data['data']['posts'] as $post) {
            $posts[] = $hydrator->hydrate($post);
        }
        $statsService = StatisticsServiceFactory::create();
        return $statsService->calculateStats(
            new \ArrayObject($posts),
            $params
        );
    }

    /**
     * @return array
     * @throws \JsonException
     */
    private function getTestData(): array
    {
        return json_decode(
            file_get_contents(
                dirname(__DIR__) . '/data/social-posts-response.json'
            ),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @param string $name
     * @return StatisticsTo
     */
    private function getSearchedStatistic(string $name): StatisticsTo
    {
        $statisticsTo = $this->getStatisticsTo();
        $object = array_filter(
            $statisticsTo->getChildren(),
            static function ($e) use (&$name) {
                return $e->getName() === $name;
            }
        );
        return reset($object);
    }
}
