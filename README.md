### Simple PHP event manager package

PubSub Event is a PHP package that provides a flexible and efficient way to manage events and listeners using the Publish-Subscribe (PubSub) pattern. It simplifies event handling and distribution within your system, offering methods to control event execution in an intuitive and dynamic way.

### Example
```php
<?php

require __DIR__. "/vendor/autoload.php";

use App\Events\UserCreatedEvent;
use App\Models\UserModel;

$user = new UserModel("Serge Gogo", "serge@gmail.com");
event(new UserCreatedEvent($user))
  ->dispatchIf(fn($event) => $event->user->name !== null);
```

##Features

The package includes the following main methods:

- dispatchIf($condition): Dispatches an event only if the specified condition is met. This allows events to be triggered conditionally.

- dispatchOnly($listener): Similar to dispatchIf, but ensures the event is dispatched only one listener in the event listeners array.

- ignoreIf($condition, $listener): Prevents an event from being dispatched if the specified condition is met. Useful for blocking certain events in specific scenarios.
