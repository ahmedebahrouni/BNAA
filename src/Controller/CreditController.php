<?php

namespace App\Controller;
use App\Repository\CreditRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Credit;
use App\Form\CreditType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CreditController extends AbstractController
{
    /**
     * @Route("/credit/CreditFront", name="CreditFront")
     */
    public function ajouterCredit(Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {

        $connectedUser = $session->get('user');

        $userId = $connectedUser->getIdUser(); // Get the ID of the connected user

// Create a new instance of the Credit entity
        $credit = new Credit();

// Pre-fill the auto-filled fields from the user data
        $credit->setRIB($connectedUser->getRIB());
        $credit->setSolde($connectedUser->getSolde());
        $credit->setStatuscompte($connectedUser->getStatuscompte());
        $credit->setSalaire($connectedUser->getSalaire()) ;




// Create the credit form and handle the request
        $form = $this->createForm(CreditType::class, $credit);
        $form->handleRequest($request);

// Process the form submission
        if ($form->isSubmitted() && $form->isValid()) {
// Update user fields if needed
$credit->setSalaire($connectedUser->getSalaire()) ;
            $credit->setRIB($connectedUser->getRIB());
            $credit->setSolde($connectedUser->getSolde());
            $credit->setStatuscompte($connectedUser->getStatuscompte());
            $credit->setIdUser($userId);
            $credit->setDateCredit((new \DateTime('now'))->format('Y-m-d')); // Convert \DateTime to string

// Persist the credit data
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($credit);
            $entityManager->flush();
            $popupScript = "<script>alert('Votre demande de crédit est en cours de traitement et devrait prendre 48 heures. Un email vous sera envoyé pour vous informer de la décision ');</script>";

            // Redirect the user after successful submission
            return new Response(
                $popupScript . $this->redirectToRoute('welcome_front')->getContent()
            );
        }

// Render the credit form
        return $this->render('credit/CreditFront.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param UserRepository $User
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/afichecredit",name="afichecredit")
     */
    public function afficher(CreditRepository $repository, Request $request, PaginatorInterface $paginator)
    {

        $Credit = $this->getDoctrine()->getRepository(Credit::class)->findAll();
        $Credit = $paginator->paginate($Credit,
            $request->query->getInt('page', 1),
            8

        );
        return $this->render('credit/index.html.twig', ['Credit' => $Credit]);

    }

    /**
     * @Route("/decision/{id}", name="decision", methods={"POST", "GET"})
     */
    public function makeDecision(Request $request, int $id, MailerInterface $mailer): Response
    {
        // Fetch the Credit entity by ID
        $credit = $this->getDoctrine()->getRepository(Credit::class)->find($id);

        // Check if the Credit entity exists
        if (!$credit) {
            throw $this->createNotFoundException('Credit with id ' . $id . ' does not exist');
        }

        // Get the relevant data from the Credit entity
        $statusCompte = $credit->getStatuscompte();
        $solde = $credit->getSolde();
        $montantDemande = $credit->getMontant();
        $salaire = $credit->getSalaire();
        $amor = $credit->getAMOR();

        // Prepare the data to send to the Python microservice
        $data = [
            'amor' => $amor,
            'salaire' => $salaire,
            'statuscompte' => $statusCompte,
            'solde' => $solde,
            'montant' => $montantDemande
        ];

        try {
            // Send an HTTP POST request to the Python microservice
            $client = HttpClient::create();
            $response = $client->request('POST', 'http://localhost:5000/credit-decision', [
                'json' => $data,
            ]);

            // Decode the response JSON
            $responseData = $response->toArray();

            // Extract the decision variable from the response
            $decision = $responseData['decision'];

            // Update the decision field in the Credit entity
            $credit->setDecision($decision);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Retrieve the associated user's email using idUser
            $idUser = $credit->getIdUser();
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->find($idUser);

            if (!$user) {
                // Handle the case where the user is not found
                // Log a warning or take appropriate action
            } else {
                // Proceed with sending the email
                $userEmail = $user->getEmail();
                $emailSubject = 'Decision concernant votre demande de crédit';
                $emailBody = ($decision === 'oui') ? 'Votre demande de crédit a été acceptée.' : 'Votre demande de crédit a été refusée.';
                $email = (new Email())
                    ->from('ahmed.bahrouni@esprit.tn')
                    ->to($userEmail)
                    ->subject($emailSubject)
                    ->html($emailBody);

                $mailer->send($email);
            }

            // Display the decision to the user
            return $this->render('credit/decision.html.twig', [
                'decision' => $decision,
            ]);
        } catch (ClientExceptionInterface $exception) {
            // Handle any exceptions, such as connection errors
            return $this->render('credit/error.html.twig', [
                'error' => 'An error occurred while communicating with the Python microservice',
            ]);
        }
    }

}