<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\TokenHelper;
use App\Helpers\MockDataHelper;
use App\Helpers\Logger;
use App\Core\Request;
use App\Core\Response;

/**
 * Webhook Controller for handling external webhook events.
 */
class WebhookController
{
  /**
   * Handle events webhook endpoint.
   *
   * @param Request $req
   * @param Response $res
   */
  public function event(Request $req, Response $res): void
  {
    $data = $req->json();
    if (!$data) {
      $res->status(400)->json([
        'code' => 'error',
        'message' => 'Invalid JSON payload'
      ]);
      return;
    }

    // Log generic webhook
    Logger::info('Webhook Event: generic', $data, 'webhooks');
    $res->json(MockDataHelper::apiResponse([
      'received' => true,
      'timestamp' => date('Y-m-d H:i:s'),
      'data' => $data
    ], 'Webhook received successfully'));
  }
}
