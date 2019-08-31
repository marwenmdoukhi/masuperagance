<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


    /**
     * @Route("/", name="home")
     * @param PropertyRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PropertyRepository $repository)
    {
        $properties= $repository->findAll();
        return $this->render('pages/index.html.twig',['properties'=>$properties] );
    }
}
