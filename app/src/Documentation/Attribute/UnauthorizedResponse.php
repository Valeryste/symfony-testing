<?php

namespace App\Documentation\Attribute;

use Attribute;
use OpenApi\Attributes as OA;
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class UnauthorizedResponse extends OA\Response
{
    public function __construct()
    {
        parent::__construct(
            response: 401,
            description: 'Unauthorized - JWT token missing or invalid',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'error', type: 'string', example: 'JWT Token not found')
                ]
            )
        );
    }
}