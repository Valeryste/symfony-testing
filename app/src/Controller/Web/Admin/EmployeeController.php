<?php

namespace App\Controller\Web\Admin;

use App\Controller\BaseController;
use App\Entity\Employee;
use App\Enum\Filter\EmployeeFilters;
use App\Enum\Search\EmployeeSearch;
use App\Enum\Sort\EmployeeSorts;
use App\Form\Admin\Employee\CreateEmployeeFormType;
use App\Form\Admin\Employee\UpdateEmployeeFormType;
use App\Service\EmployeeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/employees')]
#[IsGranted('ROLE_ADMIN')]
class EmployeeController extends BaseController
{
    private const PATH_TO_TEMPLATES = 'admin/employee/';

    public function __construct(
        private readonly EmployeeService $employeeService,
    ) {
    }


    #[Route(name: 'admin_employees_index')]
    public function index(Request $request): Response
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: EmployeeFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: EmployeeSearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->render(self:: PATH_TO_TEMPLATES . 'index.html.twig', [
            'employees' => $this->employeeService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            ),
            'filters' => EmployeeFilters::getFilterCases(),
            'sorts' => EmployeeSorts::getSortCases(),
            'shops' => $this->employeeService->getShopsHavingEmployee()
        ]);
    }

    #[Route('/create', name: 'admin_employees_create')]
    public function create(): Response
    {
        return $this->render(self:: PATH_TO_TEMPLATES . 'create.html.twig', [
            'createForm' => $this->createForm(CreateEmployeeFormType::class),
        ]);
    }


    #[Route('/store', name: 'admin_employees_store')]
    public function store(Request $request): Response
    {
        $form = $this->createForm(CreateEmployeeFormType::class, $employee = new Employee());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->employeeService->store($employee);

            $this->addFlash('success', 'Product was successfully created');

            return $this->redirectToRoute('admin_employees_index');
        }

        $this->addFlash('error', 'Validation or create error');

        return $this->render(self::PATH_TO_TEMPLATES . 'create.html.twig', [
            'createForm' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_employees_edit')]
    public function edit(Employee $employee): Response
    {
        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'employee' => $employee,
            'updateForm' => $this->createForm(UpdateEmployeeFormType::class, $employee)
        ]);
    }

    #[Route('/{id}', name: 'admin_employees_update')]
    public function update(Request $request, Employee $employee): Response
    {
        $form = $this->createForm(UpdateEmployeeFormType::class, $employee);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->employeeService->update($employee);

            $this->addFlash('success', 'Product was successfully updated');

            return $this->redirectToRoute('admin_employees_edit', [
                'id' => $employee->getId()
            ]);
        }

        $this->addFlash('error', 'Validation or update error');

        return $this->render(self::PATH_TO_TEMPLATES . 'edit.html.twig', [
            'product' => $employee,
            'updateForm' => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_employees_delete')]
    public function delete(Employee $employee): Response
    {
        $this->employeeService->delete($employee);

        return $this->redirectToRoute('admin_employees_index');
    }

}