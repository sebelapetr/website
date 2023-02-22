<?php

declare(strict_types=1);

namespace App\Api\V1\Controllers;

use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\ErrorHandler\SimpleErrorHandler;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;
use App\Api\Exceptions\SimpleApiException;
use App\Model\Orm;
use function GuzzleHttp\Psr7\stream_for;
use Nette\Utils\Json;
use Tracy\Debugger;

/**
 * @Path("/api/v1")
 */
abstract class BaseV1Controller implements IController
{
	protected Orm $orm;

	public function __construct(Orm $orm)
	{
		$this->orm = $orm;
		Debugger::$showBar = FALSE;
	}

	/**
	 * Show tracy bar in response (only available for testing)
	 */
	protected function showTracyBar(): void
	{
		Debugger::$showBar = TRUE;
	}

	public function throwErrorResponse(ApiResponse $response, string $status, int $code, string $message = NULL ): ApiResponse
    {
        $data = [
            'status' => $status,
            'code' => $code
        ];
        if ($message)
        {
            $data['message'] = $message;
        }
        $body = stream_for(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | (defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0)));
        return $response
            ->withStatus($code)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);
    }
}