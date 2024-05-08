<?php

namespace App\Controller;
use App\Entity\Organisateur;
use App\Form\OrganisateurType;
use App\Repository\OrganisateurRepository;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class OrganisateurController extends AbstractController
{
    /**
     * @Route("/organisateur", name="organisateur")
     */
    public function index(): Response
    {
        return $this->render('organisateur/index.html.twig', [
            'controller_name' => 'OrganisateurController',
        ]);
    }

    /**
     * @Route("/organisateur/afficher", name="consulter_organisateur")
     */
    public function afficherOrg(OrganisateurRepository $repository, PaginatorInterface $paginator,Request $request)
    {
        $donnees = $repository->findAll();
        $organisateurs = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page,
        # sliding pagination controls template

        );
        return $this->render('organisateur/consulterOrganisateur.html.twig', [
            'organisateurs' => $organisateurs
        ]);;
    }
    /**
     * @Route("/organisateur/afficherFront", name="organisateurFront")
     */
    public function afficherOrgFront(OrganisateurRepository $repository)
    {
        $organisateurs = $repository->findAll();
        return $this->render('organisateur/OrganisateurFront.html.twig', [
            'organisateurs' => $organisateurs
        ]);;
    }

    /**
     * @Route("/organisateur/delete/{id}", name="delete_organisateur")
     */
    public function supprimerOrg($id, OrganisateurRepository $repository)
    {
        $organisateur = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($organisateur);
        $em->flush();
        return $this->redirectToRoute('consulter_organisateur');
    }

    /**
     * @Route("/organisateur/ajouterEv", name="ajouter_organisateur")
     */
    public function ajouterOrg(Request $request, MailerInterface $mailer)
    {
        $organisateur = new Organisateur();
        $form = $this->createForm(OrganisateurType::class, $organisateur);
        $form->add('Save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organisateur);
            $em->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('ahmed.bahrouni@esprit.tn', 'Bna Service'))
                ->to($form->get('emailOrganisateur')->getData())
                ->subject('Félicitatins Vous êtes séléctionné Organisateur ')
                ->htmlTemplate('organisateur/mail.html.twig')
                ->context([
                    'name' => $form->get('nom')->getData()
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('consulter_organisateur');
        }

        return $this->render('organisateur/ajouterOrganisateur.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/organisateur/edit/{id}", name="edit_organisateur")
     */
    public function edit(Request $request, OrganisateurRepository $repository,$id)
    {
        $organisateur=$repository->find($id);

        $form = $this->createForm(OrganisateurType::class, $organisateur);
        $form->add('Save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            return $this->redirectToRoute('consulter_organisateur');

        }
        return $this->render('organisateur/ajouterOrganisateur.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

