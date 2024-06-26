<?php

namespace App\Controller;
use App\Entity\Sousservice;
use App\Entity\Imagess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SousserviceRepository;
use App\Repository\ServiceRepository;
use App\Repository\CategEventRepository;
use App\Repository\PersonneRepository;
use App\Form\SousserviceType;
use App\Form\SousserviceType_edit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ImagessRepository;
use App\Repository\AvisRepository;
use App\Repository\reservationRepository;
use App\Repository\ImagePersRepository;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/sousserviceFront')]
class SousserviceFrontController extends AbstractController
{
//affichage des sousservices -----------------------------------------------------------------------------------------------------------------
    #[Route('/', name: 'app_sousservice_front', methods: ['GET'])]
    public function index(
        SousserviceRepository $SousserviceRepository,
        ServiceRepository $ServRepository,
        CategEventRepository $CategEventRepository,
        Request $request,
        SessionInterface $session,
        ImagePersRepository $imagePersRepository,
        ImagessRepository $ImagessRepository,
        AvisRepository $AvisRepository,
        PaginatorInterface $paginator,
        // ImagessController $c
    ): Response {
        // $c->test();
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
        $search = $request->query->get('search1');
        $filter = null;
        $filter = $request->get('inputfilter');
        if ($filter) {
            $SousService = $SousserviceRepository->getAllByServiceName($filter);
        } else if ($search) {
            $SousService = $SousserviceRepository->findOneByName($search);
        } else {
            $SousService = $SousserviceRepository->findAll();
        }
        $listimg = [];
        foreach ($SousService as $serv) {
            $firstimg = $ImagessRepository->findBySousService($serv);
            if (!empty($firstimg)) {
                $listimg[] = $firstimg[0];
            
            }
        }

        // $SousService = $SousserviceRepository->findOneByName($search);
        $imagess = $ImagessRepository->findAll();
        foreach ($SousService as $s) {
            $checkboxes = explode(',', $s->getIdEventcateg());
            $list = [];
            foreach ($checkboxes as $c) {
                $list[] = $CategEventRepository->findOneByIdCateg($c)->getType();
            }
            $selectedCheckboxes = implode(',', $list);
            $s->setIdEventcateg($selectedCheckboxes);
        }
        
        $service = $ServRepository->findAll();
        $query = $SousService;
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            8 // limit per page
        );
        $av=$AvisRepository->findAll();
        $per=$imagePersRepository->findAll();
        return $this->render('templates_front/sousservice_front/index.html.twig', [
            'sousservices' => $pagination,
            'imagess' => $imagess,
            'options' => $service,
            'personne' => $personne,
            'eventCat' => $CategEventRepository->findAll(),
            'last' => $last,
            'firstimg' =>  $listimg,
            'avis'=>$av,
            'persimg'=>$per
        ]);
    }
