<?php

namespace App\Controller\Web\Authentication;

use App\DTO\RegisterFormDTO;
use App\Form\RegistrationFormType;
use App\Service\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly RegistrationService $registrationService
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        if ($this->getUser()) {
            $this->addFlash('info', 'You are already registered!');

            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RegistrationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = [
                'username' => $form->get('username')->getData(),
                'email' => $form->get('email')->getData(),
                'plainPassword' => $form->get('plainPassword')->getData(),
            ];

            $this->registrationService->register(new RegisterFormDTO(...$data));

            $this->addFlash('success', 'Registration successful!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('authentication/register.html.twig', [
                'registrationForm' => $form
            ]
        );
    }
}
