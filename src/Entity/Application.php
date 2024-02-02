<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api"})
     *
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api"})
     *
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FicheSuppl", mappedBy="application")
     */
    private $ficheSuppls;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api"})
     */
    private $apikey = null;

    public function __construct()
    {
        $this->ficheSuppls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

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
            $ficheSuppl->setApplication($this);
        }

        return $this;
    }

    public function removeFicheSuppl(FicheSuppl $ficheSuppl): self
    {
        if ($this->ficheSuppls->contains($ficheSuppl)) {
            $this->ficheSuppls->removeElement($ficheSuppl);
            // set the owning side to null (unless already changed)
            if ($ficheSuppl->getApplication() === $this) {
                $ficheSuppl->setApplication(null);
            }
        }

        return $this;
    }

    public function getApikey(): ?string
    {
        return $this->apikey;
    }

    public function setApikey(string $apikey): self
    {
        $this->apikey = $apikey;

        return $this;
    }
}
