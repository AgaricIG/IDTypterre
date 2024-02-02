<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FicheSupplRepository")
 */
class FicheSuppl
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Uts", inversedBy="ficheSuppls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $uts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Application", inversedBy="ficheSuppls")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"api"})
     */
    private $application;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api"})
     */
    private $url;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUts(): ?Uts
    {
        return $this->uts;
    }

    public function setUts(?Uts $uts): self
    {
        $this->uts = $uts;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
