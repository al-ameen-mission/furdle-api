<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Mock Data Helper for generating realistic test data.
 */
class MockDataHelper
{

    /** @var array */
    private static $events = [
        [
            'code' => '1',
            'name' => 'Mathematics',
            'description' => 'A branch of science concerned with the properties and relations of numbers and quantities and shapes.',
            'facePayload' => [
                'type' => 'student',
                'branch' => 'XFM1000231',
                'session' => '2025',
                'class' => '5'
            ],
            'created_at' => '2024-11-01 08:00:00',
            'is_active' => true
        ],
        [
            'code' => '2',
            'name' => 'Admission 2025',
            'description' => 'Information regarding the admission process for the year 2025.',
            'facePayload' => [
                'type' => 'admission',
                'session' => '2025',
                'class' => '5'
            ],
            'created_at' => '2024-11-02 09:00:00',
            'is_active' => true
        ],
        [
            'code' => '3',
            'name' => 'Staff (Branch Only)',
            'description' => 'Staff meeting for XFM1000231 branch.',
            'facePayload' => [
                'type' => 'staff',
                'branch' => 'XFM1000231'
            ],
            'created_at' => '2024-11-03 10:00:00',
            'is_active' => true
        ],
        [
            'code' => '4',
            'name' => 'Staff (Global)',
            'description' => 'General announcements for all students and staff.',
            'facePayload' => [
                'type' => 'staff'
            ],
            'created_at' => '2024-11-04 11:00:00',
            'is_active' => true
        ],
        [
            'code' => '5',
            'name' => 'Mathematics (All Students)',
            'description' => 'Mathematics class for all students in session 2025.',
            'facePayload' => [
                'type' => 'student',
                'session' => '2025',
                'class' => '5'
            ],
            'created_at' => '2024-11-05 12:00:00',
            'is_active' => true
        ],
        [
            'code' => '6',
            'name' => 'Physics Class 6',
            'description' => 'Physics class for grade 6 students.',
            'facePayload' => [
                'type' => 'student',
                'branch' => 'XFM1000232',
                'session' => '2025',
                'class' => '6'
            ],
            'created_at' => '2024-11-06 13:00:00',
            'is_active' => true
        ],
        [
            'code' => '7',
            'name' => 'Chemistry Lab',
            'description' => 'Chemistry laboratory session for students.',
            'facePayload' => [
                'type' => 'student',
                'session' => '2025',
                'class' => '5'
            ],
            'created_at' => '2024-11-07 14:00:00',
            'is_active' => true
        ],
        [
            'code' => '8',
            'name' => 'Staff Meeting XFM1000232',
            'description' => 'Branch-specific staff meeting.',
            'facePayload' => [
                'type' => 'staff',
                'branch' => 'XFM1000232'
            ],
            'created_at' => '2024-11-08 15:00:00',
            'is_active' => true
        ]
    ];

    /**
     * Get all events.
     *
     * @return array
     */
    public static function getEvents(): array
    {
        return self::$events;
    }

    /**
     * Get event by ID.
     *
     * @param string $id
     * @return array|null
     */
    public static function getEventById(string $id): ?array
    {
        foreach (self::$events as $event) {
            if ($event['id'] === $id) {
                return $event;
            }
        }
        return null;
    }

    /**
     * Generate API response wrapper.
     *
     * @param mixed $data
     * @param string $message
     * @param string $code
     * @return array
     */
    public static function apiResponse($data, string $message = 'success', string $code = 'success'): array
    {
        return [
            'code' => $code,
            'message' => $message,
            'result' => $data
        ];
    }
}
