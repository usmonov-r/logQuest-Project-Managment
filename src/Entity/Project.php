<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Controller\ProjectCreateController;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiFilter;
use App\ApiPlatform\Filter\ProjectOwnerFilter;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiFilter(ProjectOwnerFilter::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Only authenticated users can access projects."
        ),
        new Get(
            security: "is_granted('ROLE_USER') and object.getAuthor() == user",
            securityMessage: "You can only access your own projects."
        ),
        new Patch(
            security: "is_granted('ROLE_USER') and object.getAuthor() == user",
            securityMessage: "You can only edit your own projects."
        ),
        new Delete(
            security: "is_granted('ROLE_USER') and object.getAuthor() == user",
            securityMessage: "You can only delete your own projects."
        ),
        new Post(
            uriTemplate: '/projects',
            controller: ProjectCreateController::class,
            name: 'Project Create',
            security: "is_granted('ROLE_USER')",
            securityMessage: "You need to be logged in to create projects."
        )
    ],
    normalizationContext: ['groups' => 'pro:read'],
    denormalizationContext: ['groups' => 'pro:write']
)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('pro:read', 'note:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['pro:read', 'pro:write', 'note:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['pro:read', 'pro:write', 'note:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['pro:read', 'pro:write'])]
    private ?string $deadline = null;

    #[ORM\Column]
    #[Groups(['pro:read', 'pro:write'])]
    private ?bool $isActive = true;

    #[ORM\Column]
    #[Groups(['pro:read', 'pro:write'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'project', orphanRemoval: true)]
    #[Groups(['pro:read','user:read'])]
    private Collection $notes;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[Groups([ 'pro:read','user:read'])]
    private ?User $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['pro:read', 'pro:write'])]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups('pro:read')]
    private int $notesCount = 0;

    #[PrePersist]
    #[PreUpdate]
    
    public function updateNotesCount(): void
    {
        $this->notesCount = $this->notes->count();
    }


    public function getNotesCount(): int
    {
        return $this->notes->count();
    }

    public function setNotesCount(int $count): self{
        $this->notesCount = $count;
        return $this;
    }

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDeadline(): ?string
    {
        return $this->deadline;
    }

    public function setDeadline(?string $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setProject($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getProject() === $this) {
                $note->setProject(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
