<?php


namespace Drupal\inversiones\EventSubscriber;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AddHTTPHeaders implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
      $events[KernelEvents::RESPONSE][] = ['onRespond', -100];
      return $events;
    }
    //set the header only if the actuall content type response is application/json
    public function onRespond(FilterResponseEvent $event) {
      $response = $event->getResponse();
      $headers = $response->headers;
      $header_content_type = $headers->get('content-type');
      if($header_content_type == "application/json") {
        $response->headers->set('content-type', 'application/json; charset=UTF-8');
      }
    }
}
