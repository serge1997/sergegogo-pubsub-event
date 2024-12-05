# Simple PHP event manager package

### Example
```
<?php

require __DIR__. "/vendor/autoload.php";

use App\Events\UserCreatedEvent;
use App\Models\UserModel;

$user = new UserModel("Serge Gogo", "serge@gmail.com");
event(new UserCreatedEvent($user))
  ->dispatchIf(fn($event) => $event->user->name !== null);
```
