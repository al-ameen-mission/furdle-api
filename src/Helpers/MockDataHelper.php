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
            'name' => 'Mathematics (Local Branch)',
            'description' => 'A branch of science concerned with the properties and relations of numbers and quantities and shapes.',
            'query' => [
                'type' => 'student',
                'branch' => '010100113',
                'session' => '2025',
                'class' => '5'
            ],
            "payload" => [
                "event_code" => "1",
            ],
        ],
        [
            'code' => '10',
            'name' => 'Mathematics (Global)',
            'description' => 'A branch of science concerned with the properties and relations of numbers and quantities and shapes.',
            'query' => [
                'type' => 'student',
                'session' => '2025',
                'class' => '5'
            ],
            "payload" => [
                "event_code" => "10",
            ],
        ],
        [
            'code' => '2',
            'name' => 'Admission 2025',
            'description' => 'Information regarding the admission process for the year 2025.',
            'query' => [
                'type' => 'admission',
                'session' => '2025',
                'class' => '5'
            ],
            'payload' => [
                "event_code" => "2",
            ],
        ],
        [
            'code' => '3',
            'name' => 'Staff (Branch Only)',
            'description' => 'Staff meeting for 010100113 branch.',
            'query' => [
                'type' => 'admin',
                'branch' => '010100113'
            ],
            'payload' => [
                "event_code" => "3",
            ],
        ],
        [
            'code' => '4',
            'name' => 'Staff (Global)',
            'description' => 'General announcements for all students and staff.',
            'query' => [
                'type' => 'admin'
            ],
            'payload' => [
                "event_code" => "4",
            ],
        ],
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
