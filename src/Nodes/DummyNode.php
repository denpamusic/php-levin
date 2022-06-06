<?php

declare(strict_types=1);

namespace Denpa\Levin\Nodes;

use Denpa\Levin;
use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Denpa\Levin\Traits\InteractsWithConsole;
use Throwable;

/**
 * This dummy node connects to the peer, reports that
 * it's fully synced and just sits there
 * replying to timedsync requests
 * and handling new block notifications.
 */
class DummyNode extends Node
{
    use InteractsWithConsole;

    /**
     * @var int Contains current node difficulty.
     */
    public $difficulty = 0;

    /**
     * @var int Contains current node heigh.
     */
    public $height = 0;

    /**
     * @var string Contains current top id.
     */
    public $topId = '';

    /**
     * @var int Contains current top version.
     */
    public $topVersion = 0;

    /**
     * @var array Contains peerslist received from remote.
     */
    public $peerlist = [];

    /**
     * @var bool
     */
    protected $verbose = false;

    /**
     * Registers handlers for buckets.
     *
     * @return void
     */
    public function __construct()
    {
        $this
            ->registerRequestHandler('payloadDataHandler', 'timedsync')
            ->registerRequestHandler('requestChainHandler', 'requestchain')
            ->registerRequestHandler('responseHandler', 'ping', 'supportflags')
            ->registerRequestHandler('timedSyncHandler', 'timedsync')
            ->registerRequestHandler('newTransactionsHandler', 'newtransactions')
            ->registerRequestHandler('newBlockHandler', 'newblock', 'newfluffyblock')
            ->registerResponseHandler('peerlistHandler', 'handshake')
            ->registerResponseHandler('payloadDataHandler', 'handshake')
            ->registerResponseHandler('sendPingHandler', 'handshake')
            ->registerResponseHandler('recvPingHandler', 'ping');
    }

    /**
     * {@inheritdoc}
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    public function handle(Bucket $bucket, Connection $connection): mixed
    {
        $this->printBucket($bucket, 'in');

        return parent::handle($bucket, $connection);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $address
     * @param mixed  $port
     * @param array  $options
     *
     * @return void
     */
    public function connect(string $address, $port, array $options = []): void
    {
        $this->verbose = isset($options['v']);

        if (isset($options['colors'])) {
            $this->console()->enableColors();
        }

        parent::connect($address, $port, $options);
    }

    /**
     * Handles exception by outputing it to stderr.
     *
     * @param \Throwable $exception
     *
     * @return void
     */
    public function handleException(Throwable $exception): void
    {
        $this
            ->console()->target(STDERR)
            ->error('Exception: %s'.PHP_EOL, $exception->getMessage());
    }

    /**
     * Handles remote peerlist.
     *
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return mixed
     */
    protected function peerlistHandler(Bucket $bucket): mixed
    {
        $peers = $bucket->getPayload()['local_peerlist_new'] ?? [];

        foreach ($peers as $entry) {
            $addr = $entry['adr']['addr'] ?? null;

            if (is_null($addr)) {
                continue;
            }

            // add peer to peerlist
            $this->peerlist[] = [
                'ip'        => inet_ntop($addr['m_ip']->toBinary()),
                'port'      => $addr['m_port']->toInt(),
                'last_seen' => isset($entry['last_seen']) ?
                    date('Y-m-d H:i:s', $entry['last_seen']->toInt()) : '',
            ];
        }

        $this->printPeerlist();
    }

    /**
     * Handles requests and responses that have payload data.
     *
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return mixed
     */
    protected function payloadDataHandler(Bucket $bucket): mixed
    {
        $payloadData = $bucket->getPayload()['payload_data'];

        $this->difficulty = $payloadData['cumulative_difficulty']->toInt();
        $this->height = $payloadData['current_height']->toInt();
        $this->topVersion = $payloadData['top_version']->toInt();
        $this->topId = $payloadData['top_id']->getValue();

        $this
            ->console()
            ->info(
                '%sTop Id: %s, Version: %d, Height: %d, Difficulty: %d%s',
                PHP_EOL,
                bin2hex($this->topId),
                $this->topVersion,
                $this->height,
                $this->difficulty,
                PHP_EOL
            );
    }

