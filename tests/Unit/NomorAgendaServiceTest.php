<?php

namespace Tests\Unit;

use App\Services\NomorAgendaService;
use Tests\TestCase;

class NomorAgendaServiceTest extends TestCase
{
    protected NomorAgendaService $agendaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agendaService = new NomorAgendaService();
    }

    /**
     * Test preview returns valid integer agenda number.
     */
    public function test_preview_returns_positive_integer(): void
    {
        $preview = $this->agendaService->preview('inbox', true, 1, 2026);

        $this->assertIsInt($preview);
        $this->assertGreaterThanOrEqual(1, $preview);
    }

    /**
     * Test preview for outbox type.
     */
    public function test_preview_outbox_returns_positive_integer(): void
    {
        $preview = $this->agendaService->preview('outbox', false, 2, 2026);

        $this->assertIsInt($preview);
        $this->assertGreaterThanOrEqual(1, $preview);
    }
}
