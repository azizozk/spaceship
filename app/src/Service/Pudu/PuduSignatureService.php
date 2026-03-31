<?php

namespace App\Service\Pudu;

/**
 * Handles Pudu Open Platform HMAC-SHA1 signature computation.
 *
 * Signing string format (fields joined by "\n"):
 *   x-date: {dateTime}
 *   {HTTPMethod}
 *   {Accept}
 *   {Content-Type}
 *   {Content-MD5}
 *   {PathAndParameters}
 */
class PuduSignatureService
{
    /**
     * Builds the signing string from request components.
     */
    public function buildSigningString(
        string $method,
        string $pathAndParameters,
        string $dateTime,
        string $contentMd5 = '',
        string $accept = 'application/json',
        string $contentType = 'application/json',
    ): string {
        return implode("\n", [
            "x-date: {$dateTime}",
            strtoupper($method),
            $accept,
            $contentType,
            $contentMd5,
            $pathAndParameters,
        ]);
    }

    /**
     * Computes the HMAC-SHA1 signature, Base64-encoded.
     */
    public function computeSignature(string $signingString, string $apiAppSecret): string
    {
        return base64_encode(hash_hmac('sha1', $signingString, $apiAppSecret, true));
    }

    /**
     * Builds the Authorization header value.
     */
    public function buildAuthorizationHeader(string $apiAppKey, string $signature): string
    {
        return sprintf(
            'hmac id="%s", algorithm="hmac-sha1", headers="x-date", signature="%s"',
            $apiAppKey,
            $signature,
        );
    }

    /**
     * Computes Content-MD5: Base64 of the hex-encoded MD5 of the body.
     * Matches the JS reference implementation.
     */
    public function computeContentMd5(string $body): string
    {
        return base64_encode(md5($body));
    }

    /**
     * Sorts query/body params lexicographically and serializes them.
     * Used for the signing string (values are NOT URL-encoded).
     *
     * Rules:
     * - Array values are sorted and joined with commas
     * - Empty/null values: key only (no equals sign)
     */
    public function sortParams(array $params): string
    {
        ksort($params);
        $parts = [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                sort($value);
                $parts[] = empty($value)
                    ? $key
                    : $key . '=' . implode(',', $value);
            } elseif ($value === null || $value === '') {
                $parts[] = $key;
            } else {
                $parts[] = $key . '=' . $value;
            }
        }

        return implode('&', $parts);
    }

    /**
     * Sorts and URL-encodes params for the actual request URL (GET requests).
     */
    public function sortParamsEncoded(array $params): string
    {
        ksort($params);
        $parts = [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $encoded = array_map('urlencode', array_map('strval', $value));
                sort($encoded);
                $parts[] = empty($encoded)
                    ? $key
                    : $key . '=' . implode(',', $encoded);
            } elseif ($value === null || $value === '') {
                $parts[] = $key;
            } else {
                $parts[] = $key . '=' . urlencode((string) $value);
            }
        }

        return implode('&', $parts);
    }
}
