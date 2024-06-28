<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Custom exception listener to handle exceptions and format error responses consistently.
 */

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Customize the error response format
        $errorData = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];

        // Handle specific exception types
        if ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse($errorData, $exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            // For any other exception, return a 500 Internal Server Error
            $errorData['message'] = 'An unexpected error occurred';
            $response = new JsonResponse($errorData, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Set the content type to application/problem+json for better API error reporting
        $response->headers->set('Content-Type', 'application/problem+json');

        $event->setResponse($response);
    }
}
