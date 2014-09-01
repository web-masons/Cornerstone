<?php
namespace Cornerstone\Log\Writer;

use Zend;

/**
 * Writes log messages to syslog
 */
class Syslog extends Zend\Log\Writer\Syslog
{
    protected $mStream;
    protected $mSocket = 'udg:///dev/log';

    /**
     * Initialize syslog / set application name and facility
     *
     * @return void
     */
    protected function initializeSyslog()
    {
        $errno = null;
        $errstr = null;

        if (is_null($this->mStream) || false == $this->mStream)
        {
            $this->mStream = stream_socket_client($this->mSocket, $errno, $errstr);

            if (false == $this->mStream)
            {
                http_response_code(503);
                exit(99);
            }

            stream_set_write_buffer($this->mStream, 0);
            register_shutdown_function('fclose', $this->mStream);
        }
    }

    /**
     * Write a message to syslog.
     *
     * @param array $event
     *            event data
     * @return void
     */
    protected function doWrite(array $event)
    {
        if (array_key_exists($event['priority'], $this->priorities))
        {
            $priority = $this->priorities[$event['priority']];
        }
        else
        {
            $priority = $this->defaultPriority;
        }

        if (is_null($this->mStream) || false == $this->mStream)
        {
            $this->initializeSyslog();
        }

        $message = $this->formatter->format($event) . "\0";

        $priority = $this->facility + $priority;
        $packet = "<$priority>" . $this->appName . ": $message";

        stream_socket_sendto($this->mStream, $packet);
    }
}
