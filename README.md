# Pure PHP implementation of Levin protocol
[![Latest Stable Version](https://poser.pugx.org/denpa/php-levin/v/stable)](https://packagist.org/packages/denpa/php-levin)
[![License](https://poser.pugx.org/denpa/php-levin/license)](https://packagist.org/packages/denpa/php-levin)
[![Build Status](https://travis-ci.org/denpamusic/php-levin.svg)](https://travis-ci.org/denpamusic/php-levin)
[![Code Climate](https://codeclimate.com/github/denpamusic/php-levin/badges/gpa.svg)](https://codeclimate.com/github/denpamusic/php-levin)
[![Code Coverage](https://codeclimate.com/github/denpamusic/php-levin/badges/coverage.svg)](https://codeclimate.com/github/denpamusic/php-levin/coverage)


## Examples
### Using helpers
```php
require 'vendor/autoload.php';

use Denpa\Levin;

$vars = [
    'network_id' => 'somenetwork',
];

Levin\connection($ip, $port, $vars)->connect(
    function ($bucket, $connection) {
        if ($bucket->isRequest('supportflags', 'timedsync', 'ping')) {
            // respond to supportflags, timedsync and ping requests
            // to keep the connection open
            $connection->write($bucket->response());
        }

        if ($bucket->isResponse('handshake')) {
            // send ping request to the server after
            // receiving handshake response
            $connection->write(Levin\request('ping'));
        }

        if ($bucket->isResponse('ping')) {
            // dump server response to the console
            var_dump($bucket->getPayload());

            // returning false closes connection
            return false;
        }
    }
);
```

## Request Support
| command      | link                                                                                                              | request | response  |
|--------------|-------------------------------------------------------------------------------------------------------------------|---------|-----------|
| Handshake    | [p2p_protocol_defs.h#L177](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L177) | ✅       | ✅        |
| TimedSync    | [p2p_protocol_defs.h#L239](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L239) | ✅       | ✅        |
| Ping         | [p2p_protocol_defs.h#L297](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L297) | ✅       | ✅        |
| StatInfo     | [p2p_protocol_defs.h#L348](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L348) | ❌       | ❌        |
| NetworkState | [p2p_protocol_defs.h#L382](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L382) | ❌       | ❌        |
| PeerId       | [p2p_protocol_defs.h#L414](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L414) | ✅       | ✅        |
| SupportFlags | [p2p_protocol_defs.h#L437](https://github.com/monero-project/monero/blob/master/src/p2p/p2p_protocol_defs.h#L437) | ✅       | ✅        |


## Notification Support
| command                | link                                                                                                                                            | request |
|------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------|---------|
| NewBlock               | [cryptonote_protocol_defs.h#L126](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L126) | ✅       |
| RequestGetObjects      | [cryptonote_protocol_defs.h#L163](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L163) | ❌       |
| ResponseGetObjects     | [cryptonote_protocol_defs.h#L179](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L179) | ❌       |
| RequestChain           | [cryptonote_protocol_defs.h#L217](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L217) | ✅       |
| ResponseChainEntry     | [cryptonote_protocol_defs.h#L231](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L231) | ✅       |
| NewFluffyBlock         | [cryptonote_protocol_defs.h#L254](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L254) | ✅       |
| RequestFluffyMissingTx | [cryptonote_protocol_defs.h#L273](https://github.com/monero-project/monero/blob/master/src/cryptonote_protocol/cryptonote_protocol_defs.h#L273) | ❌       |

## Exceptions
* `Denpa\Levin\Exceptions\ConnectionException` - thrown on connection errors.
* `Denpa\Levin\Exceptions\EntryTooLargeException` - thrown when type or packet size is too large.
* `Denpa\Levin\Exceptions\SignatureMismatchException` - thrown on section or bucket signature mismatches.
* `Denpa\Levin\Exceptions\UnexpectedTypeException` - thrown on unexpected or invalid type.
* `Denpa\Levin\Exceptions\UnknownCommandException` - thrown on unknown command.
* `Denpa\Levin\Exceptions\UnpackException` - thrown when unable to unpack binary data.

## License
This product is distributed under the [MIT license](https://github.com/denpamusic/php-levin/blob/master/LICENSE).

## Credits
Loosely based on [py-levin](https://github.com/xmrdsc/py-levin).

## Donations
If you like this project, you can donate using one of the following addresses:

BTC:  
`3L6dqSBNgdpZan78KJtzoXEk9DN3sgEQJu`  
Monero:  
`458j3EKczYFEE1Gku9ENUgTj4KUtHbqP9hT82vFRdZHiBRfbVFDUE7QArtAB63cNZiKMgBgwrD4k1Wtac8ZgoKx2GUHFpo2`  
Safex:  
`Safex61BqfGVucrCo71xPxhQi4L1oMaRYUHwBKMuHVy8UTR1HkBjhJx4WafkLvhSwUeshkonyDjvYFiBrRDeEcrL5k6JLALD85L2T`  

❤Thanks for your support!❤
