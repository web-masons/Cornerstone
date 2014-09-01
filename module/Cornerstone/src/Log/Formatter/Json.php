<?php
namespace Cornerstone\Log\Formatter;

use Zend\Log;
use Traversable;

class Json extends Log\Formatter\Base
{

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param array $event
     *            event data
     * @return string formatted line to write to the log
     */
    public function format($event)
    {
        $time = microtime(true);
        $micro = sprintf("%06d", ($time - floor($time)) * 1000000);
        $timestamp = new \DateTime(date('Y-m-d H:i:s.' . $micro, $time));

        $event['timestamp'] = $timestamp->format('Y-m-d\TH:i:s.uP');
        $event['unix_microtime'] = $time;

        $event = parent::format($event);

        return json_encode($event);
    }
}
