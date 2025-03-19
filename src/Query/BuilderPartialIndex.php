<?php

declare(strict_types=1);

namespace Tpetry\PostgresqlEnhanced\Query;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * The implementations of these functions have been taken from the Laravel core and
 * have been changed in the most minimal way to support the returning clause.
 */
trait BuilderPartialIndex
{
    /**
     * Insert new records or update the existing ones.
     *
     * @return int
     */
    public function upsertPartialIndex(array $values, array|string $uniqueBy, ?array $update = null, string $partialIndexWhereClause = null): int
    {
        if (empty($values)) {
            return collect();
        } elseif (empty($partialIndexWhereClause)) {
            return $this->upsert($values, $uniqueBy, $update);
        }

        if (!\is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        if (null === $update) {
            $update = array_keys(reset($values));
        }

        if (method_exists($this, 'applyBeforeQueryCallbacks')) {
            $this->applyBeforeQueryCallbacks();
        }

        $bindings = $this->cleanBindings(array_merge(
            Arr::flatten($values, 1),
            collect($update)->reject(function ($_value, $key) {
                return \is_int($key);
            })->all()
        ));

        return $this->connection->affectingStatement(
            $this->grammar->compileUpsertPartialIndex($this, $values, (array) $uniqueBy, $update, $partialIndexWhereClause),
            $bindings
        );
    }
}
