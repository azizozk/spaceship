<?php

namespace App\Service\Pudu;

use App\Exception\PuduApiException;
use App\Service\Pudu\Dto\CancelCallTaskRequest;
use App\Service\Pudu\Dto\CancelCallTaskResponse;
use App\Service\Pudu\Dto\CleanRobotDetail;
use App\Service\Pudu\Dto\CompleteCallTaskRequest;
use App\Service\Pudu\Dto\CompleteCallTaskResponse;
use App\Service\Pudu\Dto\CurrentTaskStatusResponse;
use App\Service\Pudu\Dto\GetCurrentMapResponse;
use App\Service\Pudu\Dto\InitiateCallTaskRequest;
use App\Service\Pudu\Dto\InitiateCallTaskResponse;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * HTTP client for the Pudu Open Platform Cloud API.
 *
 * Authentication: HMAC-SHA1 application credential signing.
 * Every request is signed with x-date and an Authorization header.
 */
class PuduApiClient
{
    private const ACCEPT = 'application/json';
    private const CONTENT_TYPE = 'application/json';

    public function __construct(
        #[Autowire(env: 'PUDU_API_KEY')]
        private readonly string $apiAppKey,

        #[Autowire(env: 'PUDU_API_SECRET')]
        private readonly string $apiAppSecret,

        #[Autowire(env: 'PUDU_API_HOST')]
        private readonly string $hostname,

        private readonly PuduSignatureService $signatureService,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Sends a signed GET request to the Pudu API.
     *
     * @param array<string, mixed> $params Query parameters
     *
     * @return array<mixed>
     *
     * @throws PuduApiException
     */
    public function get(string $path, array $params = []): array
    {
        $dateTime = $this->currentDateTime();

        // Build URL query string (URL-encoded) and signing path (decoded)
        $encodedQuery = $this->signatureService->sortParamsEncoded($params);
        $decodedQuery = $this->signatureService->sortParams($params);

        $requestUrl = 'https://' . $this->hostname . $path
            . ('' !== $encodedQuery ? '?' . $encodedQuery : '');

        $signingPath = $path . ('' !== $decodedQuery ? '?' . $decodedQuery : '');

        $signingString = $this->signatureService->buildSigningString(
            'GET',
            $signingPath,
            $dateTime,
            '',
            self::ACCEPT,
            self::CONTENT_TYPE,
        );

        $authorization = $this->buildAuthorization($signingString);

        try {
            $response = $this->httpClient->request('GET', $requestUrl, [
                'headers' => [
                    'Accept' => self::ACCEPT,
                    'Content-Type' => self::CONTENT_TYPE,
                    'Content-MD5' => '',
                    'x-date' => $dateTime,
                    'Authorization' => $authorization,
                ],
            ]);

            return $response->toArray();
        } catch (\Throwable $e) {
            throw new PuduApiException(
                \sprintf('Pudu GET %s failed: %s', $path, $e->getMessage()),
                null,
                $e,
            );
        }
    }

    /**
     * Sends a signed POST request to the Pudu API.
     *
     * @param array<string, mixed> $body Request body as associative array (serialized to JSON)
     *
     * @return array<mixed>
     *
     * @throws PuduApiException
     */
    public function post(string $path, array $body = []): array
    {
        $dateTime = $this->currentDateTime();
        $bodyJson = json_encode($body, \JSON_THROW_ON_ERROR);
        $contentMd5 = $this->signatureService->computeContentMd5($bodyJson);

        $signingString = $this->signatureService->buildSigningString(
            'POST',
            $path,
            $dateTime,
            $contentMd5,
            self::ACCEPT,
            self::CONTENT_TYPE,
        );

        $authorization = $this->buildAuthorization($signingString);

        try {
            $response = $this->httpClient->request('POST', 'https://' . $this->hostname . $path, [
                'headers' => [
                    'Accept' => self::ACCEPT,
                    'Content-Type' => self::CONTENT_TYPE,
                    'Content-MD5' => $contentMd5,
                    'x-date' => $dateTime,
                    'Authorization' => $authorization,
                ],
                'body' => $bodyJson,
            ]);

            return $response->toArray();
        } catch (\Throwable $e) {
            throw new PuduApiException(
                \sprintf('Pudu POST %s failed: %s', $path, $e->getMessage()),
                null,
                $e,
            );
        }
    }

    // -------------------------------------------------------------------------
    // Built-in API endpoints
    // -------------------------------------------------------------------------

    /**
     * Health check endpoint.
     *
     * @return array<mixed>
     */
    public function healthCheck(): array
    {
        return $this->get('/pudu-entry/data-open-platform-service/v1/api/healthCheck');
    }

    /**
     * Returns the current map name and a paginated list of its waypoints for a robot.
     *
     * Use $limit / $offset to page through all points when the map has many waypoints.
     * Point names returned here match the `point` parameter accepted by InitiateCallTask.
     *
     * @throws PuduApiException on HTTP or API-level failure
     */
    public function getCurrentMap(string $sn, int $limit = 10, int $offset = 0): GetCurrentMapResponse
    {
        $path = '/pudu-entry/map-service/v1/open/point';
        $raw = $this->get($path, ['sn' => $sn, 'limit' => $limit, 'offset' => $offset]);

        if (($raw['message'] ?? '') !== 'SUCCESS') {
            throw new PuduApiException(
                \sprintf('GetCurrentMap failed: %s', $raw['message'] ?? 'unknown error'),
            );
        }

        return GetCurrentMapResponse::fromArray($raw['data']);
    }

    /**
     * Returns full status details for a CleanBot robot (CC1, CC1 Pro, MT1, MT1 Vac, MT1 Max).
     *
     * Includes online status, battery level, current map, task status, position,
     * water levels, and cleaning task progress.
     *
     * @throws PuduApiException on HTTP or API-level failure
     */
    public function getCleanRobotDetail(string $sn): CleanRobotDetail
    {
        $path = '/cleanbot-service/v1/api/open/robot/detail';
        $raw = $this->get($path, ['sn' => $sn]);

        if (($raw['message'] ?? '') !== 'SUCCESS') {
            throw new PuduApiException(
                \sprintf('GetCleanRobotStatusDetail failed: %s', $raw['message'] ?? 'unknown error'),
            );
        }

        return CleanRobotDetail::fromArray($raw['data']);
    }

    /**
     * Returns the current task status for a FlashBot robot.
     *
     * Legacy interface compatible with the SDK microservice.
     * Not supported by newer Pudu models.
     *
     * @throws PuduApiException on HTTP or API-level failure
     */
    public function getCurrentTaskStatus(string $sn): CurrentTaskStatusResponse
    {
        $path = '/open-platform-service/v1/robot/task/state/get';
        $raw = $this->get($path, ['sn' => $sn]);

        if (($raw['message'] ?? '') !== 'SUCCESS') {
            throw new PuduApiException(
                \sprintf('GetCurrentTaskStatus failed: %s', $raw['message'] ?? 'unknown error'),
            );
        }

        return CurrentTaskStatusResponse::fromArray($raw['data']);
    }

    /**
     * Initiates a call task: dispatches a robot to a target point.
     *
     * Either $request->sn or $request->shopId must be set.
     * On success the response contains a task_id — cache it to:
     *   - track status via the notifyCustomCall-CallStatus callback
     *   - cancel via CancelCallTask
     *   - complete early via CompleteCallTask
     *
     * Tasks time out and fail 30 minutes after creation.
     *
     * @throws PuduApiException on HTTP or API-level failure
     */
    public function initiateCallTask(InitiateCallTaskRequest $request): InitiateCallTaskResponse
    {
        $path = '/pudu-entry/open-platform-service/v1/custom_call';
        $raw = $this->post($path, $request->toArray());

        if (($raw['message'] ?? '') !== 'SUCCESS') {
            throw new PuduApiException(
                \sprintf('InitiateCallTask failed: %s', $raw['message'] ?? 'unknown error'),
            );
        }

        return InitiateCallTaskResponse::fromArray($raw['data']);
    }

    /**
     * Cancels a custom call task.
     *
     * Provide $request->taskId to cancel a specific task, or $request->sn to
     * cancel all unfinished tasks for that robot. The canceling call must use
     * the same APPKEY that initiated the task.
     *
     * @throws PuduApiException on HTTP or API-level failure
     */
    public function cancelCallTask(CancelCallTaskRequest $request): CancelCallTaskResponse
    {
        $path = '/pudu-entry/open-platform-service/v1/custom_call/cancel';
        $raw = $this->post($path, $request->toArray());

        if (($raw['message'] ?? '') !== 'SUCCESS') {
            throw new PuduApiException(
                \sprintf('CancelCallTask failed: %s', $raw['message'] ?? 'unknown error'),
            );
        }

        return CancelCallTaskResponse::fromArray($raw);
    }

    /**
     * Completes an in-progress custom call task and optionally chains the next one.
     *
     * Only applicable when the task is NOT in auto-completion mode.
     * The completing call must use the same APPKEY that initiated the task.
     * If $request->nextCallTask is set, the robot is dispatched to the next
     * point immediately; the response includes that task's ID.
     *
     * @throws PuduApiException on HTTP or API-level failure
     */
    public function completeCallTask(CompleteCallTaskRequest $request): CompleteCallTaskResponse
    {
        $path = '/pudu-entry/open-platform-service/v1/custom_call/complete';
        $raw = $this->post($path, $request->toArray());

        if (($raw['message'] ?? '') !== 'SUCCESS') {
            throw new PuduApiException(
                \sprintf('CompleteCallTask failed: %s', $raw['message'] ?? 'unknown error'),
            );
        }

        return CompleteCallTaskResponse::fromArray($raw);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function buildAuthorization(string $signingString): string
    {
        $signature = $this->signatureService->computeSignature($signingString, $this->apiAppSecret);

        return $this->signatureService->buildAuthorizationHeader($this->apiAppKey, $signature);
    }

    private function currentDateTime(): string
    {
        return gmdate('D, d M Y H:i:s') . ' GMT';
    }
}
