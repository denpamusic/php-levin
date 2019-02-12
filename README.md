# php-levin
[WIP]

```php
use Denpa\Levin\Bucket;
use Denpa\Levin\Requests\SupportFlags;

$fp = fsockopen($ip, $port);

if ($fp) {
    Bucket::request()->handshake()->writeTo($fp);

    while($bucket = Bucket::readFrom($fp)) {
        if ($bucket->getCommand() instanceof SupportFlags) {
            // respond to support flags request
            Bucket::response()->supportflags()->writeTo($fp);
        }

        var_dump($bucket);
    }

    fclose($fp);
}
```

Loosely based on [py-levin](https://github.com/xmrdsc/py-levin).
