<?php

namespace App\Controller\Api\Secure;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Shared\Reponses;
use App\Shared\Messages;
use App\Service\Service;
use App\Service\Repository;
use App\Entity\Commande;

#[Route('/api', name: 'ctrl_commande')]
#[Security("is_granted('ROLE_USER')")]
class CommandeController extends AbstractController
{
    private Reponses $reponses;
    private Service $service;
    private Repository $repository;

    public function __construct(Reponses $reponses, Service $service, Repository $repository)
    {
        $this->reponses = $reponses;
        $this->service = $service;
        $this->repository = $repository;
    }

    #[Route('/commande', name: 'ajouteCommande', methods: 'POST')]
    public function ajouteCommande(): Response
    {
        $data=$this->service->json_decode();
        if(!isset($data->achat,$data->prixTotal,$data->nom,$data->prenom,$data->telephone,$data->adresseLivraison,$data->idUser) || ($data->achat==="" || $data->prixTotal===""||$data->nom===""||$data->prenom===""||$data->telephone===""||$data->adresseLivraison===""||$data->idUser==="")){
            return $this->reponses->error(Messages::FORM_INVALID);
        }

        $user = $this->repository->userRepository()->findOneById($data->idUser);

;

        $commande = new Commande();
        $commande->setAchat($data->achat);
        $commande->setPrixTotal($data->prixTotal);
        $commande->setNom($data->nom);
        $commande->setPrenom($data->prenom);
        $commande->setAdresseLivraison($data->adresseLivraison);
        $commande->setTelephone($data->telephone);
        $commande->setUser($user);
        $this->service->em()->persist($commande);
        $this->service->em()->flush();
        return $this->reponses->success($commande->tojson(), 1, Messages::ACHAT_SUCCESS);
    }

    #[Route('/commande/{id}', name: 'afficheCommande', methods: 'GET')]
    public function afficheCommande($id): Response
    {
        $commandes = $this->repository->commandeRepository()->findBy(['user'=>$id],['id'=>'DESC']);
        return $this->reponses->success(array_map(function (Commande $commande) {
            return $commande->tojson();
        }, $commandes), count($commandes), Messages::SUCCESS);
    }

    #[Route('/commande/{id}', name: 'suprimerCommande', methods: 'DELETE')]
    public function suprimerCommande($id): Response
    {
        $commande = $this->repository->commandeRepository()->find($id);
        $this->service->em()->remove($commande);
        $this->service->em()->flush();
        return $this->reponses->success(null,null,Messages::SUCCESS_DELETE);
    }

}
