<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\UserFilters;
use App\Enum\UserSorts;
use App\Form\Admin\User\UpdateUserFormType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    private const PATH_TO_TEMPLATES = 'admin/user/';

    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Route('/users', name: 'admin_users_index')]
    public function index(Request $request): Response
    {
        $sorts = $request->query->all()['sorts'] ?? [];

        $filters = $request->query->all()['filters'] ?? [];

        return $this->render(self::PATH_TO_TEMPLATES . 'index.html.twig', [
                'users' => $this->userService->getList(
                    page: $request->query->getInt('page', 1),
                    filters: $filters,
                    sorts: $sorts
                ),
                'filters' => UserFilters::getFilterCases(),
                'sorts' => UserSorts::getSortCases(),
                'roles' => $this->userService->getAllRole(),
            ]
        );
    }

    #[Route('/users/{id}/edit', name: 'admin_users_edit', methods: 'GET')]
    public function edit(User $user): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'user' => $user,
            'updateForm' => $this->createForm(UpdateUserFormType::class, $user)
        ]);
    }

    #[Route('/users/{id}', name: 'admin_users_update', methods: ['POST'])]
    public function update(Request $request, User $user): Response
    {
        $form = $this->createForm(UpdateUserFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->update($user);

            $this->addFlash('success', 'Updated successful!');

            return $this->redirectToRoute('admin_users_edit', [
                'id' => $user->getId()
            ]);
        }

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'user' => $user,
            'updateForm' => $form
        ]);
    }

    #[Route('/users/{id}/delete', name: 'admin_users_delete', methods: ['POST'])]
    public function delete(User $user): Response
    {
        $this->userService->delete($user);

        return $this->redirectToRoute('admin_users_index');
    }
}