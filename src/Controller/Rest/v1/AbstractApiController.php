<?php

namespace App\Controller\Rest\v1;

use App\Domain\User\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route(condition="request.attributes.get('version') == 'v1'")
 */
abstract class AbstractApiController extends AbstractFOSRestController
{
    /**
     * @return UserInterface|User
     */
    public function getUser()
    {
        return parent::getUser();
    }
}
