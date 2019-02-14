# php-levin
[![Latest Stable Version](https://poser.pugx.org/denpa/php-levin/v/stable)](https://packagist.org/packages/denpa/php-levin)
[![License](https://poser.pugx.org/denpa/php-levin/license)](https://packagist.org/packages/denpa/php-levin)
[![Build Status](https://travis-ci.org/denpamusic/php-levin.svg)](https://travis-ci.org/denpamusic/php-levin)
[![Code Climate](https://codeclimate.com/github/denpamusic/php-levin/badges/gpa.svg)](https://codeclimate.com/github/denpamusic/php-levin)
[![Code Coverage](https://codeclimate.com/github/denpamusic/php-levin/badges/coverage.svg)](https://codeclimate.com/github/denpamusic/php-levin/coverage)

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
        $connection->write(Bucket::response()->supportflags());
    }

    var_dump($bucket->payload());
}
```

Loosely based on [py-levin](https://github.com/xmrdsc/py-levin).
