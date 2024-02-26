<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Api\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\DataFixtures\ActivityFixtures;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\IRI;
use XApi\Repository\Api\ActivityRepositoryInterface;

/**
 * @author Jérôme Parmentier <jerome.parmentier@acensi.fr>
 */
abstract class ActivityRepositoryTest extends TestCase
{
    private ActivityRepositoryInterface $activityRepository;

    protected function setUp(): void
    {
        $this->activityRepository = $this->createActivityRepository();
        $this->cleanDatabase();
    }

    protected function tearDown(): void
    {
        $this->cleanDatabase();
    }

    public function testFetchingNonExistingActivityThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->activityRepository->findActivityById(IRI::fromString('not-existing'));
    }

    /**
     * @dataProvider getActivitiesWithId
     */
    public function testActivitiesCanBeRetrievedById(Activity $activity): void
    {
        $fetchedActivity = $this->activityRepository->findActivityById($activity->getId());

        $this->assertTrue($activity->equals($fetchedActivity));
    }

    public function getActivitiesWithId(): array
    {
        $fixtures = [];

        foreach (get_class_methods(ActivityFixtures::class) as $method) {
            $activity = call_user_func([ActivityFixtures::class, $method]);

            if ($activity instanceof Activity) {
                $fixtures[$method] = [$activity];
            }
        }

        return $fixtures;
    }

    abstract protected function createActivityRepository();

    abstract protected function cleanDatabase();
}
