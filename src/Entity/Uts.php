<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtsRepository")
 * @UniqueEntity(fields={"id"}, message="Cet ID existe déjà...")
 */
class Uts
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fiche = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Ucs", inversedBy="uts")
     */
    private $Ucs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FicheSuppl", mappedBy="uts", cascade={"persist","remove"})
     */
    private $ficheSuppls;

    public function __construct()
    {
        $this->Ucs = new ArrayCollection();
        $this->application = new ArrayCollection();
        $this->ficheSuppls = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFiche(): ?string
    {
        return $this->fiche;
    }

    public function setFiche($fiche): self
    {
        $this->fiche = $fiche;

        return $this;
    }

    /**
     * @return Collection|Ucs[]
     */
    public function getUcs(): Collection
    {
        return $this->Ucs;
    }

    public function addUc(Ucs $uc): self
    {
        if (!$this->Ucs->contains($uc)) {
            $this->Ucs[] = $uc;
        }

        return $this;
    }

    public function removeUc(Ucs $uc): self
    {
        if ($this->Ucs->contains($uc)) {
            $this->Ucs->removeElement($uc);
        }

        return $this;
    }

    /**
     * @return Collection|FicheSuppl[]
     */
    public function getFicheSuppls(): Collection
    {
        return $this->ficheSuppls;
    }

    public function addFicheSuppl(FicheSuppl $ficheSuppl): self
    {
        if (!$this->ficheSuppls->contains($ficheSuppl)) {
            $this->ficheSuppls[] = $ficheSuppl;
            $ficheSuppl->setUts($this);
        }

        return $this;
    }

    public function removeFicheSuppl(FicheSuppl $ficheSuppl): self
    {
        if ($this->ficheSuppls->contains($ficheSuppl)) {
            $this->ficheSuppls->removeElement($ficheSuppl);
            // set the owning side to null (unless already changed)
            if ($ficheSuppl->getUts() === $this) {
                $ficheSuppl->setUts(null);
            }
        }

        return $this;
    }
}
