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
        $data = $this->service->json_decode();
        $user = $this->repository->userRepository()->findOneById($data->idUser);

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
        $commandes = $this->repository->commandeRepository()->findByUser($id);
        return $this->reponses->success(array_map(function (Commande $commande) {
            return $commande->tojson();
        }, $commandes), count($commandes), Messages::SUCCESS);
    }
}
