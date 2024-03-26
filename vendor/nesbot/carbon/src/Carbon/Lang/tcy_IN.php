<?php




return array_replace_recursive(require __DIR__.'/en.php', [
    'formats' => [
        'L' => 'D/M/YY',
    ],
    'months' => ['ಜನವರಿ', 'ಫೆಬ್ರುವರಿ', 'ಮಾರ್ಚ್', 'ಏಪ್ರಿಲ್‌‌', 'ಮೇ', 'ಜೂನ್', 'ಜುಲೈ', 'ಆಗಸ್ಟ್', 'ಸೆಪ್ಟೆಂಬರ್‌', 'ಅಕ್ಟೋಬರ್', 'ನವೆಂಬರ್', 'ಡಿಸೆಂಬರ್'],
    'months_short' => ['ಜ', 'ಫೆ', 'ಮಾ', 'ಏ', 'ಮೇ', 'ಜೂ', 'ಜು', 'ಆ', 'ಸೆ', 'ಅ', 'ನ', 'ಡಿ'],
    'weekdays' => ['ಐಥಾರ', 'ಸೋಮಾರ', 'ಅಂಗರೆ', 'ಬುಧಾರ', 'ಗುರುವಾರ', 'ಶುಕ್ರರ', 'ಶನಿವಾರ'],
    'weekdays_short' => ['ಐ', 'ಸೋ', 'ಅಂ', 'ಬು', 'ಗು', 'ಶು', 'ಶ'],
    'weekdays_min' => ['ಐ', 'ಸೋ', 'ಅಂ', 'ಬು', 'ಗು', 'ಶು', 'ಶ'],
    'day_of_first_week_of_year' => 1,
    'meridiem' => ['ಕಾಂಡೆ', 'ಬಯ್ಯ'],

    'year' => ':count ನೀರ್', 
    'y' => ':count ನೀರ್', 
    'a_year' => ':count ನೀರ್', 

    'month' => ':count ಮೀನ್', 
    'm' => ':count ಮೀನ್', 
    'a_month' => ':count ಮೀನ್', 

    'day' => ':count ಸುಗ್ಗಿ', 
    'd' => ':count ಸುಗ್ಗಿ', 
    'a_day' => ':count ಸುಗ್ಗಿ', 
]);
