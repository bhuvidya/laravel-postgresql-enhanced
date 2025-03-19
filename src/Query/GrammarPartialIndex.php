<?php

declare(strict_types=1);

namespace Tpetry\PostgresqlEnhanced\Query;

use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

trait GrammarPartialIndex
{
    /**
     * Compile an "upsert" statement with "partial index" into SQL.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array   $values
     * @param  array   $uniqueBy
     * @param  array   $update
     * @param  string  $
     * @return string
     */
    public function compileUpsertPartialIndex(Builder $query, array $values, array $uniqueBy, array $update, string $partialIndexWhereClause): string
    {
        $sql = $this->compileInsert($query, $values);

        $sql .= ' on conflict ('.$this->columnize($uniqueBy).') '.$partialIndexWhereClause.' do update set ';

        $columns = (new Collection($update))->map(function ($value, $key) {
            return is_numeric($key)
                ? $this->wrap($value).' = '.$this->wrapValue('excluded').'.'.$this->wrap($value)
                : $this->wrap($key).' = '.$this->parameter($value);
        })->implode(', ');

        return $sql.$columns;
    }
}
