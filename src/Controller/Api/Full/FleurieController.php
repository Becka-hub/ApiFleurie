<?php

namespace App\Controller\Api\Full;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Shared\Reponses;
use App\Shared\Messages;
use App\Service\Service;
use App\Service\Repository;
use App\Entity\Fleurie;

class FleurieController extends AbstractController
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

    #[Route('/fleurie', name: 'ajouteFleurie', methods: 'POST')]
    public function ajouteFleurie(Request $request): Response
    {
        $libelle = $request->request->get('libelle');
        $prix = $request->request->get('prix');
        $description = $request->request->get('dl0escription');
        $idCategory = $request->request->get('idCategory');
        $image = $request->files->get('image');

        if (!isset($libelle, $prix, $description, $image) || ($libelle === "" || $prix === "" || $description === "" || $image === "")) {
            return $this->reponses->error(Messages::FORM_INVALID);
        }
        $fleurie = $this->repository->fleurieRepository()->findOneBy(['libelle' => $libelle]);
        if ($fleurie) {
            return $this->reponses->error(Messages::FLEURIE_EXIST);
        }
        $imageName = $this->service->uploadFile($image, $this->getParameter('brochures_directory_fleurie'));
        $imageUrl="/uploads/fleurs/".$imageName;
        $category = $this->repository->categoryRepository()->findOneBy(['id' => $idCategory]);

        $fleuries = new Fleurie();
        $fleuries->setLibelle($libelle);
        $fleuries->setPrix($prix);
        $fleuries->setDescription($description);
        $fleuries->setImage($imageUrl);
        $fleuries->setCategory($category);
        $this->service->em()->persist($fleuries);
        $this->service->em()->flush();
        return $this->reponses->success($fleuries->tojson(), 1, Messages::SUCCESS_INSERT);
    }

    #[Route('/fleurie/{id}', name: 'suprimerFleurie', methods: 'DELETE')]
    public function suprimerFleurie($id): Response
    {
        $fleurie = $this->repository->fleurieRepository()->find($id);
        if (!$fleurie) {
            return $this->reponses->error(Messages::FLEUR_NOT_FOUND);
        }
        $nameImage = $this->getParameter('brochures_directory_fleurie') . '/' . $fleurie->getImage();
        if (file_exists($nameImage)) {
            unlink($nameImage);
        }
        $this->service->em()->remove($fleurie);
        $this->service->em()->flush();
        return $this->reponses->success(null, null, Messages::SUCCESS_DELETE);
    }

    #[Route('/fleurie', name: 'afficheFleurie', methods: 'GET')]
    public function afficheFleurie(): Response
    {
        $fleuries = $this->repository->fleurieRepository()->findAll();
        return $this->reponses->success(array_map(function (Fleurie $fleurie) {
            return $fleurie->tojson();
        }, $fleuries), count($fleuries), Messages::SUCCESS);
    }


    #[Route('/fleurie/{id}', name: 'afficheOneFleurie', methods: 'GET')]
    public function afficheOneFleurie($id): Response
    {
       $fleurie=$this->repository->fleurieRepository()->find($id);
       return $this->reponses->success($fleurie->tojson(),1,Messages::SUCCESS);
    }
}
