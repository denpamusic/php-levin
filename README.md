# php-levin
[![Latest Stable Version](https://poser.pugx.org/denpa/php-levin/v/stable)](https://packagist.org/packages/denpa/php-levin)
[![License](https://poser.pugx.org/denpa/php-levin/license)](https://packagist.org/packages/denpa/php-levin)
[![Build Status](https://travis-ci.org/denpamusic/php-levin.svg)](https://travis-ci.org/denpamusic/php-levin)
[![Code Climate](https://codeclimate.com/github/denpamusic/php-levin/badges/gpa.svg)](https://codeclimate.com/github/denpamusic/php-levin)
[![Code Coverage](https://codeclimate.com/github/denpamusic/php-levin/badges/coverage.svg)](https://codeclimate.com/github/denpamusic/php-levin/coverage)

[WIP]

## Command Support
| command      | link                                                                                  | request | response |
|--------------|---------------------------------------------------------------------------------------|---------|----------|
| Handshake    | [p2p_protocol_defs.h#L177](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L177) | ✅       | ❌        |
| TimedSync    | [p2p_protocol_defs.h#L239](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L239) | ❌       | ❌        |
| Ping         | [p2p_protocol_defs.h#L297](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L297) | ✅       | ✅        |
| StatInfo     | [p2p_protocol_defs.h#L348](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L348) | ❌       | ❌        |
| NetworkState | [p2p_protocol_defs.h#L382](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L382) | ❌       | ❌        |
| PeerId       | [p2p_protocol_defs.h#L414](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L414) | ✅       | ✅        |
| SupportFlags | [p2p_protocol_defs.h#L437](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L437) | ✅       | ✅        |


## Notification Support
| command                | link                                                                                                         | request |
|------------------------|--------------------------------------------------------------------------------------------------------------|---------|
| NewBlock               | [cryptonote_protocol_defs.h#L126](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L126) | ❌       |
| RequestGetObjects      | [cryptonote_protocol_defs.h#L163](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L163) | ❌       |
| ResponseGetObjects     | [cryptonote_protocol_defs.h#L179](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L179) | ❌       |
| RequestChain           | [cryptonote_protocol_defs.h#L217](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L217) | ❌       |
| NewFluffyBlock         | [cryptonote_protocol_defs.h#L254](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L254) | ❌       |
| RequestFluffyMissingTx | [cryptonote_protocol_defs.h#L273](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L273) | ❌       |

## Example
```php
use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Denpa\Levin\Requests\Ping;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Requests\SupportFlags;

$connection = new Connection($ip, $port);
$connection->write(Bucket::request(new Handshake()));

while ($bucket = $connection->read(new Bucket())) {
    if ($bucket->getCommand() instanceof SupportFlags) {
        // respond to support flags request
        $connection->write(Bucket::response(new SupportFlags()));
    }

    if ($bucket->getCommand() instanceof Ping) {
        // respond to ping request
        $connection->write(Bucket::response(new Ping()));
    }

    var_dump($bucket->payload());
}
```

## Exceptions
* `Denpa\Levin\Exceptions\ConnectionException` - thrown on connection errors.
* `Denpa\Levin\Exceptions\SignatureMismatchException` - thrown on section or bucket signature mismatches.

## License
This product is distributed under the [MIT license](https://github.com/denpamusic/php-levin/blob/master/LICENSE).

## Credits
Loosely based on [py-levin](https://github.com/xmrdsc/py-levin).

## Donations
If you like this project, you can donate using one of the following addresses:

BTC:
3L6dqSBNgdpZan78KJtzoXEk9DN3sgEQJu

Monero:
458j3EKczYFEE1Gku9ENUgTj4KUtHbqP9hT82vFRdZHiBRfbVFDUE7QArtAB63cNZiKMgBgwrD4k1Wtac8ZgoKx2GUHFpo2

Safex:
Safex61BqfGVucrCo71xPxhQi4L1oMaRYUHwBKMuHVy8UTR1HkBjhJx4WafkLvhSwUeshkonyDjvYFiBrRDeEcrL5k6JLALD85L2T

❤Thanks for your support!❤
