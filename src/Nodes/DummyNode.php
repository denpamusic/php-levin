<?php

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

        if (isset($options['no-ansi'])) {
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

        $this->console('Remote peers:')->eol()->startBlock();

        foreach ($peers as $entry) {
            $addr = $entry['adr']['addr'] ?? null;

            if (is_null($addr)) {
                continue;
            }

            $ip = inet_ntop($addr['m_ip']->toBinary());
            $port = $addr['m_port']->toInt();
            $lastSeen = isset($entry['last_seen']) ?
                date('Y-m-d H:i:s', $entry['last_seen']->toInt()) : '';

            $this
                ->console()
                ->indent()
                ->line('%s  seen %s', str_pad("$ip:$port", 21), $lastSeen)
                ->eol();

            // add peer to peerlist
            $this->peerlist[] = [
                'ip'        => $ip,
                'port'      => $port,
                'last_seen' => $lastSeen,
            ];
        }

        $this
            ->console()
            ->eol()
            ->indent()
            ->line('Total: %d known peers', count($this->peerlist))
            ->eol()
            ->eol()
            ->endBlock();
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
            ->eol()
            ->info(
                'Top Id: %s, Version: %d, Height: %d, Difficulty: %d',
                bin2hex($this->topId),
                $this->topVersion,
                $this->height,
                $this->difficulty
            )
            ->eol();
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
            ->line('New block: #%d', $this->height)
            ->eol()
            ->line('Block hex:')
            ->eol()
            ->startBlock()
            ->indent()
            ->line(bin2hex($payload['b']['block']))
            ->eol()
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
            ->info('Received %d new transactions:', count($txs))
            ->eol()
            ->startBlock();

        foreach ($txs as $tx) {
            $this
                ->console()
                ->indent()
                ->line($tx->toHex())
                ->eol()
                ->eol();
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
            ->eol()
            ->info('PING: '.$bucket->getPayload()['status'])
            ->eol();
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
            ->eol()
            ->line("$direction (");

        $this
            ->printBucketType($bucket)
            ->line(')  ')
            ->background('white')
            ->color('black')
            ->line(get_class($bucket->getCommand()))
            ->resetColors()
            ->eol();

        if ($this->verbose) {
            $this->console()->dump($bucket);
        }
    }

    /**
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return \Denpa\Levin\Console
     */
    protected function printBucketType(Bucket $bucket)
    {
        $this->console()
            ->resetColors()
            ->color('bright-yellow');

        if ($bucket->isRequest() && !$bucket->getReturnData()->getValue()) {
            return $this->console()
                ->line('notification')
                ->resetColors();
        }

        return $this
            ->console()
            ->line($bucket->isResponse() ? 'response' : 'request')
            ->resetColors();
    }
}
