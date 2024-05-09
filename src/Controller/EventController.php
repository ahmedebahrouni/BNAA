<?php

namespace App\Controller;

// Use the REST API Client to make requests to the Twilio REST API
use App\Repository\UserRepository;
use FPDF;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twilio\Rest\Client;
use App\Form\EvenementType;
use App\Repository\OrganisateurRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Evenement;
use App\Form\SearchEventType;
use App\Form\TriEventType;
use App\Repository\EvenementRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use App\Controller\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Serializer\Annotation\Groups;
class EventController extends AbstractController

{
    /**
     * @Route("/EventJson/{id}", name="EventJson")
     */
    public function SeulEvent(NormalizerInterface $Normalizer,$id)
    {
        $repository=$this->getDoctrine()->getRepository(Evenement::class);
        $event=$repository->EventID($id);
        $jsonContent=$Normalizer->normalize($event,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }


    /**
     * @Route("/allEvents", name="allEvents")
     */
    public function AllEvents(NormalizerInterface $Normalizer)
    {
        $repository=$this->getDoctrine()->getRepository(Evenement::class);
        $events=$repository->findAll();
        $jsonContent=$Normalizer->normalize($events,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }
    /**
     * @Route("/deleteEventJSON/{id}", name="deleteEventJSON")
     */
    public function deleteJSON($id, EvenementRepository $repository,Request  $request,NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $event=$repository->find($id);
        $em->remove($event);
        $em->flush();
        $jsonContent=$Normalizer->normalize($event,'json',['groups'=>'post:read']);
        return new Response("Vous avez supprimé l'evenement : ".json_encode($jsonContent));
    }
    /**
     * @Route("/addEventJSON/new", name="addEvent")
     */
    public function addEventJSON(OrganisateurRepository $rep,Request $request,NormalizerInterface $Normalizer)
    {
        $event = new Evenement();
        $event->setNomEvenement($request->get('nomEvenement'));
        $datedeb    =   \DateTime::createFromFormat("d-m-y",$request->get('dateDebut'));
        $datefin    =   \DateTime::createFromFormat("d-m-y",$request->get('dateFin'));
        $event->setDateDebut($datedeb);
        $event->setDateFin( $datefin );
        $event->setDescription($request->get('description'));
        $event->setEmplacement($request->get('emplacement'));
        $event->setImage($request->get('image'));
        $event->setNbrParticipant($request->get('nbrParticipant'));
        $event->setNbrMaxParticipant($request->get('nbrMaxParticipant'));
        $org=$rep->find($request->get('Organisateur'));
        $event->setOrganisateur($org);
        $event->setPrix($request->get('prix'));
        $em = $this->getDoctrine()->getManager();

        $em->persist($event);
        $em->flush();
        $jsonContent = $Normalizer->normalize($event,'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/updateEventJSON/{id}", name="updateEventJSON")
     */
    public function updateEventJSON($id,EvenementRepository $repo,OrganisateurRepository $rep,Request $request,NormalizerInterface $Normalizer)
    {
        $event=$repo->find($id);
        $event->setNomEvenement($request->get('nomEvenement'));
        $datedeb    =   \DateTime::createFromFormat("d-m-y",$request->get('dateDebut'));
        $datefin    =   \DateTime::createFromFormat("d-m-y",$request->get('dateFin'));
        $event->setDateDebut($datedeb);
        $event->setDateFin( $datefin );
        $event->setDescription($request->get('description'));
        $event->setEmplacement($request->get('emplacement'));
        $event->setImage($request->get('image'));
        $event->setNbrMaxParticipant($request->get('nbrMaxParticipant'));
        $org=$rep->find($request->get('Organisateur'));
        $event->setOrganisateur($org);
        $event->setPrix($request->get('prix'));
        $em = $this->getDoctrine()->getManager();


        $em->flush();
        $jsonContent = $Normalizer->normalize($event,'json',['groups'=>'post:read']);
        return new Response("Evenement modifié".json_encode($jsonContent));
    }
    /**
     * @Route("/full", name="full")
     */
    public function fullcalendar(EvenementRepository $repository,OrganisateurRepository $repo): Response
    {

        $events = $repository->findAll();
        $evenements=[];
        foreach($events as $event){
            $evenements[] = [
                'id' => $event->getReference(),
                'start' => $event->getDateDebut()->format('Y-m-d H:i:s'),
                'end' => $event->getDateFin()->format('Y-m-d H:i:s'),
                'title' => $event->getNomEvenement(),
                'description' => $event->getDescription(),
                'backgroundColor' => 'rgb(250,169,0)'
            ];
        }
        $data = json_encode($evenements);
        return $this->render('event/fullcalendar.html.twig', [
            'data'=>$data
        ]);

    }



    /**
     * @Route("/eventFront", name="eventFront")
     */
    public function index(EvenementRepository $repository,OrganisateurRepository $repo): Response
    {
        $org=$repo->findAll();
        $events = $repository->findAll();
        return $this->render('event/frontEvents.html.twig', [
            'events' => $events,
            'organisateurs' => $org
        ]);;

    }
    /**
     * @Route("/eventFront/{id}", name="eventFront1")
     */
    public function ListEventOrg($id,EvenementRepository $repository,OrganisateurRepository $repo): Response
    {
        $org=$repo->findAll();
        $events=$repository->Eventlist($id);
        return $this->render('event/eventOrfFront.html.twig', [
            'events' => $events,
            'organisateurs' => $org
        ]);;

    }
    /**
     * @Route("/eventOverview/{id}", name="eventOverview")
     */
    public function eventOverview($id,EvenementRepository $repository): Response
    {
        $event = $repository->find($id);
        return $this->render('event/eventOverview.html.twig', [
            'event' => $event
        ]);;

    }

    /**
     * @Route("/event/ajouterEv", name="ajouter_event")
     */
    public function ajouterEvent(Request $request)
    {
        $event = new Evenement();
        $form = $this->createForm(EvenementType::class, $event);
        $form->add('Save', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
           // $file1 = $this->getParameter('kernel.project_dir').'/public/images';
                $file->move($this->getParameter('images_directory'),
                   $fileName
               );
            } catch (FileException $e) {
                //....
            }
            $em = $this->getDoctrine()->getManager();
            $event->setImage($fileName);
            $em->persist($event);
            $em->flush();

          /*  $sid = 'ACb03984292fc7188bd0c4c61b212da3f9';
            $token = 'b5a96ec045d9df51536409046b58d3f0';
            $client = new Client($sid, $token);
            $date=date_format($event->getDateDebut(), 'y-M-d');
// Use the client to do fun stuff like send text messages!
            $client->messages->create(
            // the number you'd like to send the message to
                '+21697976510',
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => '+15075754654',
                    // the body of the text message you'd like to send

                    'body' => 'l evenement '.$event->getNomEvenement().'aura lieu le '.$date.' n hesitez pas a nous contacter pour plus d infos. '
                ]
            );*/
            return $this->redirectToRoute('consulter_event');
        }
        return $this->render('event/ajouterEvent.html.twig', [
            'form' => $form->createView()
        ]);;
    }

    /**
     * @Route("/event/afficher", name="consulter_event")
     */
    public function afficher(Request $request,EvenementRepository $repository)
    {
        $triEventForm = $this->createForm(TriEventType::class);
        $triEventForm->add('Trier', SubmitType::class);
        $searchEventForm = $this->createForm(SearchEventType::class);
        $searchEventForm->add('Rechercher', SubmitType::class);
        $events = $repository->findAll();
        if ($searchEventForm->HandleRequest($request)->isSubmitted() && $searchEventForm->isValid()) {
            $criteria = $searchEventForm->getData();

            $events = $repository->SearchEvent($criteria);
            return $this->render('event/consulterEvent.html.twig', [
                'search_form' => $searchEventForm->createView(),
                'tri_form' => $triEventForm->createView(),
                'events' => $events
            ]);;
        }
        if ($triEventForm->HandleRequest($request)->isSubmitted() && $triEventForm->isValid()) {
            $criteria = $triEventForm->getData();

            $events = $repository->TrierEvent($criteria);
            return $this->render('event/consulterEvent.html.twig', [
                'search_form' => $searchEventForm->createView(),

                'tri_form' => $triEventForm->createView(),
                'events' => $events
            ]);;
        }

        return $this->render('event/consulterEvent.html.twig', [

            'search_form' => $searchEventForm->createView(),
            'tri_form' => $triEventForm->createView(),
            'events' => $events


        ]);
    }

    /**
     * @Route("/event/afficher2", name="consulter2_event")
     */
    public function afficherEvAujourdhui(EvenementRepository $repository)
    {
        $value = new \DateTime();

        $time = $value->format('Y-m-d');
        $events = $repository->Eventaujourdhui($time);
        return $this->render('event/consulterEventAujourdhui.html.twig', [
            'events' => $events
        ]);;
    }
    /**
     * @Route("/event/participerEvent/{id}", name="participer_event")
     */
    public function participer($id,EvenementRepository $repository)
    {

        $event=$repository->find($id);
        return $this->render('event/participer_event.html.twig', [
            'event' => $event
        ]);;
    }
    /**
     * @Route("/checkout/{id}", name="checkout")
     */
    public function checkout($stripeSK,$id,EvenementRepository $repository): Response
    {
        $event=$repository->find($id);
        Stripe::setApiKey($stripeSK);


        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => $event->getNomEvenement(),
                        ],
                        'unit_amount'  => $event->getPrix(),
                    ],
                    'quantity'   => 1,
                ]
            ],
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('success_url', ['id'=>$event->getReference()], UrlGeneratorInterface::ABSOLUTE_URL,array('id' => '25')),
            'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }


