<?php
namespace FireKit\Sockets\Client;
/**
 * Created by JetBrains PhpStorm.
 * User: Сергей
 * Date: 06.10.11
 * Time: 11:29
 * To change this template use File | Settings | File Commands.
 */

use \FireKit\Sockets\Exceptions\SocketException;

class ClientSocket {
    protected $socket;
    protected $conn_info;
    protected $ssl = false;
    protected $errno = 0;
    protected $errstr = '';


    public function getFrame() {
        if (feof($this->socket)) {
            return false;
        }

        $hdr = fread($this->socket, 4);

        if (empty($hdr) && feof($this->socket)) {
            return false;
        } elseif (empty($hdr)) {
            return false;
        } else {
            $unpacked = unpack('N', $hdr);
            $length = $unpacked[1];
            if ($length < 5) {
              return false;

            } else {
              return  fread($this->socket, ($length - 4)) ;
            }
        }
    }

    public function sendFrame($xml) {
        fwrite($this->socket, pack('N', (strlen($xml)+4)).$xml);
    }

    public function Connect($host, $port, $timeout, $ssl = true, $context = false){
        $host = $ssl === true ? "ssl://" . $host : "tcp://".$host;
        $context = !empty($context) ? stream_context_create($context) : null;
        if ($context) {
            $conn = stream_socket_client($host.":".$port, $this->errno, $this->errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        } else {
            $conn = fsockopen($host, $port, $this->errno, $this->errstr, $timeout);
        }
        if (!$conn) {
            return false;
        }
        $this->socket = $conn;
        $this->getFrame();
        return true;
    }

    public function __destruct(){
        if (is_resource($this->socket)) @fclose($this->socket);
    }

    public function __construct($host, $port = 700, $timeout = 30, $context = false){
        $this->Connect($host, $port, $timeout, true, $context);
        return $this;
    }

    public function isSocket(){
        return is_resource($this->socket) ? true : false;
    }

    public function GetError(){
        return $this->errno . ":" . $this->errstr;
    }
}
?>