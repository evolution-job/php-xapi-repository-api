<?php

declare(strict_types=1);

/*
 * This file is part of the xAPI package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Api;

use DateTimeImmutable;
use Xabbuh\XApi\Model\ProfileDocument;

/**
 * @author Mathieu Boldo <mathieu.boldo@entrili.com>
 */
interface ProfileRepositoryInterface
{
    public function find(string $resource, string $profileId): ?ProfileDocument;

    /** @return string[] */
    public function findIds(string $resource, ?DateTimeImmutable $since = null): array;

    public function store(string $resource, string $profileId, ProfileDocument $profileDocument): void;

    public function remove(string $resource, ?string $profileId = null): void;
}
