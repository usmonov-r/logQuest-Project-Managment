<?php

namespace App\Component;


use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use DateTimeZone;
use Symfony\Component\Clock\DatePoint;


class UserFactory{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ){}

    public function create(
        string $username, string $email,
        string $password, array $role ): User{

        $user = new User();
        $hashedPass = $this->passwordHasher->hashPassword($user, $password);
        // $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        if($role === ["1"]){
            $user->setRoles(["ROLE_USER", "ROLE_ADMIN"]);
        }else{
            $user->setRoles(["ROLE_USER"]);
        }

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($hashedPass);
       
        $user->setCreatedAt(new DatePoint(timezone: new DateTimeZone("Asia/Seoul")));

        return $user;

    
    }
}
