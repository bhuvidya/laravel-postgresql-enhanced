<?php

declare(strict_types=1);

namespace Tpetry\PostgresqlEnhanced\Eloquent\Mixins;

use Closure;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/** @mixin \Illuminate\Database\Eloquent\Builder */
class BuilderPartialIndex
{
    public function upsertPartialIndex(): Closure
    {
        return function (array $values, array|string $uniqueBy, ?array $update = null, string $partialIndexWhereClause = null): int {
            /* @var \Illuminate\Database\Eloquent\Builder $this */

            if (empty($partialIndexWhereClause)) {
                return $this->upsert(
                    $this->addTimestampsToUpsertValues($this->addUniqueIdsToUpsertValues($values)),
                    $uniqueBy,
                    $this->addUpdatedAtToUpsertColumns($update),
                );
            }

            if (0 === \count($values)) {
                return 0;
            }

            if (null === $update) {
                $update = array_keys(reset($values));
            }

            return $this->toBase()->upsertPartialIndex(
                $this->addTimestampsToUpsertValues($this->addUniqueIdsToUpsertValues($values)),
                $uniqueBy,
                $this->addUpdatedAtToUpsertColumns($update),
                $partialIndexWhereClause
            );
        };
    }
}
