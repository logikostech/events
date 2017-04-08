<?php

namespace Logikos\Tests;


use Logikos\Events\EventsAwareInterface;
use Logikos\Events\EventsAwareTrait;

class EventUserWithPrefixConstant implements EventsAwareInterface {
  use EventsAwareTrait;

  const EVENT_PREFIX = 'EU';
}