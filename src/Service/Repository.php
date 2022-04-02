<?php 
namespace App\Service;

use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\FleurieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Repository extends AbstractController
{

    private CategoryRepository $categoryRepository;
    private UserRepository $userRepository;
    private CommandeRepository $commandeRepository;
    private FleurieRepository $fleurieRepository;

   
    public function __construct(CategoryRepository $categoryRepository,UserRepository $userRepository,CommandeRepository $commandeRepository,FleurieRepository $fleurieRepository)
    {
        $this->categoryRepository=$categoryRepository;
        $this->userRepository=$userRepository;
        $this->commandeRepository=$commandeRepository;
        $this->fleurieRepository=$fleurieRepository;
    }

    public function userRepository()
    {
        return $this->userRepository;
    }

    public function categoryRepository()
    {
        return $this->categoryRepository;
    }

    public function commandeRepository()
    {
        return $this->commandeRepository;
    }

    public function fleurieRepository()
    {
        return $this->fleurieRepository;
    }


}