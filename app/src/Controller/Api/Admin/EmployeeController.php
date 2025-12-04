<?php

namespace App\Controller\Api\Admin;

use App\Controller\BaseController;
use App\Documentation\Attribute\ForbiddenResponse;
use App\Documentation\Attribute\UnauthorizedResponse;
use App\Documentation\Attribute\ValidationErrorResponse;
use App\DTO\Api\Admin\Employee\StoreEmployeeDTO;
use App\DTO\Api\Admin\Employee\UpdateEmployeeDTO;
use App\Entity\Employee;
use App\Enum\Filter\EmployeeFilters;
use App\Enum\Search\EmployeeSearch;
use App\Model\Employee\EmployeeListResponse;
use App\Model\Employee\EmployeeResponse;
use App\Request\Employee\StoreEmployeeRequest;
use App\Request\Employee\UpdateEmployeeRequest;
use App\Service\Api\ApiEmployeeService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[IsGranted('ROLE_ADMIN')]
#[Route('api/admin/employees')]
#[OA\Tag(name: 'Admin Employees')]
#[ForbiddenResponse]
#[UnauthorizedResponse]
class EmployeeController extends BaseController
{
    public function __construct(
        private readonly ApiEmployeeService $apiEmployeeService
    ) {
    }

    #[Route(name: 'api_admin_employees_index', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns paginated list of employees. Supports filtering by various fields, sorting and search.',
        summary: 'Get paginated employees with filtering, sorting and search',
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1, minimum: 1)
            ),
            new OA\Parameter(
                name: 'search',
                description: 'Search term for employee name or email',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'filters[deletedAt]',
                description: 'Filter by deleted status',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'filters[shop]',
                description: 'Filter by shop ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'filters[isDismissed]',
                description: 'Filter by position',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'sorts[id]',
                description: 'Sort by id (asc/desc)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Employees list retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: EmployeeListResponse::class),
            type: 'object'
        )
    )]
    public function index(Request $request): JsonResponse
    {
        $transformedFilters = $this->transformedFilters(
            filters: $request->query->all()['filters'] ?? [],
            filtersEnumClass: EmployeeFilters::class
        );

        $transformedSearch = $this->transformedSearch(
            searchEnumClass: EmployeeSearch::class,
            search: $request->query->getString('search') ?? ''
        );

        return $this->json(
            $this->apiEmployeeService->getList(
                page: $request->query->getInt('page', 1),
                filters: $transformedFilters,
                sorts: $request->query->all()['sorts'] ?? [],
                search: $transformedSearch
            )
        );
    }

    #[Route(name: 'api_admin_employees_store', methods: ['POST'])]
    #[OA\Post(
        description: 'Adding a new employee',
        summary: 'Adding a new employee'
    )]
    #[OA\RequestBody(
        description: 'Employee data to store',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'John'),
                new OA\Property(property: 'surname', type: 'string', example: 'Doe'),
                new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com'),
                new OA\Property(property: 'phone', type: 'string', example: '+1234567890'),
                new OA\Property(property: 'position', type: 'string', example: 'Manager'),
                new OA\Property(property: 'shopId', type: 'integer', example: 1)
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Employee was stored successfully',
        content: new OA\JsonContent(
            ref: new Model(type: EmployeeResponse::class),
            type: 'object'
        )
    )]
    #[ValidationErrorResponse(field: 'email')]
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        try {
            $employee = $this->apiEmployeeService->store(new StoreEmployeeDTO(...$request->toArray()));

            return $this->json(
                $employee,
                201
            );
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_employees_show', methods: ['GET'])]
    #[OA\Get(
        description: 'Returns employee details',
        summary: 'Get employee details'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Employee ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Employee details retrieved successfully',
        content: new OA\JsonContent(
            ref: new Model(type: EmployeeResponse::class),
            type: 'object'
        )
    )]
    public function show(Employee $employee): JsonResponse
    {
        return $this->json(
            $this->apiEmployeeService->show($employee)
        );
    }

    #[Route('/{id}', name: 'api_admin_employees_update', methods: ['PATCH'])]
    #[OA\Patch(
        description: 'Update employee information. Only provided fields will be updated.',
        summary: 'Update employee'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Employee ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\RequestBody(
        description: 'Employee data to update',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'John', nullable: true),
                new OA\Property(property: 'surname', type: 'string', example: 'Doe', nullable: true),
                new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com', nullable: true),
                new OA\Property(property: 'phone', type: 'string', example: '+1234567890', nullable: true),
                new OA\Property(property: 'position', type: 'string', example: 'Manager', nullable: true),
                new OA\Property(property: 'shopId', type: 'integer', example: 1, nullable: true),
                new OA\Property(property: 'isDismissed', type: 'boolean', example: true, nullable: true),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Employee updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'employee',
                    ref: new Model(type: EmployeeResponse::class)
                )
            ]
        )
    )]
    #[ValidationErrorResponse(field: 'email')]
    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        try {
            $updatedEmployee = $this->apiEmployeeService->update(new UpdateEmployeeDTO(...$request->toArray()), $employee);

            return $this->json([
                'employee' => $updatedEmployee
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/{id}', name: 'api_admin_employees_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Soft delete employee by ID.',
        summary: 'Delete employee'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Employee ID to delete',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: 'Employee deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Employee deleted successfully')
            ]
        )
    )]
    public function delete(Employee $employee): JsonResponse
    {
        try {
            $this->apiEmployeeService->delete($employee);

            return $this->json([
                'message' => 'Employee deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}