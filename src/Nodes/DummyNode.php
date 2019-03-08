<?php

namespace Denpa\Levin\Nodes;

use Denpa\Levin;
use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Denpa\Levin\Console;
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
     * Registers handlers for commands.
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
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    public function handle(Bucket $bucket, Connection $connection)
    {
        $this->debug($bucket, 'in');

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
    public function connect(string $address, $port, array $options = []) : void
    {
        $this->verbose = isset($options['v']);

        if (!isset($options['colors'])) {
            $this->console()->disableColors();
        }

        parent::connect($address, $port, $options);
    }

    /**
     * @param \Throwable $exception
     *
     * @return void
     */
    public function handleException(Throwable $exception) : void
    {
        $this
            ->console()
            ->target(STDERR)
            ->error('Exception: %s', $exception->getMessage())
            ->eol();
    }

    /**
     * Handles remote peerlist.
     *
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return mixed
     */
    protected function peerlistHandler($bucket)
    {
        $peers = $bucket->getPayload()['local_peerlist_new'] ?? [];

        foreach ($peers as $entry) {
            $addr = $entry['adr']['addr'] ?? null;

            if (is_null($addr)) {
                continue;
            }

            $ip = inet_ntop($addr['m_ip']->toBinary());
            $port = $addr['m_port']->toInt();
            $lastSeen = isset($entry['last_seen']) ?
                date('Y-m-d H:i:s', $entry['last_seen']->toInt()) : '';

            // add peer to peerlist
            $this->peerlist[] = [
                'ip'        => $ip,
                'port'      => $port,
                'last_seen' => $lastSeen,
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
    protected function payloadDataHandler($bucket)
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
    protected function requestChainHandler($bucket, $connection)
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
    protected function responseHandler($bucket, $connection)
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
    protected function timedSyncHandler($bucket, $connection)
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
    protected function newBlockHandler($bucket)
    {
        $payload = $bucket->getPayload();

        $this->height = $payload['current_blockchain_height']->toInt();

        $this
            ->console()
            ->line('New block: #%d'.PHP_EOL, $this->height)
            ->line('Block hex:')
            ->startBlock()
            ->indent()
            ->line(bin2hex($payload['b']['block']).PHP_EOL)
            ->endBlock();
    }

    /**
     * Handles new transactions notifications.
     *
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return mixed
     */
    protected function newTransactionsHandler($bucket) : void
    {
        $txs = $bucket->getPayload()['txs'];

        $this
            ->console()
            ->info('Received %d new transactions:'.PHP_EOL, count($txs))
            ->startBlock();

        foreach ($txs as $tx) {
            $this->console()->indent()->line($tx->toHex())->eol()->eol();
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
    protected function sendPingHandler($bucket, $connection)
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
    protected function recvPingHandler($bucket)
    {
        $this
            ->console()
            ->info(PHP_EOL.'PING: '.$bucket->getPayload()['status'].PHP_EOL);
    }

    /**
     * Write bucket to the connection and output.
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return void
     */
    protected function write(Bucket $bucket, Connection $connection) : void
    {
        $this->debug($bucket, 'out');

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
    protected function debug(Bucket $bucket, string $direction = '') : void
    {
        $this
            ->printDirectionArrows($direction)
            ->color('bright-yellow')
            ->line($this->getBucketType($bucket))
            ->resetColors()
            ->line(')  ')
            ->background('white')
            ->color('black')
            ->line(get_class($bucket->getCommand()).PHP_EOL)
            ->resetColors();

        if ($this->verbose) {
            $this->console()->dump($bucket);
        }
    }

    /**
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return string
     */
    protected function getBucketType(Bucket $bucket) : string
    {
        if ($bucket->isRequest() && !$bucket->getReturnData()->getValue()) {
            return 'notification';
        }

        return $bucket->isResponse() ? 'response' : 'request';
    }

    /**
     * @return \Denpa\Levin\Console
     */
    protected function printPeerlist() : Console
    {
        $this->console()->info('Remote peers:')->eol()->startBlock();

        foreach ($this->peerlist as $peer) {
            $ipport = $peer['ip'].':'.$peer['port'];

            $this
                ->console()
                ->indent()
                ->line(
                    '%s  seen %s'.PHP_EOL,
                    str_pad($ipport, 21),
                    $peer['last_seen']
                );
        }

        return $this->console()
            ->indent()
            ->line(PHP_EOL.'Total: %d known peers'.PHP_EOL, count($this->peerlist))
            ->endBlock()
            ->eol();
    }

    /**
     * @param string $direction
     *
     * @return \Denpa\Levin\Console
     */
    protected function printDirectionArrows(string $direction = '') : Console
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

        return $this
            ->console()
            ->resetColors()
            ->line(PHP_EOL."$direction (");
    }
}
