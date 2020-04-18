<?php

namespace AngelosKanatsos\XM\PHP\core;

class JsonReporter implements ReporterInterface
{
    protected $lastMessage;

    public function getLastMessage(): array
    {
        return $this->lastMessage;
    }

    public function successJSON(string $content = null, bool $die = false)
    {
        $this->lastMessage = $content;
        $this->print(array("code" => 200, "content" => $this->format($content, "info"), "die" => $die, "type" => "info"));
    }

    public function errorJSON(string $content, bool $die = true, int $code = 500)
    {
        $this->lastMessage = $content;
        $this->print(array("code" => $code, "content" => $this->format($content, "warn"), "die" => $die));
    }

    public function format($content, $type): string
    {
        if (!empty($content)) {
            $content = json_encode((object) $content);
            // strip the first character '{' from the json encoded string    
            $content = substr($content, 1, strlen($content) - 1);
            // add the "code" part to the string    
            $suffix = ',' . $content;
        } else {
            $suffix = '}';
        }
        return $suffix;
    }

    public function print(array $array): void
    {
        $buffer = '{"code":"' . $array["code"] . '"' . $array["content"];
        echo $buffer;
        if ($array["die"]) {
            die();
        }
    }
}
