<?php

namespace App\Service;

use DateTime;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UtilisateurService{

    private $mailer;
    private $session;

    public function __construct(MailerInterface $mailer,SessionInterface $session)
    {
        $this->mailer = $mailer;
        $this->session=$session;
    }


 


    public function sendEmail(string $subject,string $text,string $email)
    {
       
        $email = (new Email())
            ->from('ecoartteampi@gmail.com')
            ->to($email)
            ->subject($subject)
            ->text($text);
       //     ->html('<p>Contenu du message en HTML</p>');

       try{
        $this->mailer->send($email);
       } catch (\Exception $e) {
        dd($e->getMessage());
       }
    }

}
