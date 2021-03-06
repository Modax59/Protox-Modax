<?php

namespace App\Controller\user;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

class ResetPasswordController
{

    /**
     * Undocumented variable
     *
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository)
    {
        $this->manager = $manager;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request)
    {
        $token = $request->attributes->get('token');
        $user = $this->userRepository->findOneBy(['resetPassToken' => $token]);
        if($user instanceof User){
            return $message = ["valide"];
        }else{
            return $message =["notValide"];
        }
    }
}
