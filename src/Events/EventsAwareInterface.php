<?php

namespace Logikos\Events;

interface EventsAwareInterface extends \Phalcon\Events\EventsAwareInterface {
  public function getEventPrefix();

  /**
   * Attach an event listener, if no event manager has been setup it will set one up for you.
   * @param string $eventType leave blank (null, false, '') to place a listener for all events or specify the event you want
   * @param object|callable $handler
   */
  public function attachEventListener($eventType, $handler);
}