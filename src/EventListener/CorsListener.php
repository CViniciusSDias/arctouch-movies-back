<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CorsListener
{
    /** @var ParameterBagInterface */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ('OPTIONS' === $event->getRequest()->getMethod()) {
            $event->setResponse($this->addAccessControlAllowOriginToResponse(new Response()));
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        $event->setResponse($this->addAccessControlAllowOriginToResponse($response));
    }

    private function addAccessControlAllowOriginToResponse(Response $response): Response
    {
        $clientUrl = $this->parameterBag->get('client_url');
        $response->headers->set('Access-Control-Allow-Origin', $clientUrl);

        return $response;
    }
}
