<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;


    public function __construct(PropertyRepository $repository,ObjectManager $em)
    {

        $this->repository = $repository;
        $this->em = $em;
    }


    /**
     * @Route("/admin", name="admin.property.index")
     */
    public function index()
    {
        $properties=$this->repository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));

    }

    /**
     * @Route("/admin/property/create" ,name="admin.property.new")
     */
    public function new(Request $request){
        $property=new Property();
        $form = $this->createForm(PropertyType::class,$property);
        $form->handleRequest($request);
        if ( $form->isSubmitted() &&$form->isValid() ) {
            $this->em->persist($property);
            $this->em->flush();
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/new.html.twig', [
            'property'=>$property,
            'form'=> $form->createView()
        ]);
    }
    /*
     * edit et delte a le meme rout mais pas le meme methode il faut delcaré la mthode
     * et crée une form dans suprsion pour protection votre form de piartge ou modifier les donnés
     * edit on donné le mthode get et post et supprimer methods="DELETE" pour ne faire conflir entre
     * les deux function et a des 2 lien deffrent
     */

    /**
     * @Route("/admin/property/{id}", name="admin.property.edit" ,methods="GET|POST")
     */

    public function edit(Property $property,Request $request){


       $form = $this->createForm(PropertyType::class,$property);
       $form->handleRequest($request);
       if ( $form->isSubmitted() &&$form->isValid() ) {
           $this->em->flush();
           $this->addFlash("success","bien edit");
           return $this->redirectToRoute('admin.property.index');
       }
        return $this->render('admin/property/edit.html.twig', [
            'property'=>$property,
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/admin/property/{id}", name="admin.property.delete", methods="DELETE")
     * @param Property $property
     * @return RedirectResponse
     */
    public function delete(Property $property,Request $request){
        if ($this->isCsrfTokenValid('delete'.$property->getId(),$request->get('_token'))){
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash("success","bien supprimer");
        }
        return $this->redirectToRoute('admin.property.index');

    }

}
