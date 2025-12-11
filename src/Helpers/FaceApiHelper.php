<?php

declare(strict_types=1);

namespace App\Helpers;

use GuzzleHttp\Client;

/**
 * Face API Helper for interacting with face recognition API.
 */
class FaceApiHelper
{
  /**
   * Get Face API URL from environment.
   *
   * @return string
   */
  private static function getApiUrl(string $path): string
  {
    return (getenv('FACE_API_URL') ?: 'https://face.nafish.me/api') . $path;
  }

  /**
   * Get Face API Token from environment.
   *
   * @return string
   */
  private static function getApiToken(): string
  {
    return getenv('FACE_API_TOKEN') ?: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0eXBlIjoicmVzdCIsIm9yZyI6eyJjb2RlIjoiT1JHMSJ9LCJpYXQiOjE3NjQ5MTczODN9.o2HLf6_CVTGK6a4pkgcQd4rnFBoP8xfTXLj97MTuyn4';
  }

  /**
   * Generate client token from Face API.
   *
   * @return string|null
   */
  public static function generateToken(): ?string
  {
    try {
      $client = new Client();
      $response = $client->post(self::getApiUrl("/rest/client-token"), [
        'headers' => [
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . self::getApiToken()
        ],
        'json' => ['code' => 'ORG1']
      ]);
      $status = $response->getStatusCode();
      $body = $response->getBody()->getContents();
      $decoded = json_decode($body, true);

      if ($status === 200) {
        if (isset($decoded['result']['token'])) {
          return $decoded['result']['token'];
        }
      }
    } catch (\Exception $e) {
      throw $e;
    }
    return null;
  }

  public static function searchFaces(array $query): array
  {
    try {
      $client = new Client();
      $response = $client->post(self::getApiUrl('/rest/faces/search'), [
        'headers' => [
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . self::getApiToken(),
        ],
        'json' => ["query" => $query]
      ]);
      $status = $response->getStatusCode();
      $body = $response->getBody()->getContents();
      $decoded = json_decode($body, true);
      if ($status === 200) {
        if (isset($decoded['result']['records'])) {
          return $decoded['result']["records"];
        }
      }
    } catch (\Exception $e) {
      throw $e;
    }
    return [];
  }

  public static function deleteFace(string $faceId): bool
  {
    try {
      $client = new Client();
      $response = $client->delete(self::getApiUrl('/rest/faces/' . $faceId), [
        'headers' => [
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . self::getApiToken(),
        ]
      ]);
      return $response->getStatusCode() === 200;
    } catch (\Exception $e) {
      return false;
    }
  }
}
