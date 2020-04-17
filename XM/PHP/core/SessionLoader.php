<?php

namespace AngelosKanatsos\XM\PHP\Core;

use \AngelosKanatsos\XM\PHP\Core\Crypto\CryptographyInterface;
use Exception;
use Throwable;

final class SessionLoader
{
    private $savePath;
    private $suite;
    private $cryptoBasePath;

    public function __construct(string $suite, string $cryptoBasePath = CRYPTO_BASE_PATH)
    {
        global $messages;
        $this->suite = $suite;
        $this->cryptoBasePath = $cryptoBasePath;
        try {
            $this->sessionStart($this->checkSuite());                   
        } catch (Throwable $t) {
            $messages[] = $t->getMessage();
            $messages[] = 'No session details available.';
        }
    }


    public function getSavePath(): ?string
    {
        return $this->savePath;
    }

    public function getSuite(): ?string
    {
        return $this->suite;
    }
    
    private function checkSuite(): ?CryptographyInterface
    {
        $suiteClassPath = $this->cryptoBasePath . $this->suite;
        if (class_exists(ucfirst(strtolower($suiteClassPath)))) {
            return new $suiteClassPath();
        } else {
            throw new Exception("Encryption suite \"{$this->suite}\" does not exists.");
        }
    }

    private function sessionStart(CryptographyInterface $encryptionSuite): void
    {
        global $messages;
        $sessionHandler = new SessionHandler($encryptionSuite);
        $this->savePath = (string) dirname(__DIR__) . DIRECTORY_SEPARATOR . 'sessions' . DIRECTORY_SEPARATOR . strtolower($this->suite);
        $messages['session.cryptosuite'] = $this->suite;   
        $messages['session.path'] = $this->savePath;
        session_set_save_handler($sessionHandler, true);
        session_name(SESSION_NAME);
        session_save_path($this->savePath);        
        session_start();        
    }
}
