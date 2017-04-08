[![Travis CI](https://img.shields.io/travis/logikostech/events/master.svg)](https://travis-ci.org/logikostech/events)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/logikostech/core/master/LICENSE)

# Logikos\Events\EventsAwareTrait
Useful trait when using Phalcon\Events\EventsAwareInterface.
Key feature is the attachEventListener method in that if an EventsManager is not yet being used by the component it will add one and then attach the event.  This way you don't have to setup your events manager when you setup the \Phalcon\Di services and each controller or library using the componenet can attach their own events as they see fit on a case by case basis.

## Usage

### Add to class
```php
class foo implements \Phalcon\Events\EventsAwareInterface {
  use \Logikos\Events\EventsAwareTrait;
}
```

### Add event handelers with ease
#### To listen for all events pass null
```php
$component->attachEventListener(null, function(\Phalcon\Events\Event $event, $component, $data=null) {
  switch ($event->getType()) {
    case 'beforeAction' :
      // ...
      break;
    case 'afterAction' :
      // ...
      break;
  }
});
```

#### Listen for specific event
```php
$component->attachEventListener('beforeAction', function(\Phalcon\Events\Event $event, $component, $data=null) {
  // ...
});
```
