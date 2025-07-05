<?php

namespace StoneHilt\Blade\Tests\Feature\Directives;

use Carbon\Carbon;
use StoneHilt\Blade\Tests\Feature\FeatureTestCase;

/**
 * Class DateTest
 *
 * @package StoneHilt\Blade\Tests\Feature\Directives
 */
class TimeTest extends FeatureTestCase
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

        $this->view('directives.time', ['date' => $date])
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
                'expects' => '12:34 PM',
            ],
            [
                'date' => $now,
                'config' => null,
                'expects' => $now->format('g:i A'),
            ],

            [
                'date' => '2025-07-15T12:34:45Z',
                'config' => [
                    'date' => 'Y-m-d',
                    'datetime' => 'r',
                    'time' => 'g:i:s A e',
                ],
                'expects' => '12:34:45 PM Z',
            ],
            [
                'date' => $now,
                'config' => [
                    'date' => 'Y-m-d',
                    'datetime' => 'r',
                    'time' => 'g:i:s A e',
                ],
                'expects' => $now->format('g:i:s A e'),
            ],
        ];
    }
}
