<?php



return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'DD/MM/YY',
    ],
    'first_day_of_week' => 1,
    'day_of_first_week_of_year' => 1,
]);
