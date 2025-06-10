<?php

namespace App\Controller;
use TCPDF;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Enum\UserRole;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\OpenAIService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class UserController extends AbstractController
{
    #[Route(path: '/userback', name: 'app_user_index', methods: ['GET'])]
    public function index(SessionInterface $session, UserRepository $userRepository): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$session->has('user_id')) {
            // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
            return $this->redirectToRoute('login');
        }

        // Ajouter des en-têtes pour éviter le cache dans le navigateur
        $response = $this->render('backOfficeUser/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);

        // Définir les en-têtes HTTP pour interdire la mise en cache côté client
        $response->setCache([
            'no_store' => true,
            'no_cache' => true,
            'must_revalidate' => true,
            'max_age' => 0
        ]);

        return $response;
    }

    #[Route(path: 'userback/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user, ['include_admin' => true]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérification de l'unicité de l'email
        $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['AdresseMail' => $user->getAdresseMail()]);
        if ($existingUserByEmail) {
            $this->addFlash('error', 'Cet email est déjà utilisé.');
            return $this->redirectToRoute('app_user_new');
        }

        // Vérification de l'unicité du mot de passe
        // Cette vérification est rare, mais ajoutée ici pour votre besoin.
        $existingUserByPassword = $entityManager->getRepository(User::class)->findOneBy(['Password' => $user->getPassword()]);
        if ($existingUserByPassword) {
            $this->addFlash('error', 'Ce mot de passe est déjà utilisé.');
            return $this->redirectToRoute('app_user_new');
        }

        // Hacher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        // Sauvegarder l'utilisateur
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('backOfficeUser/new.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
    }

    #[Route('userback/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('backOfficeUser/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('userback/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Créer le formulaire pour l'utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification de l'unicité de l'email, sauf si c'est le même utilisateur
            $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['AdresseMail' => $user->getAdresseMail()]);
            if ($existingUserByEmail && $existingUserByEmail->getId() !== $user->getId()) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('backOfficeUser/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }

            // Vérification de l'unicité du mot de passe, sauf si c'est le même utilisateur
            $existingUserByPassword = $entityManager->getRepository(User::class)->findOneBy(['Password' => $user->getPassword()]);
            if ($existingUserByPassword && $existingUserByPassword->getId() !== $user->getId()) {
                $this->addFlash('error', 'Ce mot de passe est déjà utilisé.');
                return $this->render('backOfficeUser/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }

            // Vérification du mot de passe : si un mot de passe est fourni, le hacher
            $plainPassword = $form->get('Password')->getData();
            if ($plainPassword) {
                // Hacher le mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            // Si tout est validé, on met à jour l'utilisateur
            $entityManager->flush();

            // Redirection après l'enregistrement
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu du formulaire d'édition
        return $this->render('backOfficeUser/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('userback/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: 'userfront/signup', name: 'app_user_new_front', methods: ['GET', 'POST'])]
    public function signup(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user, ['include_admin' => false]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérification de l'unicité de l'email
        $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['AdresseMail' => $user->getAdresseMail()]);
        if ($existingUserByEmail) {
            $this->addFlash('error', 'Cet email est déjà utilisé.');
            return $this->redirectToRoute('app_user_new');
        }

        // Vérification de l'unicité du mot de passe
        // Cette vérification est rare, mais ajoutée ici pour votre besoin.
        $existingUserByPassword = $entityManager->getRepository(User::class)->findOneBy(['Password' => $user->getPassword()]);
        if ($existingUserByPassword) {
            $this->addFlash('error', 'Ce mot de passe est déjà utilisé.');
            return $this->redirectToRoute('app_user_new');
        }

       

        // Sauvegarder l'utilisateur
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->render('internaute.html.twig');
    }

    return $this->render('frontOfficeUser/signup.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
    }
    
    #[Route('userfront/{id}', name: 'app_user_showmine', methods: ['GET'])]
    public function showmine($id, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est connecté en vérifiant la session
        $userId = $session->get('user_id');
        if (!$userId || $userId != $id) {
            // Si l'utilisateur n'est pas connecté ou l'ID ne correspond pas, rediriger vers la page de connexion
            return $this->redirectToRoute('login');
        }

        // Récupère l'utilisateur connecté depuis la base de données
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('frontOfficeUser/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/userfront/delete/{id}', name: 'app_user_deletemine', methods: ['POST'])]
    public function deletemine($id, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Vérifie si l'utilisateur est connecté et si l'ID de l'utilisateur dans la session correspond à celui passé dans l'URL
        $userId = $session->get('user_id');
        if (!$userId || $userId != $id) {
            // Si l'ID ne correspond pas ou si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
            return $this->redirectToRoute('login');
        }

        // Récupérer l'utilisateur à supprimer
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Supprimer l'utilisateur
        $entityManager->remove($user);
        $entityManager->flush();

        // Déconnecter l'utilisateur en supprimant ses données de session
        $session->clear();

        // Rediriger vers la page d'accueil ou la page de connexion après la suppression
        return $this->redirectToRoute('home_page');
    }

    #[Route('/userfront/edit/{id}', name: 'app_user_editmine', methods: ['GET', 'POST'])]
    public function editUser($id, Request $request, EntityManagerInterface $entityManager, SessionInterface $session, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Vérifie si l'utilisateur est connecté et si l'ID de l'utilisateur dans la session correspond à celui passé dans l'URL
        $userId = $session->get('user_id');
        if (!$userId || $userId != $id) {
            return $this->redirectToRoute('login');
        }

        // Récupérer l'utilisateur à modifier
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Créer le formulaire
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification du mot de passe : si un mot de passe est fourni, le hacher
            $plainPassword = $form->get('Password')->getData();
            if ($plainPassword) {
                // Hacher le nouveau mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            // Vérification de l'unicité de l'email (si modifié)
            $existingUserByEmail = $entityManager->getRepository(User::class)->findOneBy(['AdresseMail' => $user->getAdresseMail()]);
            if ($existingUserByEmail && $existingUserByEmail->getId() !== $user->getId()) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('frontOfficeUser/edit.html.twig', [
                    'form' => $form->createView(),
                    'userRole' => $user->getRole()->value, // Passer la valeur du rôle sous forme de chaîne
                    'user' => $user,
                ]);
            }

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            // Message de succès et redirection vers la page de profil
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
            return $this->redirectToRoute('app_user_showmine', ['id' => $user->getId()]);
        }

        return $this->render('frontOfficeUser/edit.html.twig', [
            'form' => $form->createView(),
            'userRole' => $user->getRole()->value, // Passer la valeur du rôle sous forme de chaîne
            'user' => $user,
        ]);
    }

   #[Route('/login', name: 'login')]
public function login(Request $request, SessionInterface $session, EntityManagerInterface $entityManager)
{
    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $recaptchaResponse = $request->request->get('g-recaptcha-response');

        // Vérification reCAPTCHA
        if (!$recaptchaResponse) {
            $this->addFlash('error', 'Veuillez valider le reCAPTCHA.');
            return $this->redirectToRoute('login');
        }

        $client = HttpClient::create();
        $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => '6LcOWOgqAAAAAD7pwDPD8XMCWdR7hshbUgDhr_Mv', // Clé secrète
                'response' => $recaptchaResponse
            ]
        ]);

        $data = $response->toArray();

        if (!$data['success']) {
            $this->addFlash('error', 'Échec de la validation reCAPTCHA.');
            return $this->redirectToRoute('login');
        }

        // Vérification de l'utilisateur sans hachage
        $user = $entityManager->getRepository(User::class)->findOneBy(['AdresseMail' => $email]);

        if ($user && $user->getPassword() === $password) {
            $session->set('user_id', $user->getId());
            $session->set('user_name', $user->getNom());
            $session->set('user_role', $user->getRole()->value);

            if ($user->getRole() === UserRole::ADMIN) {
                return $this->redirectToRoute('app_user_index');
            } else {
                return $this->render('base_front_etude.html.twig');
            }
        } else {
            $this->addFlash('error', 'Email ou mot de passe incorrect.');
        }
    }

    return $this->render('frontOfficeUser/login.html.twig');
}

    
    #[Route('/logout', name: 'logout')]
    public function logout(SessionInterface $session, Request $request)
    {
        // Effacer la session
        $session->clear();

        // Pour éviter le retour arrière, on effectue une redirection vers la page d'accueil
        return $this->render('internaute.html.twig');
    }

    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, UrlGeneratorInterface $urlGenerator): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $entityManager->getRepository(User::class)->findOneBy(['AdresseMail' => $email]);

            if ($user) {
                // Générer un token unique
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $entityManager->flush();

                // Générer l'URL de réinitialisation
                $resetUrl = $urlGenerator->generate('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // Envoyer l'email avec le lien
                $email = (new Email())
                    ->from('ayari.ahmed.1920@gmail.com')
                    ->to($user->getAdresseMail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html("<p>Bonjour,</p><p>Pour réinitialiser votre mot de passe, cliquez sur le lien suivant :</p><p><a href='{$resetUrl}'>Réinitialiser mon mot de passe</a></p>");

                $mailer->send($email);

                $this->addFlash('success', 'Un email a été envoyé pour réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('error', 'Adresse e-mail non trouvée.');
            }
        }

        return $this->render('frontOfficeUser/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'reset_password')]
    public function resetPassword(string $token, Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('login');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            } else {
                // Mettre à jour le mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
                $user->setResetToken(null); // Supprimer le token après utilisation
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe réinitialisé avec succès. Connectez-vous avec votre nouveau mot de passe.');
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('frontOfficeUser/reset_password.html.twig', ['token' => $token]);
    }

    #[Route(path: '/statistics', name: 'app_user_statistics', methods: ['GET'])]
    public function statistics(UserRepository $userRepository): Response
    {
        // Compter le nombre d'admins, agriculteurs et clients
        $adminCount = $userRepository->count(['Role' => UserRole::ADMIN]);
        $agriculteurCount = $userRepository->count(['Role' => UserRole::AGRICULTEUR]);
        $clientCount = $userRepository->count(['Role' => UserRole::CLIENT]);

        // Passer les données à la vue pour les afficher
        return $this->render('backOfficeUser/statistics.html.twig', [
            'adminCount' => $adminCount,
            'agriculteurCount' => $agriculteurCount,
            'clientCount' => $clientCount,
        ]);
    }

    #[Route(path: '/search', name: 'user_search', methods: ['GET', 'POST'])]
    public function search(Request $request, UserRepository $userRepository)
    {
        // Récupérer le terme de recherche depuis la requête
        $searchTerm = $request->query->get('searchTerm', '');

        // Récupérer les utilisateurs en fonction du terme de recherche
        $users = $userRepository->findBySearchTerm($searchTerm);

        return $this->render('backOfficeUser/search.html.twig', [
            'users' => $users,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route(path: '/pdf', name: 'app_user_export_pdf', methods: ['GET', 'POST'])]
    public function exportPdf(UserRepository $userRepository): Response
    {
        // Récupérer tous les utilisateurs
        $users = $userRepository->findAll();

        // Initialisation de TCPDF avec un format A4
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

        // Définir les marges de la page
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderData('', 0, 'List of Users', 'Generated by DigiFarm-Project', [0, 51, 102], [255, 255, 255]);

        // Ajouter une page
        $pdf->AddPage();

        // Définir la police du titre
        $pdf->SetFont('Helvetica', 'B', 16);
        
        // Titre du document
        $pdf->SetTextColor(0, 51, 102);  // Couleur bleue pour le titre
        $pdf->Cell(0, 10, 'List of Users', 0, 1, 'C');
        $pdf->Ln(5);  // Un petit espace après le titre

        // Définir l'en-tête de la table avec des couleurs de fond
        $pdf->SetFont('Helvetica', 'B', 10);  // Police plus petite pour les entêtes
        $pdf->SetFillColor(0, 51, 102);  // Couleur de fond bleue
        $pdf->SetTextColor(255, 255, 255);  // Texte en blanc

        // Ajuster les largeurs des colonnes
        $pdf->Cell(20, 10, 'Id', 1, 0, 'C', 1);  // Colonne plus étroite
        $pdf->Cell(40, 10, 'Nom', 1, 0, 'C', 1);
        $pdf->Cell(40, 10, 'Prenom', 1, 0, 'C', 1);
        $pdf->Cell(50, 10, 'AdresseMail', 1, 0, 'C', 1);  // Réduire la largeur
        $pdf->Cell(30, 10, 'Role', 1, 1, 'C', 1);

        // Réinitialiser la couleur du texte et la police pour les données
        $pdf->SetTextColor(0, 0, 0);  // Texte noir
        $pdf->SetFont('Helvetica', '', 9);  // Police plus petite pour les données

        // Ajouter les données des utilisateurs
        foreach ($users as $user) {
            $pdf->Cell(20, 10, (string)$user->getId(), 1, 0, 'C');
            $pdf->Cell(40, 10, $user->getNom(), 1, 0, 'C');
            $pdf->Cell(40, 10, $user->getPrenom(), 1, 0, 'C');
            $pdf->Cell(50, 10, $user->getAdresseMail(), 1, 0, 'C');
            $pdf->Cell(30, 10, $user->getRole()->value, 1, 1, 'C');
        }

        // Retourner le PDF avec un nom de fichier pour le télécharger
        $filename = 'users_list_' . date('Y-m-d_H-i-s') . '.pdf';
        return new Response(
            $pdf->Output($filename, 'D'),  // 'D' signifie forcer le téléchargement
            200,
            ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="' . $filename . '"']
        );
    }



}
