<?php

namespace App\Controller;

use App\Service\Pudu\Dto\CallStatusCallbackData;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Receives inbound webhook callbacks from the Pudu Open Platform.
 */
#[Route('/webhook/pudu', name: 'pudu_callback_')]
class PuduCallbackController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Handles the notifyCustomCall (Call Status) callback.
     *
     * Pudu POSTs a JSON body whenever the state of a dispatched call task changes.
     * This endpoint must respond with HTTP 200; any other status causes Pudu to retry.
     */
    #[Route('/call-status', name: 'call_status', methods: ['POST'])]
    public function callStatus(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload) || ($payload['callback_type'] ?? null) !== 'notifyCustomCall') {
            return $this->json(['status' => 'ignored'], Response::HTTP_OK);
        }

        if (!isset($payload['data']) || !is_array($payload['data'])) {
            $this->logger->warning('pudu.callback.call_status: missing data field', ['payload' => $payload]);

            return $this->json(['status' => 'error', 'message' => 'missing data'], Response::HTTP_OK);
        }

        try {
            $data = CallStatusCallbackData::fromArray($payload['data']);
        } catch (\Throwable $e) {
            $this->logger->error('pudu.callback.call_status: failed to parse data', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return $this->json(['status' => 'error', 'message' => 'invalid data'], Response::HTTP_OK);
        }

        $this->logger->info('pudu.callback.call_status', [
            'task_id' => $data->taskId,
            'sn'      => $data->sn,
            'state'   => $data->state->value,
            'point'   => $data->point,
            'queue'   => $data->queue,
        ]);

        // TODO: dispatch an event or message to handle the state change downstream

        return $this->json(['status' => 'ok'], Response::HTTP_OK);
    }
}
