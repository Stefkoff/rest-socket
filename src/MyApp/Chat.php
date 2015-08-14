<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface{

	protected $clients;

	public function __construct(){
		$this->clients = new \SplObjectStorage;
	}

	public function onOpen(ConnectionInterface $conn){
		$this->clients->attach($conn);

		$markers = array(
			array(
				'lat' => -33,
				'lng' => 149,
				'content' => "<div id='content'>Hello World</div>'"
			),
			array(
				'lat' => -32,
				'lng' => 148,
				'content' => "<div id='content'>Hello World 2</div>"
			),
			array(
				'lat' => -31,
				'lng' => 147,
				'content' => "<div id='content'>Hello World 3 </div>"
			)
		);
		
		
		$data = array();

		$data[] = array(
			'type' => 'setCenter',
			'value' => array(
				'lat' => -33,
				'lng' => 150
			)
		);
		$data[] = array(
			'type' => 'setMarcker',
			'value' => $markers
		);

		$conn->send(json_encode($data));
		echo "New Connection! ({$conn->resourceId})";
	}

	public function onMessage(ConnectionInterface $from, $msg){
		$numRecv = count($this->clients);
		
		echo sprintf('Connection %d sending message "%s" to %d other connection%s' . '\n', $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

		foreach($this->clients as $client){
			if($from !== $client){
				$client->send($msg);
			}
		}
	}

	public function onClose(ConnectionInterface $conn){
		$this->clients->detach($conn);

		echo "Connection {$conn->resourceId} has disconected\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e){

		echo "An error has occurred: {$e->getMessage()}\n";

		$conn->close();	
	}
}