    /**
     * @Route("/success_url", name="success_url")
     */
    public function successUrl(EvenementRepository $repository, UserRepository $userRepository, SessionInterface $session): Response
    {
        $id=$_GET['id'];
        $connectedUser = $session->get('user');

        // Check if the connected user exists and has an ID
        if ($connectedUser) {
            $userId = $connectedUser->getIdUser(); // Get the ID of the connected user
            $event = $repository->find($id); // Find the event by ID

            // Update the number of participants for the event
            $event->setNbrParticipant($event->getNbrParticipant() + 1);

            // Create a new reservation
            $reservation = new Reservation();
            $reservation->setRefEvenement($id);
            $reservation->setDateReservation((new \DateTime('now'))->format('Y-m-d')); // Convert \DateTime to string
            $reservation->setIdUser($userId); // Set the user ID for the reservation

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('ticket', ['id' => $id]);
        } else {
            // Handle case when the user is not connected
            // You might want to redirect the user to the login page or show an error message
            return $this->redirectToRoute('login');
        }
    }

    /**
     * @Route("/cancel_url", name="cancel_url")
     */
   public function cancelUrl(): Response
    {
        return $this->render('event/cancel.html.twig', []);
    }

    /**
     * @Route("/event/delete/{id}", name="delete_event")
     */
    public function supprimer($id, EvenementRepository $repository)
    {
        $event = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute('consulter_event');
    }


