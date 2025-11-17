<?php

namespace App\Documentation\Attribute;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class ValidationErrorResponse extends OA\Response
{
    public function __construct()
    {
        parent::__construct(
            response: 422,
            description: 'Validation failed',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'validation failed'),
                    new OA\Property(
                        property: 'errors',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'property', type: 'string', example: 'username'),
                                new OA\Property(property: 'value', type: 'string', example: ''),
                                new OA\Property(property: 'message', type: 'string', example: 'field username is required')
                            ],
                            type: 'object'
                        )
                    )
                ]
            )
        );
    }
}