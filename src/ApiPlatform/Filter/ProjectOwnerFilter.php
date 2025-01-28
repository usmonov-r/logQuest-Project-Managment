<?php

namespace App\ApiPlatform\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use App\Entity\Project;

final class ProjectOwnerFilter extends AbstractFilter
{
    private $security;

    public function __construct(
        ManagerRegistry $managerRegistry,
        Security $security,
        ?NameConverterInterface $nameConverter = null
    ) {
        parent::__construct($managerRegistry, $nameConverter);
        $this->security = $security;
    }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($resourceClass !== Project::class) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName('user');
        
        $queryBuilder
            ->andWhere(sprintf('%s.author = :%s', $rootAlias, $parameterName))
            ->setParameter($parameterName, $user);
    }

    public function getDescription(string $resourceClass): array
    {
        if ($resourceClass !== Project::class) {
            return [];
        }

        return [
            'author' => [
                'property' => 'author',
                'type' => 'string',
                'required' => false,
                'description' => 'Filter projects by current user',
            ],
        ];
    }
}
