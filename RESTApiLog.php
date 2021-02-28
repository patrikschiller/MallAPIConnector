<?php
abstract class MessageType{
    const ERROR = 0;
    const WARNING = 1;
    const INFO = 2;

    const Translate = [
        0 => "ERROR",
        1 => "WARNING",
        2 => "INFO"
    ];
}

class RestApiLog{
    private $implementationPrefix;
    private $log = array();

    public function __construct($prefix)
    {
        $this->implementationPrefix = $prefix;
    }

    /**
     * @summary - Adds new message into log queue
     * @param message {String}
     * @param type {MessageType}
     */
    public function push($type, $module ,$message){
        $message = "[" . $this->implementationPrefix . "][".MessageType::Translate[$type]."::".$module."]: " . $message;
        $obj = [
            "type" => MessageType::Translate[$type],
            "module" => $module,
            "message" => $message,
            "time" => (new DateTime())->getTimestamp()
        ];
        array_push($this->log, $obj);
    }

    public function printLogSimple(){
        foreach($this->log as $item){
            echo $item['message']."<br/>";
        }
    }
}

?>