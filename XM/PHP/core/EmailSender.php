<?php

namespace AngelosKanatsos\XM\PHP\core;

require_once __DIR__  . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .  'vendor/autoload.php';

use Swift_SmtpTransport, Swift_Mailer, Swift_Message, Exception;
use AngelosKanatsos\XM\PHP\core\JsonReporter;

class EmailSender
{
    protected $email;
    protected $result;
    protected $jsonReporter;

    public function __construct(Email $email, JsonReporter $jsonReporter)
    {
        $this->email = $email;
        $this->jsonReporter = $jsonReporter;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function sendEmail()
    {
        $transport = (new Swift_SmtpTransport('smtp.mail.yahoo.com', 587, 'tls'))
            ->setUsername('xm.test_aggeloskan@yahoo.com')
            ->setPassword('xhwaolbnyrcuxlgk');

        // Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

        // Create a message
        $message = (new Swift_Message($this->email->getSubject()))
            ->setFrom([$this->email->getAddressFrom() => 'Aggelos Kanatsos'])
            ->setTo([$this->email->getAddressTo()])
            ->setBody($this->email->getContent());

        // Send the message        
        try {            
            if (!$mailer->send($message)) {
                $this->jsonReporter->errorJSON("There was a problem when sending the email.");
            }
        } catch (Exception $e) {
            $this->jsonReporter->errorJSON($e->getMessage());
        }
        $this->jsonReporter->successJSON('Successfully sent email');
    }
}
