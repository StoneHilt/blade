<?php

namespace StoneHilt\Blade\Tests\Feature\Directives;

use Carbon\Carbon;
use StoneHilt\Blade\Tests\Feature\FeatureTestCase;

/**
 * Class DateTimeTest
 *
 * @package StoneHilt\Blade\Tests\Feature\Directives
 */
class DateTimeTest extends FeatureTestCase
{
    /**
     * @dataProvider provider_directive
     * @param string|\DateTime $date
     * @param array|null $config
     * @param string $expects
     * @return void
     */
    public function test_directive(string|\DateTime $date, ?array $config, string $expects)
    {
        if (isset($config)) {
            config(['formats.defaults' => $config]);
        }

        $this->view('directives.date-time', ['date' => $date])
            ->assertSee(
                sprintf(
                    '<div>%s</div>',
                    $expects
                ),
                false
            );
    }

    /**
     * @return array[]
     */
    public static function provider_directive(): array
    {
        $now = Carbon::now();

        return [
            [
                'date' => '2025-07-15T12:34:45Z',
                'config' => null,
                'expects' => '07/15/2025 12:34 PM',
            ],
            [
                'date' => $now,
                'config' => null,
                'expects' => $now->format('m/d/Y g:i A'),
            ],

            [
                'date' => '2025-07-15T12:34:45Z',
                'config' => [
                    'date' => 'Y-m-d',
                    'datetime' => 'c',
                    'time' => 'r',
                ],
                'expects' => '2025-07-15T12:34:45+00:00',
            ],
            [
                'date' => $now,
                'config' => [
                    'date' => 'Y-m-d',
                    'datetime' => 'c',
                    'time' => 'r',
                ],
                'expects' => $now->format('c'),
            ],
        ];
    }
}
