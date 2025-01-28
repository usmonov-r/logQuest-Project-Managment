<?php


namespace App\Component;

use App\Entity\Project;
use DateTimeZone;
use Symfony\Component\Clock\DatePoint;
use App\Entity\User;


class ProjectFactory{

    public function create(
        string $title,  
        string $description, 
        string $deadline, 
        User $user,
        ?string $status = null
    ): Project {
        
        $project = new Project();

        $project->setTitle($title);
        $project->setDescription($description);
        $project->setDeadline($deadline);
        $project->setCreatedAt(new DatePoint(timezone: new DateTimeZone("Asia/Seoul")));
        $project->setAuthor($user);
        $project->setStatus($status);
        
        return $project;
    }

}
