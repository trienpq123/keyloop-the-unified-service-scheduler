<?php

declare(strict_types=1);

namespace Tests\Unit\Appointment\ValueObjects;

use App\Appointment\ValueObjects\TimeRange;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TimeRangeTest extends TestCase
{
    public function test_it_detects_partial_overlaps_on_both_sides(): void
    {
        $period = TimeRange::fromIso8601('2026-08-20T09:00:00+00:00', '2026-08-20T10:00:00+00:00');
        $overlapsOnLeft = TimeRange::fromIso8601('2026-08-20T08:30:00+00:00', '2026-08-20T09:30:00+00:00');
        $overlapsOnRight = TimeRange::fromIso8601('2026-08-20T09:30:00+00:00', '2026-08-20T10:30:00+00:00');

        self::assertSame(60, $period->durationInMinutes());
        self::assertTrue($period->overlaps($overlapsOnLeft));
        self::assertTrue($period->overlaps($overlapsOnRight));
    }

    public function test_back_to_back_ranges_do_not_overlap(): void
    {
        $first = TimeRange::fromIso8601('2026-08-20T09:00:00+00:00', '2026-08-20T10:00:00+00:00');
        $next = TimeRange::fromIso8601('2026-08-20T10:00:00+00:00', '2026-08-20T11:00:00+00:00');

        self::assertFalse($first->overlaps($next));
    }

    #[DataProvider('invalidIntervals')]
    public function test_it_rejects_an_end_that_is_not_after_start(string $start, string $end): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TimeRange(CarbonImmutable::parse($start), CarbonImmutable::parse($end));
    }

    /** @return array<string, array{string, string}> */
    public static function invalidIntervals(): array
    {
        return [
            'equal endpoints' => ['2026-08-20T09:00:00+00:00', '2026-08-20T09:00:00+00:00'],
            'end before start' => ['2026-08-20T10:00:00+00:00', '2026-08-20T09:00:00+00:00'],
        ];
    }
}
