<?php

/**
 * Created by IntelliJ IDEA.
 * User: saleeh
 * Date: 22/01/17
 * Time: 4:30 PM
 */
class Appmaker_WC_FCM_Helper {


	private $serverKey;

	private $_title = '';

	private $_message = '';

	private $_action = array();

	public function __construct( $serverKey ) {

		$this->serverKey = $serverKey;

	}


	public function setTopic( $topic ) {
		$this->_topic = "/topics/$topic";

		return $this;
	}

	public function setMessage( $title, $message ) {
		$this->_title = $title;

		$this->_message = $message;

		return $this;
	}

	public function setAction( $action ) {
		$this->_action = $action;

		return $this;
	}


	public function send() {

		$path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
		$fields              = array(
			'to'           => $this->_topic,
			'priority'     => 'high',
			'notification' => array(
				'title' => $this->_title,
				'body'  => $this->_message
			),
			'data'         => array(
				'newScene' => true,
				'action'   => $this->_action
			)
		);
		$headers             = array(
			'Authorization:key=' . $this->serverKey,
			'Content-Type:application/json'
		);

		$data                = json_encode( $fields );
		$ch                  = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $path_to_firebase_cm );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		$result = curl_exec( $ch );
		curl_close( $ch );

		return $result;
	}
}