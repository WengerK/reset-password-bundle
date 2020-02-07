<?php

namespace SymfonyCasts\Bundle\ResetPassword\tests\UnitTests\Generator;

use SymfonyCasts\Bundle\ResetPassword\Generator\ResetPasswordTokenGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
class ResetPasswordTokenGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function hashDataEncodesToJson(): void
    {
        //@todo refactor or remove
        $this->markTestSkipped('encodeHashData is private.');
        $mockDateTime = $this->createMock(\DateTimeImmutable::class);
        $mockDateTime
            ->expects($this->once())
            ->method('format')
            ->willReturn('2020')
        ;

        $result = $this->fixture->getEncodeHashedDataProtected($mockDateTime, 'verify', '1234');
        self::assertJson($result);
    }

    /**
     * @test
     */
    public function hashDataEncodesWithProvidedParams(): void
    {
        //@todo refactor or remove
        $this->markTestSkipped('encodeHashData is private.');
        $mockDateTime = $this->createMock(\DateTimeImmutable::class);
        $mockDateTime
            ->method('format')
            ->willReturn('2020')
        ;

        $result = $this->fixture->getEncodeHashedDataProtected($mockDateTime, 'verify', '1234');
        self::assertJsonStringEqualsJsonString(
        '["verify", "1234", "2020"]',
            $result
        );
    }

    /**
     * @test
     */
    public function returnsHmacHashedToken(): void
    {
        $mockExpiresAt = $this->createMock(\DateTimeImmutable::class);
        $mockExpiresAt
            ->expects($this->once())
            ->method('format')
            ->with('Y-m-d\TH:i:s')
            ->willReturn('2020')
        ;

        $signingKey = 'abcd';
        $verifier = 'verify';
        $userId = '1234';

        $generator = new ResetPasswordTokenGenerator();
        $result = $generator->getToken($signingKey, $mockExpiresAt, $verifier, $userId);

        $expected = \hash_hmac(
            'sha256',
            \json_encode([$verifier, $userId, '2020']),
            $signingKey
        );

        self::assertSame($expected, $result);
    }
}
