<?php

namespace App\Controller;

use App\Entity\User;
use App\Component\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class  UserCreateController extends AbstractController{

    public function  __construct(
        private  EntityManagerInterface $entityManager,
        private  UserFactory $userFactory,
        ){}

    public function __invoke(User $data): User{
        
        $user = $this->userFactory->create(
            $data->getUsername(),
            $data->getEmail(),
            $data->getPassword(),
            $data->getRoles());
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return  $user;
    }
}