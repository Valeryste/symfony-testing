<?php

namespace App\Documentation\Attribute;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class ForbiddenResponse extends OA\Response
{
    public function __construct()
    {
        parent::__construct(
            response: 403,
            description: 'Forbidden - insufficient permissions',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'error', type: 'string', example: 'Access denied')
                ]
            )
        );
    }
}