<?php
namespace Fpp;


class Log {

    // log 名字
    private $name = 'root';

    // log 级别
    private $level = 0;

    // log 输出方式，0控制台, 1文件
    private $channel = 0;

    public function __construct($name, $level=0, $channel=0) {
        $this->name = $name;
        $this->level = $level;
        $this->channel = $channel;
    }

    public function setLevel($level) {
        $this->level = $level;
    }

    public function setChannel($channel) {
        $this->channel = $channel;
    }

    public static function getLogger($name, $level=0, $channel=0) {
        return new Log($name, $level, $channel);
    }

    public function format($level, $message) {
        $time = date('Y-m-d H:i:s+e');
        $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        return sprintf("%s [%s] %s\t%s%s", $time, $this->name, strtoupper($level), $message, PHP_EOL);
    }

    public function log($level, $message) {
        $out = $this->format($level, $message);
        if ($this->channel == 0) {
            echo $out;
        }elseif ($this->channel === 1) {
            error_log($out);
        }elseif ($this->channel === 2) {
            echo $out;
            error_log($out);
        }
    }

    public function debug($message) {
        $this->log('debug', $message);
    }

    public function info($message) {
        $this->log('info', $message);
    }

    public function warn($message) {
        $this->log('warn', $message);
    }

    public function error($message) {
        $this->log('error', $message);
    }

    public function fatal($message) {
        $this->log('fatal', $message);
    }
}

