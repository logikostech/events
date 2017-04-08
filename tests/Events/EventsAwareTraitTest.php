<?php

namespace Logikos\Tests\Events;

use Logikos\Events\EventsAwareTrait;
use Logikos\Events\EventsAwareInterface;
use Logikos\Tests\EventUserWithPrefixConstant;
use Phalcon\Di;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Events\ManagerInterface as EventsManagerInterface;

class EventsAwareTraitTest extends \PHPUnit_Framework_TestCase implements EventsAwareInterface {
  use EventsAwareTrait;

  private $eventData;


  public function testCanSetAndGetEventsManager() {
    $em = new EventsManager();
    $this->setEventsManager($em);
    $this->assertSame($em, $this->getEventsManager());
  }

  public function testWhenEventPrefixNotSet_ThenUsesShortClassName() {
    $expected = strtolower((new \ReflectionClass($this))->getShortName());
    $this->assertSame($expected, $this->getEventPrefix());
  }

  public function testCanSetEventPrefix() {
    $prefix = uniqid();
    $this->useEventPrefix($prefix); // protected method, can not be used from outside
    $this->assertSame($prefix, $this->getEventPrefix());
  }

  public function testUsesEventPrefixConstantWhenDefined() {
    $foo = new EventUserWithPrefixConstant();
    $expected = EventUserWithPrefixConstant::EVENT_PREFIX;
    $this->assertSame($expected, $foo->getEventPrefix());
  }

  public function testAttachEventWillCreateNewEventsManagerIfNoneSet() {
    $this->assertNull($this->getEventsManager());
    $this->attachEventListener('foo', function(){});
    $this->assertInstanceOf(
        EventsManagerInterface::class,
        $this->getEventsManager()
    );
  }

  public function testAttachEventWillUseEventsManagerFromDi() {
    $this->assertNull($this->getEventsManager());
    $di = new Di();
    $di->setShared(
        'eventsManager',
        function(){
          $em = new EventsManager();
          $em->foo = 'bar';
          return $em;
        }
    );
    Di::setDefault($di);
    $this->attachEventListener('foo', function(){});
    $this->assertEquals('bar', $this->getEventsManager()->foo);
  }

  public function testCanAttachEvent() {
    $eventType = 'beforefoo';
    $this->attachEventListener($eventType, function(Event $event, $component, $data=null) {
      $component->eventData = [
          'type' => $event->getType(),
          'data' => $data
      ];
    });

    $data = ['foo', 'bar'];
    $this->eventData = null;
    $this->fireEvent($eventType, $data);
    $this->assertSame($eventType, $this->eventData['type']);
    $this->assertSame($data,      $this->eventData['data']);
  }

  public function testCanAttachNamedEvent() {
    $eventType = 'beforefoo';
    $this->attachEventListener($eventType, function(Event $event, $component, $data=null) {
      $component->eventData = $data;
    });

    $data = ['foo', 'bar'];
    $this->eventData = null;
    $this->fireEvent($eventType, $data);
    $this->assertSame($data, $this->eventData);
  }

}