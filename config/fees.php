<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fee Management Lists
    |--------------------------------------------------------------------------
    |
    | Centralize the options used across the fee management module so that
    | updating school years or terms can be done without touching controllers
    | or views. These values can later be made editable via an admin UI by
    | persisting them to the database and caching them through this config.
    |
    */

    'school_years' => [
        '2024-2025',
        '2025-2026',
    ],

    'semesters' => [
        'First Semester',
        'Second Semester',
        'Quarter 1',
        'Quarter 2',
        'Quarter 3',
        'Quarter 4',
    ],
];
