<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


use App\Entity\Reclamation;
use App\Entity\User;
use App\Form\ChangementType;
use App\Form\ChangePasswordType;
use App\Form\LoginType;
use App\Form\OublierType;
use App\Form\ResetpassType;
use App\Form\UserType;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function PHPUnit\Framework\matches;

class GestionuserController extends AbstractController
{
    const  ATTRIBUTES_TO_SERIALIZE = ['id_user','mdp','nom','prenom','email','role','numtel_user','adresse_user','connected'	];
    private $requestStack;


    #[Route("/gestionuser", name:"gestionuser")]

    public function index(): Response
    {
        return $this->render('gestionuser/index.html.twig', [
            'controller_name' => 'GestionuserController',
        ]);
    }
        #[Route("/welcome_front", name:"welcome_front")]

    public function welcomefront(): Response
    {
        return $this->render('base-front1.html.twig',[
            'controller_name' => 'GestionuserController',
        ]);;
    }

     #[Route("/", name:"normal")]

    public function normal(): Response
    {
        return $this->redirectToRoute('home_front');
    }

    /**
     * @param UserRepository $User
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/afficheU",name="user_list")
     */
    public function afficher(UserRepository $repository, Request $request, PaginatorInterface $paginator)
    {

        $User = $this->getDoctrine()->getRepository(User::class)->findAll();
        $User = $paginator->paginate($User,
            $request->query->getInt('page', 1),
            4

        );
        return $this->render('gestionuser/index.html.twig', ['User' => $User]);

    }

    /**
     * @Route("/supprimer/{id}", name="suppuser")
     */
    public function supprimer(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('user_list');
    }

