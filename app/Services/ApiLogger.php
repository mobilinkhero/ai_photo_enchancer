<?php

namespace App\Services;

use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * ApiLogger — Call this from your enhance controller to log every step.
 *
 * Usage:
 *   $logger = ApiLogger::start($request, $userUid, $photoId);
 *   // ... call AI provider
 *   $logger->logAiRequest($provider, $endpoint, $payload);
 *   $logger->logAiResponse($statusCode, $responseBody, $timeMs, $model, $outputUrl);
 *   $logger->finish('success');          // or ->finish('error', 'message')
 */
class ApiLogger
{
    protected ApiLog $log;

    private function __construct(ApiLog $log)
    {
        $this->log = $log;
    }

    /**
     * Start a new log entry for an incoming request.
     */
    public static function start(Request $request, ?string $userUid = null, ?int $photoId = null): static
    {
        // Strip sensitive headers
        $headers = collect($request->headers->all())
            ->except(['cookie', 'authorization', 'x-api-key'])
            ->toArray();

        // Strip sensitive payload fields
        $payload = collect($request->except(['password', 'token', 'api_key', 'key']))
            ->toArray();

        $log = ApiLog::create([
            'request_id' => Str::uuid(),
            'client_ip' => $request->ip(),
            'client_endpoint' => $request->path(),
            'client_method' => $request->method(),
            'client_headers' => $headers,
            'client_payload' => $payload,
            'user_uid' => $userUid,
            'photo_id' => $photoId,
            'status' => 'pending',
        ]);

        return new static($log);
    }

    /**
     * Log what we're sending to the AI provider.
     */
    public function logAiRequest(string $provider, string $endpoint, array $payload): static
    {
        $this->log->update([
            'ai_provider' => $provider,
            'ai_endpoint' => $endpoint,
            'ai_request_payload' => $payload,
            'ai_request_size_bytes' => strlen(json_encode($payload)),
        ]);
        return $this;
    }

    /**
     * Log what the AI provider sent back.
     */
    public function logAiResponse(
        int $statusCode,
        mixed $responseBody,
        float $timeMs,
        ?string $model = null,
        ?string $outputUrl = null
    ): static {
        $body = is_array($responseBody) ? $responseBody : ['raw' => (string) $responseBody];

        $this->log->update([
            'ai_response_status' => $statusCode,
            'ai_response_body' => $body,
            'ai_response_time_ms' => $timeMs,
            'ai_model' => $model,
            'ai_output_url' => $outputUrl,
        ]);
        return $this;
    }

    /**
     * Finalize the log with success or error status.
     */
    public function finish(string $status, ?string $errorMessage = null): ApiLog
    {
        $startedAt = $this->log->created_at->getPreciseTimestamp(3);
        $totalMs = microtime(true) * 1000 - $startedAt;

        $this->log->update([
            'status' => $status,
            'error_message' => $errorMessage,
            'total_time_ms' => round(max(0, $totalMs)),
        ]);

        return $this->log;
    }

    public function getLog(): ApiLog
    {
        return $this->log;
    }

    public function getRequestId(): string
    {
        return $this->log->request_id;
    }
}
