# php-levin
[WIP]

```php
use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Denpa\Levin\Requests\SupportFlags;

$connection = new Connection($ip, $port);
$connection->write(Bucket::request()->handshake());

while ($bucket = $connection->read(new Bucket())) {
    if ($bucket->getCommand() instanceof SupportFlags) {
        // respond to support flags request
        Bucket::response()->supportflags()->writeTo($fp);
    }

    var_dump($bucket->payload());
}
```

Loosely based on [py-levin](https://github.com/xmrdsc/py-levin).
