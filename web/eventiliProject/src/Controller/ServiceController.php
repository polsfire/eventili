<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/service')]
class ServiceController extends AbstractController
{
    #[Route('/', name: 'app_service_index', methods: ['GET', 'POST'])]
    public function index(ServiceRepository $serviceRepository, request $request): Response
    {

        $search = $request->query->get('search');
        if ($search) {
            $service = $serviceRepository->findOneByName($search);
        } else {
            $service = $serviceRepository->findAll();
        }
        $serv = new Service();
        $form = $this->createForm(ServiceType::class, $serv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->save($serv, true);

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/index.html.twig', [
            'service' => $serv,
            'services' => $service,
            'form' => $form,
        ]);
    }
    //-------------------------------------------------------------------------------------------------
    #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ServiceRepository $serviceRepository): Response
    {
        $serv = new Service();
        $form = $this->createForm(ServiceType::class, $serv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->save($serv, true);

            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/new.html.twig', [
            'service' => $serv,
            'form' => $form,
        ]);
    }
    //-------------------------------------------------------------------------------------------------
    #[Route('/{idService}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }
    // //-------------------------------------------------------------------------------------------------
    #[Route('/{idService}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceRepository->save($service, true);
            return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
        
    }
    // //-------------------------------------------------------------------------------------------------
    // #[Route('/{idService}/editt', name: 'app_service_edit1')]
    // public function edit1(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    // {
    //     $var = $request->get('inputService');
    //     $service->setNom($var);
    //     // $form = $this->createForm(ServiceType::class, $service);
    //     // $form->handleRequest($request);

    //     // if ($form->isSubmitted() && $form->isValid()) {
    //     $serviceRepository->save($service, true);

    //     return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    //     // }

    //     return $this->renderForm('service/edit.html.twig', [
    //         // 'service' => $service,
    //         // 'form' => $form,
    //     ]);
    // }
    //-------------------------------------------------------------------------------------------------
    #[Route('/{idService}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, ServiceRepository $serviceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $service->getIdService(), $request->request->get('_token'))) {
            $serviceRepository->remove($service, true);
        }

        return $this->redirectToRoute('app_service_index', [], Response::HTTP_SEE_OTHER);
    }
    //-------------------------------------------------------------------------------------------------
    #[Route('/findbyId/{idService}', name: 'app_service_findById', methods: ['GET'])]
    public function FindServiceById(ServiceRepository $serviceRepository, $idService): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findby(array('idService' => $idService)),
        ]);
    }
    //-------------------------------------------------------------------------------------------------    
    #[Route('/findbyName/{nom}', name: 'app_service_findByName', methods: ['GET'])]
    public function findByName(ServiceRepository $serviceRepository, $nom): Response
    {
        return $this->render('service/index.html.twig', [
            'services' =>  $serviceRepository->findOneByName($nom),
        ]);
    }
    //------------------------------------------------------------------------------------------------- 
    #[Route('/findbyNames/{nom}', name: 'app_service_findByNames', methods: ['GET'])]
    public function findByNames(ServiceRepository $serviceRepository, $nom): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findOneByNames($nom),
        ]);
    }
}
