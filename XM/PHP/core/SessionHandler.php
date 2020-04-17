<?php

namespace AngelosKanatsos\XM\PHP\Core;

use AngelosKanatsos\XM\PHP\Core\RandomGenerator;
use AngelosKanatsos\XM\PHP\Core\Crypto\CryptographyInterface;
use \SessionHandlerInterface;

class SessionHandler implements SessionHandlerInterface
{

    private const SALT = 'salt';
    private $savePath;
    private $cryptographer;
    private $hashAlgo;

    public function __construct(CryptographyInterface $ci, string $level = 'strong')
    {
        $this->cryptographer = $ci;
        $this->hashAlgo = $this->chooseHashAlgo($level);
    }

    public function create_sid(): string
    {
        return (string) hash($this->hashAlgo, RandomGenerator::secureStr());
    }

    public function open($savePath, $sessionName): ?bool
    {
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0700);
        }
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        global $messages;
        $sessionPath = $this->getSessionPath($id);
        $messages['session.id'] = $id;
        $data = file_exists($sessionPath) ? ((string) @file_get_contents($sessionPath)) : "";
        $response = $this->cryptographer->decrypt($data);
        // php v. > 7 needs to return not null value
        return $response == null ? '' : $response;
    }

    public function write($id, $data): bool
    {
        return file_put_contents(
            $this->getSessionPath($id),
            $this->cryptographer->encrypt($data)
        ) === false ? false : true;
    }

    public function destroy($id): ?bool
    {
        $file = $this->getSessionPath($id);
        if (file_exists($file)) {
            unlink($file);
        }
        return true;
    }

    public function gc($maxlifetime): ?bool
    {
        foreach (glob($this->getSessionPath('_*')) as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }
        return true;
    }

    public function getHashAlgo(): string
    {
        return $this->hashAlgo;
    }

    public function getSavePath(): string
    {
        return $this->savePath;
    }

    private function chooseHashAlgo(string $level): string
    {
        if (strcmp($level, 'medium') == 0) {
            return 'sha256';
        }
        return 'sha512';
    }

    private function getSessionPath(string $id): string
    {
        return $this->savePath . DIRECTORY_SEPARATOR . self::SALT . $id;
    }
}
