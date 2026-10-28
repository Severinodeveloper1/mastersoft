<?php

namespace Tests\Feature\Domain;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ClaimTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_claim_numbers_are_sequential_per_year(): void
    {
        Carbon::setTestNow('2026-12-30 10:00:00');
        $first = Claim::factory()->create();
        $second = Claim::factory()->create();

        Carbon::setTestNow('2027-01-04 10:00:00');
        $nextYear = Claim::factory()->create();

        $this->assertSame('000001-2026', $first->code);
        $this->assertSame('000002-2026', $second->code);
        $this->assertSame('000001-2027', $nextYear->code);
        $this->assertSame(ClaimStatus::Received, $first->fresh()->status);
    }

    public function test_response_is_due_fifteen_business_days_after_filing(): void
    {
        // Wednesday 2026-10-28 + 15 business days (weekends skipped) = Wednesday 2026-11-18.
        Carbon::setTestNow('2026-10-28 13:40:00');

        $claim = Claim::factory()->create();

        $this->assertSame('2026-11-18', $claim->response_due_on->toDateString());
    }

    public function test_claim_filed_on_a_saturday_counts_from_the_next_business_day(): void
    {
        // Saturday 2026-10-31: business days start Monday 2026-11-02, so day 15 is Friday 2026-11-20.
        Carbon::setTestNow('2026-10-31 09:00:00');

        $claim = Claim::factory()->create();

        $this->assertSame('2026-11-20', $claim->response_due_on->toDateString());
    }
}
