<?php

namespace App\Controller\Api\ResetPassword;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use App\Shared\Reponses;
use App\Shared\Messages;
use App\Service\Service;
use App\Service\Repository;

class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;
    private $entityManager;
    private Reponses $reponses;
    private Service $service;
    private Repository $repository;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, EntityManagerInterface $entityManager,Reponses $reponses, Service $service, Repository $repository)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
        $this->reponses = $reponses;
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('/forgout', name: 'forgoutPassword',methods:'POST')]
    public function forgoutPassword(MailerInterface $mailer): Response
    {
        $data=$this->service->json_decode();
        if(!isset($data->email) || $data->email===""){
            return $this->reponses->error(Messages::FORM_INVALID);
        }

       
        $user=$this->repository->userRepository()->findOneByEmail($data->email);
        if(!$user){
            return $this->reponses->error(Messages::USER_NOT_FOUND);
        }

        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        $email = (new TemplatedEmail())
        ->from(new Address('MAMINIAINAZAIN@gmail.com', 'Beckas'))
        ->to($user->getEmail())
        ->subject('Lien changement de mot de passe')
        ->htmlTemplate('email.html.twig')
        ->context([
            'resetToken' => $resetToken,
            'nom'=>$user->getNom(),
            'prenom'=>$user->getPrenom(),
        ]);

        $mailer->send($email);
        $this->setTokenObjectInSession($resetToken);

        return $this->reponses->success(null,null,Messages::CHECK_EMAIL);
    }
    

    #[Route('/reset/{token}', name: 'resetPassword',methods:'POST')]
     public function resetPassword($token):Response
     {
        $data=$this->service->json_decode();
        if(!isset($data->password) || $data->password===""){
            return $this->reponses->error(Messages::FORM_INVALID);
        }
        if ($token) {
            $this->storeTokenInSession($token);
        }
        $token = $this->getTokenFromSession();
        if (null === $token) {
            return $this->reponses->success(Messages::BAD_URL_TOKEN);
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->reponses->error(Messages::TOKEN_INVALID);
        }

        $this->resetPasswordHelper->removeResetRequest($token);

        $encodedPassword = $this->service->hasher()->hashPassword(
            $user,
            $data->password
        );

        $user->setPassword($encodedPassword);
        $this->service->em()->flush();

        $this->cleanSessionAfterReset();

        return $this->reponses->success(null,null,Messages::SUCCESS_RESETPASSWORD);

     }
}