    /**
     * @Route("/event/editFull/{id}", name="editFull_event")
     */
    public function editFull(Request $request, Evenement $evenement)
    {
        $donnees = json_decode($request->getContent());
        if(
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor)
        ){
            $code = 200;
            if(!$evenement){
                $evenement = new Evenement();
                $code = 201;
            }
            $evenement->setNomEvenement($donnees->title);
            $evenement->setDateDebut(new \DateTime($donnees->start));
            $evenement->setDateFin(new \DateTime($donnees->end));

            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);
            $em->flush();
            return new Response('OK',$code);
        }else{
            return new Response('Données incompletes', 404);
        }



    }

    /**
     * @Route("/event/edit/{id}", name="edit_event")
     */
    public function edit(Request $request, EvenementRepository $repository,$id)
    {
        $evenement=$repository->find($id);
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->add('Save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
               // $file1 = $this->getParameter('kernel.project_dir').'/public/images';
                $file->move($this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                //....
            }
            $em = $this->getDoctrine()->getManager();
            $evenement->setImage($fileName);
            $em->flush();
            return $this->redirectToRoute('consulter_event');

        }

        return $this->render('event/ajouterEvent.html.twig', [
            'form' => $form->createView()
        ]);;

    }

    /**
     * @Route("/event/rechercher", name="rechercher_event")
**/
    public function Rechercher(Request $request, EvenementRepository $repository): Response
    {
        $searchEventForm = $this->createForm(SearchEventType::class);
        $searchEventForm->add('Rechercher', SubmitType::class);
        $events = $repository->findAll();
        if ($searchEventForm->HandleRequest($request)->isSubmitted() && $searchEventForm->isValid()) {
            $criteria = $searchEventForm->getData();

            $events = $repository->SearchEvent($criteria);
            return $this->render('event/consulterEvent.html.twig', [
                'search_form' => $searchEventForm->createView(),
                'events' => $events
            ]);;
        }

        return $this->render('event/consulterEvent.html.twig', [

            'search_form' => $searchEventForm->createView(),
            'events' => $events


        ]);

    }
    /**
     * @Route("/event/ticket/{id}", name="ticket")
     */







    public function imprimerPDF(EvenementRepository $repo, SessionInterface $session, $id, UrlGeneratorInterface $urlGenerator)
    {
        // Retrieve event details
        $event = $repo->find($id);

        // Create PDF object
        $pdf = new FPDF();

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('Arial', 'B', 16);

        // Add ticket background image
        $pdf->Image('c:/xampp/ticket1.png', 0, 0, 210, 89); // Adjusted dimensions

        // Set starting position for text
        $pdf->SetXY(27, 59);

        // Retrieve the connected user from the session
        $connectedUser = $session->get('user');

        // Add connected user's name and last name
        if ($connectedUser) {
            $pdf->Cell(40, 0, 'Nom: ' . $connectedUser->getNom(), 0, 0, 'L');
            $pdf->Cell(40, 0, 'Prenom: ' . $connectedUser->getPrenom(), 0, 0, 'L');
        }

        // Add reservation date
        $reservationDate = date('d-m-Y H:i:s'); // Current system date and time
        $pdf->SetXY(27, 60);
        $pdf->Cell(40, 40, 'Date de reservation: ' . $reservationDate, 0, 0, 'L');

        // Add event details
        $pdf->SetXY(27, 100);
        $pdf->Cell(90, -160, 'Date de l\'evenement: ' . date_format($event->getDateDebut(), 'd-m-Y'), 0, 0, 'L');
        $pdf->Cell(270, -70, 'Emplacement: ' . $event->getEmplacement(), 0, 0, 'L');
        $pdf->SetXY(69, 30);
        $pdf->SetFillColor(0, 31, 153);
        $pdf->SetFont('Times', 'BI', 30);
        $pdf->Cell(10, 10, $event->getNomEvenement(), 0, 0, 'L');
        $pdf->SetXY(177, 10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Prix: ' . $event->getPrix() . '$', 0, 0, 'L');
        // Add a button for redirection
        $pdf->SetFillColor(0, 255, 0); // Background color (adjust as needed)
        $pdf->SetTextColor(0, 0, 0); // Text color (white in this case)
        $pdf->SetDrawColor(18, 99, 57); // Border color (black in this case)
        $pdf->SetLineWidth(1); // Border width

        $pdf->SetXY(160, 100); // Adjust coordinates as needed
        $pdf->Cell(40, 10, 'Go to Home', 1, 0, 'C', true, $urlGenerator->generate('home1_front'));


        // Output PDF
        $pdfData = $pdf->Output('ticket.pdf', 'S');

        // Create Response object
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="ticket.pdf"');
        $response->setContent($pdfData);

        // Send the response
        $response->send();


    }





    /**
     * @Route("/search/back", name="SearchAjax", methods={"GET"})
     */

    public function SearchAjax(Request $request, EvenementRepository $repository): Response
    {
        $triEventForm = $this->createForm(TriEventType::class);
        $triEventForm->add('Trier', SubmitType::class);
        $requestString = $request->get('searchValue');
        $events = $repository->EventNom($requestString);

        return $this->render('event/consulterEvent.html.twig', [
            "events" => $events,
            'tri_form' => $triEventForm->createView()
        ]);
    }
}
