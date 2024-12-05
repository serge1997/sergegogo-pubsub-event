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

## Features

The package includes the following main methods:

- dispatchIf($condition): Dispatches an event only if the specified condition is met. This allows events to be triggered conditionally.

- dispatchOnly($listener): Similar to dispatchIf, but ensures the event is dispatched only one listener in the event listeners array.

- ignoreIf($condition, $listener): Prevents an event from being dispatched if the specified condition is met. Useful for blocking certain events in specific scenarios.

## Installation

To install the package, use composer:
```
composer require sergegogo/pubsub-event

```

## Example Usage
Create your own class and extends ### SergeGogoEvent\Provider\PubSubEventProvider
#### 1. EventServiceProvider.php. after this we must create a event 
UserCreatedEvent and listener SendEmailListener
```php
<?php
namespace App\Providers;

use App\Events\UserCreatedEvent;
use App\Listeners\SendEmailListener;
use SergeGogoEvent\Provider\PubSubEventProvider;

class EventServiceProvider extends PubSubEventProvider
{
    protected $suscribers = [
        UserCreatedEvent::class => [
            SendEmailListener::class,
            UserCreatedListener::class
            //regist more listener here
        ]
    ];

    public function __construct($event)
    {
        parent::__construct($event);
    }
}
```
#### 2. create your Event (UserCreatedEvent.php) and pass data to the construct magic method

```php
<?php
namespace App\Events;

use App\Models\UserModel;
use SergeGogoEvent\Event\PubSubEvent;

class UserCreatedEvent extends PubSubEvent
{
    public function __construct(public UserModel $user)
    {
        parent::__construct();
    }
}
```
### 3. create your listener(s) SendEmailListener for the event.
```php

<?php
namespace App\Listeners;

use App\Events\UserCreatedEvent;

#1st listener
class SendEmailListener
{

    public function handle(UserCreatedEvent $event)
    {
        echo "Send email listener dispatched successfully with data: " . json_encode($event);
    }
}

#2nd listener
<?php
namespace  App\Listeners;

use GoEvent\App\Events\UserCreatedEvent;

class UserCreatedListener
{

    public function handle(UserCreatedEvent $event)
    {
        echo "User created listener dispatched successfully with data: " . json_encode($event->user) . PHP_EOL;
    }
}
```
### 4. Finally create a Global function
```php
use App\Providers\EventServiceProvider;

if (!function_exists('event')){
    function event($event){
        return new EventServiceProvider($event);
    }
}
```

```php
<?php
$user = new UserModel("Serge Gogo", "serge@gmail.com");
event(new UserCreatedEvent($user))
    ->dispatchIf(fn($event) => $event->user->name !== null);

//OUTPUT: Send email listener dispatched successfully with data:  {"user":{"name":"Serge Gogo","email":"serge@gmail.com"}},
//User created listener dispatched successfully with data: {"name":"Serge Gogo","email":"serge@gmail.com"}
```
The dispatchIf() with callback method has access to all event parameters