    #[Route('/user/edit/{id}', name: 'edit_USER')]
    public function edit(Request $request, UserRepository $repository,$id): Response
    {
        $user=$repository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->add('Save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('user_list');
        }

        return $this->render('gestionuser/modifieruser.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/ajouteruser", name="ajouteruser")
     */


    public function ajouter(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('Save', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $passwordCrypte = md5($user->getMdp());
            $rib = "04";

            $agence = $user->getAgence();

            $asciiCode = strtoupper(substr($agence, 0, 2));


            $rib .= ord($asciiCode[0]) . ord($asciiCode[1]);

            $cin = $user->getCin();
            $rib .= substr($cin, -4);

            // Générer 10 chiffres aléatoires pour compléter le RIB
            $randomDigits = mt_rand(1000000000, 9999999999);
            $rib .= $randomDigits;

            // Assurer que le RIB ne dépasse pas 20 chiffres
            $rib = substr($rib, 0, 20);

            // Enregistrer le RIB dans l'utilisateur
            $user->setRib($rib);
            $user->setMdp($passwordCrypte);
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'vous compte a été creé avec succées veuillez consultez votre compte!'
            );
            return $this->redirectToRoute('loginnn');
        }
        return $this->render('gestionuser/login-register.html.twig', [
            'formregister' => $form->createView()
        ]);;
    }

    /**
     * @Route("/loginnn", name="loginnn")
     */
    public function login(Request $request, SessionInterface $session, UserPasswordEncoderInterface $encoder): Response
    {

        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $useronline = new User();
        $connexion = $this->createForm(LoginType::class, $useronline);
        $connexion->handleRequest($request);
        if ($connexion->isSubmitted() && $connexion->isValid()) {
            $verifuser = $userRepository->findOneBy(array('email' => $useronline->getEmail()));


            if ($verifuser ==null || $verifuser->getMdp() != md5($useronline->getMdp())) {
                return $this->render('gestionuser/message.html.twig', ['message' => 'Email ou mot de passe incorrect'
                ]);
            } else {
                if ($verifuser->getRole() == "CLIENT") {
                    $session->set('user', $verifuser);
                    return $this->redirectToRoute('home1_front');
                } elseif ($verifuser->getRole() == "ADMIN") {

                    $session->set('user', $verifuser);
                    return $this->redirectToRoute('home');
                }

            }

        }
        return $this->render('gestionuser/register.html.twig', [
            'connexion' => $connexion->createView(),
        ]);

    }

    /**
     * @Route("/deconnexion", name="deconnecter", methods={"GET"})
     */
    public function logout(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('loginnn');
    }

    /**
     * @Route("/listuser", name="listuser", methods={"GET"})
     */
    public function listuser(UserRepository $UserRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfoptions = new Options();
        $pdfoptions->set('defaultFont', 'Arial');
        $pdfoptions->set('tempDir', '.\www\DaryGym\public\uploads\images');


        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfoptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('gestionuser/list.html.twig', [
            'User' => $UserRepository->findAll(),
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $output = $dompdf->output();
        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="user_list.pdf"'
        ]);
    }



    /**
     * @Route("/TriernomASC/back", name="trie1",methods={"GET"})
     */
    public function Trierprenom1(Request $request, UserRepository $UserRepository): Response
    {
        $UserRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $UserRepository->trierprenom1();

        return $this->render('gestionuser/index.html.twig', [
            'User' => $user,
        ]);
    }

    /**
     * @Route("/TriernomASC/back", name="trie2",methods={"GET"})
     */
    public function Trierprenom2(Request $request, UserRepository $UserRepository): Response
    {
        $UserRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $UserRepository->trierprenom2();

        return $this->render('gestionuser/index.html.twig', [
            'User' => $user,
        ]);
    }

    /**
     * @Route("/TriernomASC/back", name="trie3",methods={"GET"})
     */
    public function Triernom1(Request $request, UserRepository $UserRepository): Response
    {
        $UserRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $UserRepository->triernom1();

        return $this->render('gestionuser/index.html.twig', [
            'User' => $user,
        ]);
    }

    /**
     * @Route("/TriernomDESC/back", name="trie4",methods={"GET"})
     */
    public function Triernom2(Request $request, UserRepository $UserRepository): Response
    {
        $UserRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $UserRepository->triernom2();

        return $this->render('gestionuser/index.html.twig', [
            'User' => $user,
        ]);
    }

    /**
     * @Route("/TrieremailASC/back", name="trie5",methods={"GET"})
     */
    public function Trieremail1(Request $request, UserRepository $UserRepository): Response
    {
        $UserRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $UserRepository->trieremail1();

        return $this->render('gestionuser/index.html.twig', [
            'User' => $user,
        ]);
    }

    /**
     * @Route("/TrieremailDESC/back", name="trie6",methods={"GET"})
     */
    public function Trieremail2(Request $request, UserRepository $UserRepository): Response
    {
        $UserRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $UserRepository->trieremail2();

        return $this->render('gestionuser/index.html.twig', [
            'User' => $user,
        ]);
    }

    /**
     * @Route("/search/back", name="offreajax", methods={"GET"})
     */

    public function searchoffreajax(Request $request, UserRepository $UserRepository): Response
    {
        $UserRepository = $this->getDoctrine()->getRepository(User::class);
        $requestString = $request->get('searchValue');
        $user = $UserRepository->finduserbynom($requestString);

        return $this->render('gestionuser/index.html.twig', [
            "User" => $user
        ]);
    }

    public function getRealEntities($entities)
    {

        foreach ($entities as $entity) {
            $realEntities[$entity->getId()] = $entity->getNom();
        }

        return $realEntities;

    }



    /**
     * @Route("/ajouter/utilisateur" , name="utilisateur_ajouter" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function ajouteruser(Request $request,SerializerInterface $serializer)
    {

        $user = new User();
        $nom=$request->query->get('nom');
        $prenom=$request->query->get('prenom');
        $mdp=$request->query->get('mdp');

        $email=$request->query->get('email');
        $numtel=$request->query->get('numtel_user');
        $adresseuser=$request->query->get('adresse_user');


        $em=$this->getDoctrine()->getManager();
        $user->setRole("CLIENT");
        $user->setPrenom($prenom);
        $user->setNom($nom);

        $user->setEmail($email);
        $user->setadresse_user($adresseuser);
        $user->setnumtel_user($numtel);

        $user->setMdp($mdp);

        $em->persist($user);
        $em->flush();
        $serializer=new Serializer([new ObjectNormalizer()]);
        $formatted=$serializer->normalize($user);
        return new JsonResponse($formatted);
    }

    /**
     * @Route("/user/list")
     */
    public function getList(UserRepository $user, SerializerInterface $serializer): Response
    {

        $User = $user->findAll();
        $json = $serializer->serialize($User, 'json', ['groups' => ['User']]);


        return $this->json(['User' => $User], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);
    }

    /**
     * @Route("/delete/utilisateur/json", name="supprimer_utilisateur")
     */
    public function supprimerUtilisateur(Request $request, UserRepository $repo): Response
    {

        $id =$request->get("id_user");
        $em=$this->getDoctrine()->getManager();

        $d=$repo->find($id);

        if($d != null){
            $em->remove($d);
            $em->flush();
            $serializer=new Serializer([new ObjectNormalizer()]);
            $formatted=$serializer->normalize("utilisateur a eté supprimeé");
            return new JsonResponse($formatted);
        }

        return  new JsonResponse("Id Invalide");
    }






    /**
     * @Route("/modifier/user" , name="user_modifier" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function modifier(Request $request, SerializerInterface $serializer, UserRepository $repo)
    {

        $user = new User();
        $id = $request->query->get('id_user');
        $user = $repo->find($id);
        $nom = $request->query->get('nom');
        $prenom = $request->query->get('prenom');
        $email = $request->query->get('email');
        $mdp = $request->query->get('mdp');
        $numtel=$request->query->get('numtel_user');
        $adresseuser=$request->query->get('adresse_user');

        $em = $this->getDoctrine()->getManager();

        $user->setPrenom($prenom);
        $user->setNom($nom);

        $user->setEmail($email);
        $user->setadresse_user($adresseuser);
        $user->setnumtel_user($numtel);

        $user->setMdp($mdp);

        $em->persist($user);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($user);
        return new JsonResponse($formatted);
    }


    /**
     * @Route("/user/signin", name="login_")
     */
    public function LoginMobile(Request $request,UserRepository $repo) {


        $verifuser = $repo ->findOneBy(['email'=>$request->get("email")]);

        $mdp=$request->get("Password");

        if($verifuser)
        {

            if($verifuser ==null ||  $mdp != ($verifuser->getMdp()))
            {
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($verifuser);
                return new JsonResponse($formatted);
            }
            else
            {
                return new Response("password incorect");
            }
        }
    }


}