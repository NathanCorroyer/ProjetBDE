<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class
Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startingDateTime = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Expression("this.getStartingDateTime() >= this.getInscriptionLimitDate()", message : "La date de début doit être postérieure ou égale à la date limite d'inscription.")]
    private ?\DateTimeInterface $inscriptionLimitDate = null;

    #[ORM\Column]
    private ?int $maxInscription = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 30, enumType: State::class)]
    private ?State $state = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'activities')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'plannedActivities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $planner = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }


    /**
     * Gets called during validation process to ensure that you can't add a user to an activity if all the
     * inscription slots are full
     * @Assert\Callback
     */
    public function validateMaxInscription(ExecutionContextInterface $context)
    {
        if ($this->users->count() >= $this->maxInscription) {
            $context->buildViolation('The maximum number of users cannot exceed maxInscription.')
                ->atPath('users')
                ->addViolation();
        }
    }

    /*
     * Methode qui sert à retourner le badge associé à la bonne valeur de l'énumération
     * Il serait peut-être bon de la déplacer dans un service?
     */
    public function getStateBadge(): string
    {
        $state = $this->getState();
        switch ($state) {
            case State::Finished:
                return '<div class="badge badge-danger rounded-pill d-inline">' . $state->value . '</div>';
            case State::Ongoing:
                return '<div class="badge badge-primary rounded-pill d-inline">' . $state->value . '</div>';
            case State::Creation:
                return '<div class="badge badge-secondary rounded-pill d-inline">' . $state->value . '</div>';
            case State::Open:
                return '<div class="badge badge-success rounded-pill d-inline">' . $state->value . '</div>';
            case State::Cancelled:
                return '<div class="badge badge-warning rounded-pill d-inline">' . $state->value . '</div>';
            case State::Archived:
                return '<div class="badge badge-dark rounded-pill d-inline">' . $state->value. '</div>';
            case State::Closed:
                return '<div class="badge badge-info rounded-pill d-inline">' . $state->value . '</div>';
            default:
                return '<div class="badge badge-light rounded-pill d-inline">' . $state->value . '</div>';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartingDateTime(): ?\DateTimeInterface
    {
        return $this->startingDateTime;
    }

    public function setStartingDateTime(\DateTimeInterface $startingDateTime): static
    {
        $this->startingDateTime = $startingDateTime;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getInscriptionLimitDate(): ?\DateTimeInterface
    {
        return $this->inscriptionLimitDate;
    }

    public function setInscriptionLimitDate(\DateTimeInterface $inscriptionLimitDate): static
    {
        $this->inscriptionLimitDate = $inscriptionLimitDate;

        return $this;
    }

    public function getMaxInscription(): ?int
    {
        return $this->maxInscription;
    }

    public function setMaxInscription(int $maxInscription): static
    {
        $this->maxInscription = $maxInscription;

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

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(State $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getPlanner(): ?User
    {
        return $this->planner;
    }

    public function setPlanner(?User $planner): static
    {
        $this->planner = $planner;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }


}
