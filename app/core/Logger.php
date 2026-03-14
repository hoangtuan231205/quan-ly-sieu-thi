<?php
/**
 * LOGGER CLASS - GHI LOG HỆ THỐNG
 */

class Logger {
    
    private $channel;
    private $logPath;
    
    public function __construct($channel = 'app') {
        $this->channel = $channel;
        $this->logPath = ROOT_PATH . '/logs';
        
        // Tạo thư mục logs nếu chưa có
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }
    
    /**
     * Ghi log INFO
     */
    public function info($message, $context = []) {
        $this->log('INFO', $message, $context);
    }
    
    /**
     * Ghi log ERROR
     */
    public function error($message, $context = []) {
        $this->log('ERROR', $message, $context);
    }
    
    /**
     * Ghi log WARNING
     */
    public function warning($message, $context = []) {
        $this->log('WARNING', $message, $context);
    }
    
    /**
     * Ghi log DEBUG
     */
    public function debug($message, $context = []) {
        $this->log('DEBUG', $message, $context);
    }
    
    /**
     * Ghi log vào file
     */
    private function log($level, $message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $logFile = $this->logPath . '/' . $this->channel . '_' . date('Y-m-d') . '.log';
        
        $logEntry = sprintf(
            "[%s] [%s] [%s] %s %s\n",
            $timestamp,
            $level,
            $this->channel,
            $message,
            !empty($context) ? json_encode($context) : ''
        );
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}