//ajout d'un sousservice -----------------------------------------------------------------------------------------------------------------
    #[Route('/new', name: 'app_sousservice_new_front', methods: ['GET', 'POST'])]
    public function new(Request $request, SessionInterface $session, ImagePersRepository $imagePersRepository, CategEventRepository $CategEventRepository, PersonneRepository $PersonneRepository, SousserviceRepository $SousserviceRepository, ImagessRepository $imagessRepository, ServiceRepository $ServiceRepository): Response
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
        //----------------------------------------------
        $ss = new Sousservice();
        $form = $this->createForm(SousserviceType::class, $ss);
        $form->handleRequest($request);
        $errorMessage = null;
        $errorMessage1 = null;
        //-----------------------------------------------
        if ($form->isSubmitted()) {
            $cat = $request->request->get('my-checkbox');
            $desc = $form->get('description')->getData();
            $imag = $form->get('imagess')->getData();

            //------------------------------------------
            if ($cat) {
                $formIsValid1 = true;
            } else {
                $errorMessage = "selectionner au moin une categorie ";
                $formIsValid1 = false;
            }
            //-----------------------------------------
            if ($desc) {
                $formIsValid = true;
            } else {
                $formIsValid = false;
            }
            //------------------------------------------
            if ($imag ) {
                $formIsValid2 = true;
            } else {
                $errorMessage1 = "selectionner au moin une image ";
                $formIsValid2 = false;
            }
            //-----------------------------------------
            if ($formIsValid && $formIsValid1 && $formIsValid2 && $form->isValid()) {
                //----------------------------------------
                $imageFile = $form->get('imagess')->getData();
                foreach ($imageFile as $imageFile) {
                    $imageFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $imageFilename  //configured fel config service.yaml                  
                    );
                    $imageEntity = new Imagess();
                    $imageEntity->setImg($imageFilename);
                    $ss->addImagess($imageEntity);
                }
                // ----------------------------------------
                $catev = $request->request->get('my-checkbox');

                $selectedCheckboxes = implode(',', $catev);
                $ss->setIdEventcateg($selectedCheckboxes);
                $ss->setIdPers($PersonneRepository->findOneByIdPers($idPerss));
                $ss->setNote(0);
                // $SousserviceRepository->save($ss, true);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($ss);
                $entityManager->flush();
                return $this->redirectToRoute('app_sousservice_front', [
                    // 'idss' => $ss->getId(),
                    // 'personne' => $personne,
                    // 'last' => $last,
                ], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->renderForm('templates_front/sousservice_front/new.html.twig', [
            'eventCat' => $CategEventRepository->findAll(),
            'selectedCategories' => "",
            'sousservice' => $ss,
            'form' => $form,
            'personne' => $personne,
            'last' => $last,
            'errorMessage' => $errorMessage,
            'errorMessage1' => $errorMessage1,
        ]);
    }
//modifier sous service -------------------------------------------------------------------------------------------------
    #[Route('/{id}/edit', name: 'app_sousservice_edit_front', methods: ['GET', 'POST'])]
    public function edit1(Request $request, ImagessRepository $ImagessRepository, SessionInterface $session, ImagePersRepository $imagePersRepository, CategEventRepository $CategEventRepository, PersonneRepository $PersonneRepository, Sousservice $sousservice, SousserviceRepository $SousserviceRepository): Response
    {
        $personne = $session->get('id');
        $idPerss = $session->get('personne');
        $images = $imagePersRepository->findBy(['idPers' => $idPerss]);
        $images = array_reverse($images);
        $iml = $ImagessRepository->findBysousService($sousservice);
        if (!empty($images)) {
            $i = $images[0];
            $last = $i->getLast();
        } else {
            $last = "account (1).png";
        }
        $session->set('last', $last);
        $last = $session->get('last');

        $form = $this->createForm(SousserviceType_edit::class, $sousservice);
        $form->handleRequest($request);
        $errorMessage = null;
        $errorMessage1 = null;
        $checkboxes = explode(',', $sousservice->getIdEventcateg());
        $list = [];
        foreach ($checkboxes as $c) {
            $list[] = $CategEventRepository->findOneByIdCateg($c);
        }
        if ($form->isSubmitted()) {
            $cat = $request->request->get('my-checkbox');
            $desc = $form->get('description')->getData();
            $imag = $form->get('imagess')->getData();
            if ($cat) {
                $formIsValid = true;
            } else {
                $errorMessage = "Il faut selectionner au moin une categorie ";
                $formIsValid = false;
            }

            if ($desc) {
                $formIsValid1 = true;
            } else {
                // $errorMessage1 = "Il faut remplir le champ description ";
                $formIsValid1 = false;
            }

            $imageFile = $form->get('imagess')->getData();
            foreach ($imageFile as $imageFile) {

                $imageFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFilename  //configured fel config service.yaml                  
                );
                $imageEntity = new Imagess();
                // $imageEntity->setSousService($sousservice);
                $imageEntity->setImg($imageFilename);
                $sousservice->addImagess($imageEntity);
            }

            // ----------------------------------------
            if ($formIsValid && $formIsValid1  && $form->isValid()) {
                $catev = $request->get('my-checkbox');
                $selectedCheckboxes = implode(',', $catev);
                $sousservice->setIdEventcateg($selectedCheckboxes);
                $sousservice->setIdPers($personne);
                $sousservice->setNote(0);
                // $sousservice->setImagess($iml[0]->getImg());
                // $SousserviceRepository->save($sousservice, true);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($sousservice);
                $entityManager->flush();
                return $this->redirectToRoute('app_sousservice_front', [
                    // 'idss' => $ss->getId(),
                    // 'personne' => $personne,
                    // 'last' => $last,
                ], Response::HTTP_SEE_OTHER);
                // return $this->redirectToRoute('app_sousservice_front', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('templates_front/sousservice_front/edit1.html.twig', [
            'eventCat' => $CategEventRepository->findAll(),
            'selectedCategories' => $list,
            'sousservice' => $sousservice,
            'form' => $form,
            'personne' => $personne,
            'last' => $last,
            'imglist' => $iml,
            'errorMessage' => $errorMessage,
            'errorMessage1' => $errorMessage1,
        ]);
    }
//suppression sous service-------------------------------------------------------------------------------------------------
    #[Route('/{id}', name: 'app_sousservice_delete_front', methods: ['POST'])]
    public function delete(FlashyNotifier $flashy, reservationRepository $reservationRepository,Request $request, Sousservice $sousservice, SousserviceRepository $SousserviceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sousservice->getId(), $request->request->get('_token'))) {
            $reservations = $reservationRepository->findAll();
            $is_reserved = false;
    
            foreach ($reservations as $reservation) {
                $souservices = $reservation->getIdSs();
                $ss[$reservation->getIdRes()] = explode(',', $souservices);
    
                foreach ($ss[$reservation->getIdRes()] as $s) {
                    $list[$reservation->getIdRes()] = $SousserviceRepository->findById($s);
                }
    
                foreach ($list[$reservation->getIdRes()] as $l) {
                    if ($l->getId() == $sousservice->getId()) {
                        $is_reserved = true;
                        break 2;
                    }
                }
            }
    
            if ($is_reserved) {
                // $flashy->error("Impossible de supprimer ce sous-service, car il est réservé.");
            } else {
                $SousserviceRepository->remove($sousservice, true);
                // $flashy->success("Le sous-service a été supprimé avec succès.");
            }
        }
    
        return $this->redirectToRoute('app_sousservice_front',[], Response::HTTP_SEE_OTHER);
    }
//search ---------------------------------------------------------------------------------------    
    #[Route('/searchSS', name: 'app_sousservice_search_front', methods: ['GET'])]
    public function search(SousserviceRepository $SousserviceRepository, Request $request)
    {
        $searchTerm = $request->query->get('search1');


        $response = [];
        $serv = $SousserviceRepository->findOneByName($searchTerm);
        foreach ($serv as $s) {
            $response[] = [
                'sousservice' => $s
            ];
        }

        return new JsonResponse($response);
    }
}
