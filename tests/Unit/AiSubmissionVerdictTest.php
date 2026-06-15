<?php

namespace Tests\Unit;

use App\Services\AiModerationService;
use PHPUnit\Framework\TestCase;

class AiSubmissionVerdictTest extends TestCase
{
    public function test_allow_verdict_is_allowed_without_reason(): void
    {
        $result = AiModerationService::parseSubmissionVerdict("VERDICT: allow\nREASON:");

        $this->assertTrue($result['allowed']);
        $this->assertSame('', $result['reason']);
    }

    public function test_block_verdict_with_reason(): void
    {
        $result = AiModerationService::parseSubmissionVerdict("VERDICT: block\nREASON: Объявление содержит БДСМ-контент");

        $this->assertFalse($result['allowed']);
        $this->assertSame('Объявление содержит БДСМ-контент', $result['reason']);
    }

    public function test_block_verdict_without_reason_uses_default(): void
    {
        $result = AiModerationService::parseSubmissionVerdict('VERDICT: block');

        $this->assertFalse($result['allowed']);
        $this->assertSame('Объявление нарушает правила сайта.', $result['reason']);
    }

    public function test_garbage_response_is_allowed_fail_open(): void
    {
        $result = AiModerationService::parseSubmissionVerdict('тут какой-то мусор без формата');

        $this->assertTrue($result['allowed']);
        $this->assertSame('', $result['reason']);
    }

    public function test_empty_response_is_allowed_fail_open(): void
    {
        $result = AiModerationService::parseSubmissionVerdict('');

        $this->assertTrue($result['allowed']);
        $this->assertSame('', $result['reason']);
    }
}
