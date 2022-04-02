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
use App\Entity\Category;

class CategorieController extends AbstractController
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

    #[Route('/category', name: 'ajouteCategory', methods: 'POST')]
    public function ajouteCategory(Request $request): Response
    {
        $libelle = $request->request->get('libelle');
        if (!isset($libelle) || $libelle === "") {
            return $this->reponses->error(Messages::FORM_INVALID);
        }
        $categorie = $this->repository->categoryRepository()->findOneBy(['libelle' => $libelle]);
        if ($categorie) {
            return $this->reponses->error(Messages::CATEGORIE_EXIST);
        }
        $category = new Category();
        $category->setLibelle($libelle);
        $this->service->em()->persist($category);
        $this->service->em()->flush();
        return $this->reponses->success($category->tojson(), 1, Messages::SUCCESS_INSERT);
    }


    #[Route('/category', name: 'afficheCategory', methods: 'GET')]
    public function afficheCategory(): Response
    {
        $categories = $this->repository->categoryRepository()->findAll();
        return $this->reponses->success(array_map(function (Category $category) {
            return $category->tojson();
        }, $categories), count($categories), Messages::SUCCESS);
    }


    #[Route('/category/{id}', name: 'suprimerCategory', methods: 'DELETE')]
    public function suprimerCategory($id): Response
    {
        $categorie = $this->repository->categoryRepository()->find($id);
        if (!$categorie) {
            return $this->reponses->error(Messages::CATEGORIE_NOT_FOUND);
        }
        $this->service->em()->remove($categorie);
        $this->service->em()->flush();
        return $this->reponses->success(null, null, Messages::SUCCESS_DELETE);
    }
}
