<?php

use app\models\LessonFormat;

return [
    [
        'id' => 1,
        'course_id' => 2,
        'teacher_id' => 1, // Alice van Dijk
        'persons_per_lesson' => 1,
        'duration_minutes' => 60,
        'weeks_per_year' => 36,
        'frequency' => LessonFormat::FREQUENCY_WEEKLY,
        'price_per_person' => 55.00,
        'price_display_type' => LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON,
        'remarks' => 'Voor gevorderde studenten die een concervatoriumopleiding overwegen.',
    ],
    [
        'id' => 2,
        'course_id' => 2,
        'teacher_id' => 7, // Gina Vos
        'persons_per_lesson' => 1,
        'duration_minutes' => 30,
        'weeks_per_year' => 36,
        'frequency' => LessonFormat::FREQUENCY_WEEKLY,
        'price_per_person' => 22.50,
        'price_display_type' => LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON,
        'remarks' => '',
    ],
    [
        'id' => 3,
        'course_id' => 2,
        'teacher_id' => 7, // Gina Vos
        'persons_per_lesson' => 2,
        'duration_minutes' => 45,
        'weeks_per_year' => 36,
        'frequency' => LessonFormat::FREQUENCY_WEEKLY,
        'price_per_person' => 17.50,
        'price_display_type' => LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON,
        'remarks' => '',
    ],
    [
        'id' => 4,
        'course_id' => 3,
        'teacher_id' => 2, // Bob Jansen
        'persons_per_lesson' => 4,
        'duration_minutes' => 60,
        'weeks_per_year' => 34,
        'frequency' => LessonFormat::FREQUENCY_WEEKLY,
        'price_per_person' => 12.00,
        'price_display_type' => LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON,
        'remarks' => '',
    ],
    [
        'id' => 5,
        'course_id' => 1,
        'teacher_id' => 6, // Ferry Kuipers
        'persons_per_lesson' => 1,
        'duration_minutes' => 60,
        'weeks_per_year' => 30,
        'frequency' => LessonFormat::FREQUENCY_BIWEEKLY,
        'price_per_person' => 35.00,
        'price_display_type' => LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON,
        'remarks' => '',
    ],
    [
        'id' => 6,
        'course_id' => 2,
        'teacher_id' => 10, // Joris Willems
        'persons_per_lesson' => 8,
        'duration_minutes' => 90,
        'weeks_per_year' => 32,
        'frequency' => LessonFormat::FREQUENCY_WEEKLY,
        'price_per_person' => 9.50,
        'price_display_type' => LessonFormat::PRICE_DISPLAY_PER_PERSON_PER_LESSON,
        'remarks' => '',
    ],
    [
        'id' => 7,
        'course_id' => 4,
        'teacher_id' => 5, // Eva Smits
        'persons_per_lesson' => 30,
        'duration_minutes' => 120,
        'weeks_per_year' => 30,
        'frequency' => LessonFormat::FREQUENCY_WEEKLY,
        'price_per_person' => null, // prijs op aanvraag
        'price_display_type' => LessonFormat::PRICE_DISPLAY_HIDDEN,
        'remarks' => '',
    ],
];
