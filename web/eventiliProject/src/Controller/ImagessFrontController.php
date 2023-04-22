<?php

namespace App\Controller;

use App\Entity\Imagess;
use App\Entity\Sousservice;
use App\Form\ImagessType;
use App\Repository\ImagessRepository;
use App\Repository\SousserviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ImagePersRepository;

#[Route('/imagessfront')]
class ImagessFrontController extends AbstractController
{
    #[Route('/', name: 'app_imagess_index', methods: ['GET'])]
    public function index(ImagessRepository $imagessRepository, SessionInterface $session, ImagePersRepository $imagePersRepository): Response
    {
        $personne = $session->get('id');
        $idPerss = $session->get('personne');
        $images = $imagePersRepository->findBy(['idPers' => $idPerss]);
        $images = array_reverse($images);

        if (!empty($images)) {
            $i = $images[0];
            $last = $i->getLast();
        } else {
            $last = "account (1).png";
        }
        $session->set('last', $last);
        $last = $session->get('last');
        return $this->render('templates_back/imagess/index.html.twig', [
            'imagess' => $imagessRepository->findAll(),
            'personne' => $personne,
            'last' => $last,
        ]);
    }
    //------------------------------------------------------------------------------------------------
    #[Route('/new', name: 'app_imagess_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SousserviceRepository $SousserviceRepository, ImagessRepository $imagessRepository, SessionInterface $session, ImagePersRepository $imagePersRepository): Response
    {
        $personne = $session->get('id');
        $idPerss = $session->get('personne');
        $images = $imagePersRepository->findBy(['idPers' => $idPerss]);
        $images = array_reverse($images);

        if (!empty($images)) {
            $i = $images[0];
            $last = $i->getLast();
        } else {
            $last = "account (1).png";
        }
        $session->set('last', $last);
        $last = $session->get('last');
        $imagess = new Imagess();
        // $i = new Imagess();
        $ss = $request->query->get('idss'); //get id of sous service
        //create the form
        $form = $this->createForm(ImagessType::class, $imagess);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //image upload
            $imageFile = $form->get('imageFile')->getData();
            // $tabImg=[];
            // foreach($imageFiles as $imageFile)
            // {
            // if ($imageFile) {
            //     $imageFilename = uniqid() . '.' . $imageFile->guessExtension();
            //     $imageFile->move(
            //         $this->getParameter('images_directory'),
            //         $imageFilename  //configured fel config service.yaml                  
            //     );
            //     // $tabImg[]=$imageFilename;
            //     // foreach($tabImg as $t){
            //     $imagess->setImg($imageFilename);
            //     // }
            // }
            $imagess->setSousService($SousserviceRepository->findOneById($ss));
            $imagessRepository->save($imagess, true);
            // }
            //setting image with sous service image and new images
            // $i->setImg($SousserviceRepository->findOneById($ss)->getImagess());
            // $i->setSousService($SousserviceRepository->findOneById($ss));
            // $imagessRepository->save($i, true);
            return $this->redirectToRoute('app_sousservice_index', [], Response::HTTP_SEE_OTHER);
        }
        //in case there's no image added in the extra image page
        //setting image only with sous service image  
        else if ($form->isSubmitted()) {
            // $i->setImg($SousserviceRepository->findOneById($ss)->getImagess());
            // $i->setSousService($SousserviceRepository->findOneById($ss));
            // $imagessRepository->save($i, true);
            return $this->redirectToRoute('app_sousservice_index', [], Response::HTTP_SEE_OTHER);
        }
        //calling imagess creation page
        return $this->renderForm('templates_back/imagess/new.html.twig', [
            'imagess' => $imagess,
            'form' => $form,
            'personne' => $personne,
            'last' => $last,
        ]);
    }
    // ------------------------------------------------------------------------------------------------
    #[Route('{id}/new1', name: 'app_imagess_new1', methods: ['GET', 'POST'])]
    public function new1(Request $request, Sousservice $Sousservice,SousserviceRepository $SousserviceRepository, ImagessRepository $imagessRepository, SessionInterface $session, ImagePersRepository $imagePersRepository): Response
    {
        $personne = $session->get('id');
        $idPerss = $session->get('personne');
        $images = $imagePersRepository->findBy(['idPers' => $idPerss]);
        $images = array_reverse($images);
        if (!empty($images)) {
            $i = $images[0];
            $last = $i->getLast();
        } else {
            $last = "account (1).png";
        }
        $session->set('last', $last);
        $last = $session->get('last');
        //------------------------------------
        $imagess = new Imagess();//imgess
        $i = new Imagess();//souserv
        // $ss = $request->query->get('idss'); //get id of sous service
        //create the form
        $form = $this->createForm(ImagessType::class, $imagess);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //image upload
            $imageFile = $form->get('img')->getData();
            // $tabImg=[];
            // foreach($imageFiles as $imageFile)
            // {
            if ($imageFile) {
                $imageFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFilename  //configured fel config service.yaml                  
                );
                // $tabImg[]=$imageFilename;
                // foreach($tabImg as $t){
                $imagess->setImg($imageFilename);
                // }
            }
            $imagess->setSousService($SousserviceRepository->findOneById($Sousservice));
            $imagessRepository->save($imagess, true);
            // }
            //setting image with sous service image and new images
            $i->setImg($SousserviceRepository->findOneById($Sousservice)->getImagess());
            $i->setSousService($SousserviceRepository->findOneById($Sousservice));
            $imagessRepository->save($i, true);
            return $this->redirectToRoute('app_sousservice_edit', [
                'id'=> $imagess->getSousService()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }
        //in case there's no image added in the extra image page
        //setting image only with sous service image  
        else if ($form->isSubmitted()) {
            $i->setImg($SousserviceRepository->findOneById($Sousservice)->getImagess());
            $i->setSousService($SousserviceRepository->findOneById($Sousservice));
            $imagessRepository->save($i, true);
            return $this->redirectToRoute('app_sousservice_edit', [
                'id'=> $imagess->getSousService()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }
        //calling imagess creation page
        return $this->renderForm('templates_back/imagess/new.html.twig', [
            'imagess' => $imagess,
            'form' => $form,
            'personne' => $personne,
            'last' => $last,
        ]);
    }
    //--------------------------------------------------------------------------------------------------
    #[Route('/{idimgss}', name: 'app_imagess_show', methods: ['GET'])]
    public function show(Imagess $imagess, SessionInterface $session, ImagePersRepository $imagePersRepository): Response
    {
        $personne = $session->get('id');
        $idPerss = $session->get('personne');
        $images = $imagePersRepository->findBy(['idPers' => $idPerss]);
        $images = array_reverse($images);

        if (!empty($images)) {
            $i = $images[0];
            $last = $i->getLast();
        } else {
            $last = "account (1).png";
        }
        $session->set('last', $last);
        $last = $session->get('last');
        return $this->render('templates_back/imagess/show.html.twig', [
            'imagess' => $imagess,
            'personne' => $personne,
            'last' => $last,
        ]);
    }
    //------------------------------------------------------------------------------------------------
    #[Route('/{idimgss}/edit', name: 'app_imagess_edit_front', methods: ['GET', 'POST'])]
    public function edit(Request $request, SousserviceRepository $SousserviceRepository, Imagess $imagess, SessionInterface $session, ImagePersRepository $imagePersRepository, ImagessRepository $imagessRepository): Response
    {
        $personne = $session->get('id');
        $idPerss = $session->get('personne');
        $images = $imagePersRepository->findBy(['idPers' => $idPerss]);
        $images = array_reverse($images);
        $errorMessage =null;

        if (!empty($images)) {
            $i = $images[0];
            $last = $i->getLast();
        } else {
            $last = "account (1).png";
        }
        $session->set('last', $last);
        $last = $session->get('last');
        $form = $this->createForm(ImagessType::class, $imagess);
        $form->handleRequest($request);
        $imag = $form->get('img')->getData();
        if ($imag) {
            $formIsValid = true;
        } else {
            $errorMessage = "Il faut selectionner une image ";
            $formIsValid = false;
        }
        if ($form->isSubmitted() && $formIsValid && $form->isValid()) {
            $imageFile = $form->get('img')->getData(); 
            if ($imageFile) {
                $imageFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFilename  //configured fel config service.yaml                  
                );
                $imagess->setImg($imageFilename);
            }
            $imagessRepository->save($imagess, true);
            return $this->redirectToRoute('app_sousservice_edit_front', ['id' => $imagess->getSousService()->getId()], Response::HTTP_SEE_OTHER);
        } else if ($form->isSubmitted()) {

            return $this->redirectToRoute('app_sousservice_edit_front', [
                'id' => $imagess->getSousService()->getId(),
                'errorMessage'=>$errorMessage 
            ], Response::HTTP_SEE_OTHER);
        }

        //------------------------------------------------------------------------------------------------
        return $this->renderForm('templates_front/imagess_front/edit.html.twig', [
            'imagess' => $imagess,
            'form' => $form,
            'personne' => $personne,
            'errorMessage'=>$errorMessage,
            'last' => $last,
        ]);
    }
    //------------------------------------------------------------------------------------------------
    #[Route('/{idimgss}', name: 'app_imagess_delete_front', methods: ['POST'])]
    public function delete(Request $request, Imagess $imagess, ImagessRepository $imagessRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $imagess->getIdimgss(), $request->request->get('_token'))) {
            $imagessRepository->remove($imagess, true);
        }

        return $this->redirectToRoute('app_sousservice_edit_front', [
            'id'=>$imagess->getSousService()->getId()
        ], Response::HTTP_SEE_OTHER);
    }
    //---------------------------------------------------------------------------------------------------
    #[Route('/findImagessById/{idimgss}', name: 'app_imagess_findImagessById', methods: ['GET'])]
    public function findImagessById(ImagessRepository $ImagessRepository, $idimgss, SessionInterface $session, ImagePersRepository $imagePersRepository): Response
    {
        $personne = $session->get('id');
        $idPerss = $session->get('personne');
        $images = $imagePersRepository->findBy(['idPers' => $idPerss]);
        $images = array_reverse($images);

        if (!empty($images)) {
            $i = $images[0];
            $last = $i->getLast();
        } else {
            $last = "account (1).png";
        }
        $session->set('last', $last);
        $last = $session->get('last');
        return $this->render('templates_back/imagess/index.html.twig', [
            'imagess' => $ImagessRepository->findby(array('idimgss' => $idimgss)),
            'personne' => $personne,
            'last' => $last,
        ]);
    }
    //---------------------------------------------------------------------------------------------------
    #[Route('/findImageByIdSS/{sousService}', name: 'app_imagess_findImageByIdSS', methods: ['GET'])]
    public function findImageByIdSS(ImagessRepository $ImagessRepository, $sousService, SessionInterface $session, ImagePersRepository $imagePersRepository): Response
    {
        $personne = $session->get('id');
        $idPerss = $session->get('personne');
        $images = $imagePersRepository->findBy(['idPers' => $idPerss]);
        $images = array_reverse($images);

        if (!empty($images)) {
            $i = $images[0];
            $last = $i->getLast();
        } else {
            $last = "account (1).png";
        }
        $session->set('last', $last);
        $last = $session->get('last');
        return $this->render('templates_back/imagess/index.html.twig', [
            'imagess' => $ImagessRepository->findby(array('sousService' => $sousService)),
            'personne' => $personne,
            'last' => $last,
        ]);
    }
}
