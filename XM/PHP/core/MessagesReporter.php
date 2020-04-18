<?php

namespace AngelosKanatsos\XM\PHP\core;

/**
 * This class uses two methods in order to 
 * print arrays' messages with specific format
 */
class MessagesReporter implements ReporterInterface
{
    /**
     * This method wraps a string message with a div
     * block and sets bootstrap class for the 
     * color of the block
     *
     * @param string $message
     * @param string $bgColor
     * @return string
     */
    public function format(string $message, string $bgColor = 'info'): string
    {
        return "<div class='p-3 mb-2 bg-{$bgColor} text-dark font-weight-bold'>" . $message . '</div>';
    }

    /**
     * This method prints messages with specific format
     *
     * @param array $messages
     * @return void
     */
    public function print(array $messages): void
    {
        foreach ($messages as $key => $message) {
            $message = !is_numeric($key) ? $key . ' => ' . $message : $message;
            echo $this->format($message);
        }       
    }
}
