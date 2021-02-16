<?php

namespace App\Controller\Rest\v1;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(condition="request.attributes.get('version') == 'v1'")
 */
final class IndexController extends AbstractFOSRestController
{
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'code' => JsonResponse::HTTP_OK,
            'message' => '',
        ]);
    }
}
