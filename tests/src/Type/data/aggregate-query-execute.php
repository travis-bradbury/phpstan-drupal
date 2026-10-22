<?php

declare(strict_types=1);

namespace DrupalAggregateQuery;

use function PHPStan\Testing\assertType;

// An aggregate query grouped by a field returns a list of result rows, where
// each row is an array keyed by the aggregate results and the groupBy columns.
// @see \Drupal\Core\Entity\Query\QueryAggregateInterface::execute()
$rows = \Drupal::entityTypeManager()->getStorage('block')->getAggregateQuery()
    ->accessCheck(FALSE)
    ->condition('status', TRUE)
    ->groupBy('theme')
    ->execute();
assertType('array<int, array<string, string>>', $rows);

// array_column() over those rows pulls a single groupBy column back out, which
// is the pattern used to collect the grouped IDs for a follow-up query.
$ids = array_column(
    \Drupal::entityTypeManager()->getStorage('block')->getAggregateQuery()
        ->accessCheck(FALSE)
        ->condition('status', TRUE)
        ->groupBy('theme')
        ->execute(),
    'theme'
);
assertType('list<string>', $ids);

// Because the column can be present, the result is not statically empty, so a
// truthiness check on it is meaningful rather than "always false".
$filtered = $ids ? $ids : [];
assertType('list<string>', $filtered);
