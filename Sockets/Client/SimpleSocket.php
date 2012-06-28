<?php
namespace FireKit\Sockets\Client;
/**
 * User: Сергей Пименов
 * Date: 22.12.11
 * Time: 15:58
 * File: SimpleSocket.php
 */
class SimpleSocket extends \FireKit\Base\Super {
    protected $protocol = 'ssl://';
    protected $host;
    protected $port;
    protected $mode;
    protected $timeout;
    protected $socket;

    public $isConnected = false;
    public $rawError = "";

    public function __construct($protocol = "tcp://", $host = "localhost", $port = 43, $mode = 1, $timeout = 5, $connectimmediate = false) {
      $this->protocol = $protocol;
      $this->host = $host;
      $this->port = $port;
      $this->mode = $mode;
      $this->timeout = $timeout;

      if ($connectimmediate) {
        $this->Connect();
      }
    }

    public function __destruct(){
      $this->disconnect();
    }

    public function Connect(){
      if ($this->isConnected && $this->host) {
        $this->Disconnect();
      }
      $errno = 0;
      $errstr = "";
      $this->socket = fsockopen($this->protocol . $this->host, $this->port, $errno, $errstr, 30);
      if (!$this->socket) {
        $this->rawError = $errno . ':' . $errstr;
        return false;
      }
      stream_set_blocking($this->socket, $this->mode);
      stream_set_timeout($this->socket, $this->timeout);
      $this->isConnected = true;
      return true;
    }

    public function Disconnect(){
      if (is_resource($this->socket)) {
        fclose( $this->socket);
      }
      $this->isConnected = false;
      unset($this->socket);
    }

    public function GetSocket(){
        return is_resource($this->socket) ? $this->socket:false;
    }
}
