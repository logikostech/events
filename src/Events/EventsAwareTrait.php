<?php

namespace Logikos\Events;

use Phalcon\Di;
use Phalcon\Events\Manager;
use Phalcon\Events\ManagerInterface;

trait EventsAwareTrait {
  /** @var  ManagerInterface */
  private $eventsManager;
  private $_eventPrefix;

  /**
   * Sets the events manager
   *
   * @param mixed $eventsManager
   */
  public function setEventsManager(ManagerInterface $eventsManager) {
    $this->eventsManager = $eventsManager;
  }

  /**
   * Returns the internal event manager
   *
   * @return ManagerInterface
   */
  public function getEventsManager() {
    return $this->eventsManager;
  }


  protected function useEventPrefix($prefix) {
    $this->_eventPrefix = $prefix;
  }

  /**
   * Prefix uses static::EVENT_PREFIX if defined
   * else it uses static::class without the namespace
   * @return string
   */
  public function getEventPrefix() {
    if ($this->_eventPrefix)
      return $this->_eventPrefix;

    elseif (defined(static::class.'::EVENT_PREFIX'))
      return static::EVENT_PREFIX;

    else
      return $this->_eventPrefix =strtolower(array_slice(explode('\\', static::class), -1)[0]);
  }


  /**
   * Attach an event listener, if no event manager has been setup it will set one up for you.
   * @param string $eventType leave blank (null, false, '') to place a listener for all events or specify the event you want
   * @param object|callable $handler
   */
  public function attachEventListener($eventType, $handler) {
    if (!$eventType)
      $eventType = $this->getEventPrefix();

    elseif (!$this->isAlreadyPrefixed($eventType))
      $eventType = $this->getEventPrefix().':'.$eventType;

    return $this->requireEventsManager()->attach($eventType, $handler);
  }

  private function isAlreadyPrefixed($eventType) {
    $prefix  = $this->getEventPrefix();
    $pattern = "/^{$prefix}(:.+)?$/";
    return preg_match($pattern, $eventType);
  }

  /**
   * @return ManagerInterface
   */
  protected function requireEventsManager() {
    if (!$this->getEventsManager())
      $this->setEventsManager(new Manager());

    if (Di::getDefault() && Di::getDefault()->has('eventsManager'))
      $this->setEventsManager(DI::getDefault()->get('eventsManager'));

    return $this->getEventsManager();
  }


  /**
   * Fire Event - checks if eventsmanager is setup, and if so it fires the event
   * if no prefix is used in $eventType it defaults to const EVENT_PREFIX
   * if no constant EVENT_PREFIX is defined it uses static::class without the namespace
   * @param string $eventType
   * @param mixed $data
   * @param boolean $cancelable
   * @return mixed this will return whatever the listener returns
   * @see \Phalcon\Events\Manager ::fire()
   */
  protected function fireEvent($eventType, $data = null, $cancelable = true) {
    if ($this->getEventsManager() instanceof ManagerInterface) {
      $prefix = strstr($eventType,':') ? '' : $this->getEventPrefix().':';
      return $this->getEventsManager()->fire(
          $prefix.$eventType,
          $this,
          $data,
          $cancelable
      );
    }
  }

}