    /**
     * Handles chain request chain notification.
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    protected function requestChainHandler(Bucket $bucket, Connection $connection): mixed
    {
        $responsechainentry = Levin\notification('responsechainentry', [
            'start_height'          => $this->height - 1,
            'total_height'          => $this->height,
            'cumulative_difficulty' => $this->difficulty,
            'm_block_ids'           => $this->topId,
        ]);

        // report that we already have lastest block available to remote
        $this->write($responsechainentry, $connection);
    }

    /**
     * Responds to the request.
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    protected function responseHandler(Bucket $bucket, Connection $connection): mixed
    {
        $this->write($bucket->response(), $connection);
    }

    /**
     * Handles timed sync requests.
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    protected function timedSyncHandler(Bucket $bucket, Connection $connection): mixed
    {
        $payloadData = $bucket->getPayload()['payload_data'];

        $timedsync = Levin\response('timedsync', [
            'top_id'                => $payloadData['top_id']->getValue(),
            'top_version'           => $payloadData['top_version']->toInt(),
            'cumulative_difficulty' => $payloadData['cumulative_difficulty']->toInt(),
            'current_height'        => $payloadData['current_height']->toInt(),
        ]);

        $this->write($timedsync, $connection);
    }

    /**
     * Handles new block notifications.
     *
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return mixed
     */
    protected function newBlockHandler(Bucket $bucket): mixed
    {
        $payload = $bucket->getPayload();

        $this->height = $payload['current_blockchain_height']->toInt();

        $this
            ->console()
            ->line('New block: #%d'.PHP_EOL, $this->height)->line('Block hex:')
            ->startBlock()
            ->indent()->line(bin2hex($payload['b']['block']).PHP_EOL)
            ->endBlock();
    }

    /**
     * Handles new transactions notifications.
     *
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return mixed
     */
    protected function newTransactionsHandler(Bucket $bucket): void
    {
        $txs = $bucket->getPayload()['txs'] ?? [];

        $this
            ->console()
            ->info('Received %d new transactions:'.PHP_EOL, count($txs))
            ->startBlock();

        foreach ($txs as $tx) {
            $this->console()->indent()->line($tx->toHex().PHP_EOL.PHP_EOL);
        }

        $this->console()->endBlock();
    }

    /**
     * Sends ping request.
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    protected function sendPingHandler(Bucket $bucket, $connection)
    {
        $this->write(Levin\request('ping'), $connection);
    }

    /**
     * Outputs received ping status.
     *
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return mixed
     */
    protected function recvPingHandler(Bucket $bucket): mixed
    {
        $status = $bucket->getPayload()['status'] ?? 'FAIL';

        $this
            ->console()
            ->info(PHP_EOL.'PING: %s'.PHP_EOL, $status);
    }

    /**
     * End of bucket handler methods.
     */

    /**
     * Write bucket to the connection and output.
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return void
     */
    protected function write(Bucket $bucket, Connection $connection): void
    {
        $this->printBucket($bucket, 'out');

        $connection->write($bucket);
    }

    /**
     * Outputs line containing info about bucket.
     *
     * @param \Denpa\Levin\Bucket $bucket
     * @param string              $direction
     *
     * @return void
     */
    protected function printBucket(Bucket $bucket, string $direction = ''): void
    {
        $this->printDirectionArrows($direction);
        $this->printBucketType($bucket);

        $this
            ->console()
            ->background('white')->color('black')
            ->line(get_class($bucket->getCommand()).PHP_EOL)
            ->resetColors();

        if ($this->verbose) {
            $this->console()->dump($bucket);
        }
    }

    /**
     * Prints bucket type, which can be response, request or notification.
     *
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return void
     */
    protected function printBucketType(Bucket $bucket): void
    {
        $type = $bucket->isResponse() ? 'response' : 'request';

        if ($bucket->isRequest() && !$bucket->getReturnData()->getValue()) {
            $type = 'notification';
        }

        $this
            ->console()
            ->line(' (')
            ->color('bright-yellow')
            ->line($type)
            ->resetColors()
            ->line(')  ');
    }

    /**
     * Prints bucket direction arrows.
     *
     * @param string $direction
     *
     * @return void
     */
    protected function printDirectionArrows(string $direction = ''): void
    {
        switch ($direction) {
            case 'in':
                $direction = '>>>';
                break;
            case 'out':
                $direction = '<<<';
                break;
            default:
                $direction = '   ';
        }

        $this
            ->console()
            ->resetColors()
            ->line(PHP_EOL.$direction);
    }

    /**
     * @return void
     */
    protected function printPeerlist(): void
    {
        $this->console()->info('Remote peers:'.PHP_EOL)->startBlock();

        foreach ($this->peerlist as $peer) {
            $this
                ->console()
                ->indent()
                ->line(
                    '%s  seen %s'.PHP_EOL,
                    str_pad($peer['ip'].':'.$peer['port'], 21),
                    $peer['last_seen']
                );
        }

        $this
            ->console()
            ->indent()
            ->line(
                '%sTotal: %d known peers%s%s',
                PHP_EOL,
                count($this->peerlist),
                PHP_EOL,
                PHP_EOL
            )
            ->endBlock();
    }
}
