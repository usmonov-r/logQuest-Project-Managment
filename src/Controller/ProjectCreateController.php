<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Project;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Component\ProjectFactory;

class ProjectCreateController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectFactory $factory,
        ){}

    public function __invoke(Project $data): Project{

        $user = $this->getUser();
        $pro = $this->factory->create(
            $data->getTitle(), 
            $data->getDescription(), 
            $data->getDeadline(), 
            $user,
            $data->getStatus()
        );

        $this->entityManager->persist($pro);
        $this->entityManager->flush();

        return $pro;
    }

}
