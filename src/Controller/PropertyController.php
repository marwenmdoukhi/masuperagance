<?php

namespace App\Controller;

use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Property;

class PropertyController extends AbstractController
{
    /*
    injection de repo pour reduit le code et quand on'a pleuseur methode qui utilise le repository
    le mieux injectet et fait l'appele
     */

    /**
     * @var PropertyRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/property", name="property_index")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */

    /*
     * autre methode de le mthre de le paramtre avecc autoohwring comme dans le demo
     * public function index(PropertyRepository $repository)
     */
    public function index(PaginatorInterface $paginator,Request $request)
    {
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);


        /*$property = new Property();
        $property->setTitle('Mon premier bien')
            ->setPrice('200000')
            ->setRooms(4)
            ->setBedrooms(3)
            ->setDescription('Une petite description')
            ->setSurface(60)
            ->setFloor(4)
            ->setHeat(1)
            ->setCity('Montpellier')
            ->setAddress('Avenue Gambetta')
            ->setPostalCode('34000');
        $em = $this->getDoctrine()->getManager();
        $em->persist($property);
        $em->flush(); */

        /* methode classic sans injection de reposottry et afficher les données et il ya autres methode
        dans projet demo
        $entityManager = $this->getDoctrine()->getManager();
        $property = $entityManager->getRepository(Property::class)->findAllVisble();
        */


        $property = $paginator->paginate(
            $this->repository->findAllVisbleQuery($search),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('property/index.html.twig', [
            'menu_courant' => 'properties',
            'properties'=>$property,
            'form'     => $form->createView()
        ]);
    }

    /**
     * @Route("/property/{slug}-{id}", name="property_show" , requirements={"slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @param string $slug
     * @return RedirectResponse|Response
     */
     public function show(Property $property,string $slug)
     {
         /* quand on change dans url le slug el redrige auto dans le meme pages et il est tres important pour
         le refrancment */
         if ($property->getSlug() !== $slug) {
             return $this->redirectToRoute('property_show', [
                 'id' => $property->getId(),
                 'slug' => $property->getSlug()
             ], 301);
         }

         return $this->render('property/show.html.twig', [
             'property'=>$property,
             'menu_courant' => 'properties'
         ]);     }


}
