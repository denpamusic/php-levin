# Nodes
## Dummy
#### `Denpa\Levin\Nodes\DummyNode`
For now, this is the only node available. It connects to the peer, reports that it's fully synchronized to peer's height and just sits there waiting for notifications, replying to timedsync requests and dumping responses and requests to the STDOUT.

This node could be useful for ensuring that you daemon is working or for educational purposes, when learning about protocol.

This node can be run by typing:
`vendor/bin/dummy-node [-v] [--no-ansi] [--network-id=] <ip> <port>`

| param        | *        | description                                                     |
|--------------|----------|-----------------------------------------------------------------|
| -v           | optional | Be verbose. Dumps every bucket to the standard output.          |
| --colors     | optional | Enables ANSI colors.                                            |
| --network-id | optional | Network id encoded as hex string. Defaults to monero's mainnet. |
| ip           | required | IP address to connect to.                                       |
| port         | optional | Port to connect to. Defaults to 18080.                          |
