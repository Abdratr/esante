<?php

namespace App\Controller;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route('/admin/register', name: 'admin_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        if ($request->isMethod('POST')) {

            $email = trim($request->request->get('email'));
            $telephone = trim($request->request->get('telephone'));
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('admin_register');
            }

            if ($em->getRepository(Admin::class)->findOneBy(['email' => $email])) {
                $this->addFlash('error', 'Cet email existe déjà.');
                return $this->redirectToRoute('admin_register');
            }

            if ($em->getRepository(Admin::class)->findOneBy(['telephone' => $telephone])) {
                $this->addFlash('error', 'Ce numéro existe déjà.');
                return $this->redirectToRoute('admin_register');
            }

            $admin = new Admin();

            $admin->setNomComplet($request->request->get('nom_complet'));
            $admin->setEmail($email);
            $admin->setTelephone($telephone);
            $admin->setLieuNaissance($request->request->get('lieu_naissance'));
            $admin->setDomicile($request->request->get('domicile'));

            if ($request->request->get('date_naissance')) {
                $admin->setDateNaissance(
                    new \DateTime($request->request->get('date_naissance'))
                );
            }

            $admin->setPassword(
                $passwordHasher->hashPassword($admin, $password)
            );

            $photoFile = $request->files->get('photo');

            if ($photoFile) {

                $newFilename = uniqid('', true).'.'.$photoFile->guessExtension();

                $photoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads',
                    $newFilename
                );

                $admin->setPhoto('uploads/'.$newFilename);
            }

            $admin->setCreatedAt(new \DateTimeImmutable());

            $em->persist($admin);
            $em->flush();

            return $this->redirectToRoute('admin_success', [
                'id' => $admin->getId()
            ]);
        }

        return $this->render('admin/register.html.twig');
    }

    #[Route('/admin/success/{id}', name: 'admin_success')]
    public function success(Admin $admin): Response
    {
        return $this->render('admin/inscription_reussi.html.twig', [
            'admin' => $admin
        ]);
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/forgot-password', name: 'admin_forgot_password')]
    public function forgot(): Response
    {
        return $this->render('admin/forgot_password.html.twig');
    }

    #[Route('/admin/logout', name: 'admin_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            'Cette méthode est interceptée par le firewall logout.'
        );
    }
}