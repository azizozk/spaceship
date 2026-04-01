<?php

namespace App\Tests\Service\Pudu;

use App\Service\Pudu\PuduSignatureService;
use PHPUnit\Framework\TestCase;

class PuduSignatureServiceTest extends TestCase
{
    private PuduSignatureService $service;

    protected function setUp(): void
    {
        $this->service = new PuduSignatureService();
    }

    public function testBuildSigningStringFormat(): void
    {
        $result = $this->service->buildSigningString(
            'GET',
            '/some/path?foo=bar',
            'Tue, 01 Apr 2025 12:00:00 GMT',
        );

        $expected = implode("\n", [
            'x-date: Tue, 01 Apr 2025 12:00:00 GMT',
            'GET',
            'application/json',
            'application/json',
            '',
            '/some/path?foo=bar',
        ]);

        self::assertSame($expected, $result);
    }

    public function testBuildSigningStringNormalizesMethodToUppercase(): void
    {
        $lower = $this->service->buildSigningString('post', '/path', 'date');
        $upper = $this->service->buildSigningString('POST', '/path', 'date');

        self::assertSame($lower, $upper);
    }

    public function testComputeSignatureIsBase64HmacSha1(): void
    {
        $signingString = "x-date: Wed, 01 Jan 2025 00:00:00 GMT\nGET\napplication/json\napplication/json\n\n/path";
        $secret = 'my-secret';

        $expected = base64_encode(hash_hmac('sha1', $signingString, $secret, true));

        self::assertSame($expected, $this->service->computeSignature($signingString, $secret));
    }

    public function testBuildAuthorizationHeaderFormat(): void
    {
        $header = $this->service->buildAuthorizationHeader('my-key', 'abc123==');

        self::assertSame(
            'hmac id="my-key", algorithm="hmac-sha1", headers="x-date", signature="abc123=="',
            $header,
        );
    }

    public function testComputeContentMd5IsBase64OfHexMd5(): void
    {
        $body = '{"foo":"bar"}';
        $expected = base64_encode(md5($body));

        self::assertSame($expected, $this->service->computeContentMd5($body));
    }

    public function testComputeContentMd5EmptyBody(): void
    {
        self::assertSame(base64_encode(md5('')), $this->service->computeContentMd5(''));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sortParamsProvider')]
    public function testSortParams(array $input, string $expected): void
    {
        self::assertSame($expected, $this->service->sortParams($input));
    }

    public static function sortParamsProvider(): array
    {
        return [
            'alphabetical order' => [
                ['z' => '1', 'a' => '2'],
                'a=2&z=1',
            ],
            'null value omits equals' => [
                ['key' => null],
                'key',
            ],
            'empty string omits equals' => [
                ['key' => ''],
                'key',
            ],
            'array values sorted and joined' => [
                ['ids' => ['3', '1', '2']],
                'ids=1,2,3',
            ],
            'empty array omits equals' => [
                ['ids' => []],
                'ids',
            ],
            'mixed params' => [
                ['sn' => 'ABC', 'limit' => 10, 'offset' => 0],
                'limit=10&offset=0&sn=ABC',
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('sortParamsEncodedProvider')]
    public function testSortParamsEncoded(array $input, string $expected): void
    {
        self::assertSame($expected, $this->service->sortParamsEncoded($input));
    }

    public static function sortParamsEncodedProvider(): array
    {
        return [
            'special chars are url-encoded' => [
                ['q' => 'hello world'],
                'q=hello+world',
            ],
            'alphabetical order' => [
                ['z' => 'last', 'a' => 'first'],
                'a=first&z=last',
            ],
            'null omits equals' => [
                ['key' => null],
                'key',
            ],
        ];
    }
}
