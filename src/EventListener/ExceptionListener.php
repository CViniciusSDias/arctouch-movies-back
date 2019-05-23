<?php

namespace App\EventListener;

use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        switch (get_class($exception)) {
            case ClientException::class:
                /** @var ClientException $exception */
                $exceptionResponse = $exception->getResponse();

                $response = new Response(
                    $exceptionResponse->getBody(),
                    $exceptionResponse->getStatusCode(),
                    $exceptionResponse->getHeaders()
                );
                break;
            case \Exception::class:
                /** @var \Exception $exception */
                $errorCode = $exception->getCode() >= 200 && $exception->getCode() <= 500
                    ? $exception->getCode()
                    : 500;

                $response = new JsonResponse([
                    'status_code' => $errorCode,
                    'status_message' => $exception->getMessage()
                ], $errorCode);
                break;
            case \Error::class:
            default:
                /** @var \Error $exception */
                error_log($exception->getMessage());

                $response = new JsonResponse([
                    'status_code' => 500,
                    'status_message' => 'Unexpected Error',
                ]);
        }

        $event->setResponse($response);
    }
}
