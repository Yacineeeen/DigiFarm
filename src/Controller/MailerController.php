<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/sendmail/{id}', name: 'mailing',methods: ['GET'])]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('oumayma.gafsi@esprit.tn')
            ->to('oumaymagafsi3@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('follow the suivi')
            ->text('here is the animal state')
            ->html('<p>sending suivi-_-  </p> <p> thanks _|_  </p>');
    
        $mailer->send($email);
    
        // Return a response, for example, a simple acknowledgment message.
        return new Response('Email sent successfully');
    }
}

