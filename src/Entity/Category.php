<?php

namespace App\Entity;

use App\Entity\Fleurie;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $libelle;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Fleurie::class)]
    private $fleuries;

    public function tojson(bool $fleurie=false): ?array
    {
        return $this ? [
            'id' => $this->id,
            'libelle' => $this->libelle,
            'fleuries'=> $fleurie ? array_map(function(Fleurie $fleurie){
                return $fleurie->tojson();
            },$this->fleuries->getValues()):[],
        ] : null;
    }


    public function __construct()
    {
        $this->fleuries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Fleurie>
     */
    public function getFleuries(): Collection
    {
        return $this->fleuries;
    }

    public function addFleury(Fleurie $fleury): self
    {
        if (!$this->fleuries->contains($fleury)) {
            $this->fleuries[] = $fleury;
            $fleury->setCategory($this);
        }

        return $this;
    }

    public function removeFleury(Fleurie $fleury): self
    {
        if ($this->fleuries->removeElement($fleury)) {
            // set the owning side to null (unless already changed)
            if ($fleury->getCategory() === $this) {
                $fleury->setCategory(null);
            }
        }

        return $this;
    }
}
