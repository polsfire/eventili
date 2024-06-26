<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReclamationRepository;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\DateTime;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id_rep", type: "integer", nullable: false)]
    #[Groups("Reponses")]
    private $idRep;

    #[ORM\Column(name: "message", type: "text", length: 65535, nullable: false)]
    #[Groups("Reponses")]
    private $message;

    #[ORM\Column(name: "timestamp", type: "datetime", nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Groups("Reponses")]
    private $timestamp;

    #[ORM\Column(name: "isEdited", type: "boolean", nullable: false)]
    #[Groups("Reponses")]
    private $isedited = '0';

    #[ORM\ManyToOne(targetEntity: Reclamation::class)]
    #[ORM\JoinColumn(name: "rec_id", referencedColumnName: "id_rec")]
    #[Groups("Reponses")]
    private $rec;

    #[ORM\ManyToOne(targetEntity: Personne::class)]
    #[ORM\JoinColumn(name: "senderId", referencedColumnName: "id_pers")]
    #[Groups("Reponses")]
    private $senderid;

    public function getIdRep(): ?int
    {
        return $this->idRep;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function isIsedited(): ?bool
    {
        return $this->isedited;
    }

    public function setIsedited(bool $isedited): self
    {
        $this->isedited = $isedited;

        return $this;
    }

    public function getRec(): ?Reclamation
    {
        return $this->rec;
    }

    public function setRec(?Reclamation $rec): self
    {
        $this->rec = $rec;

        return $this;
    }

    public function getSenderid(): ?Personne
    {
        return $this->senderid;
    }

    public function setSenderid(?Personne $senderid): self
    {
        $this->senderid = $senderid;

        return $this;
    }

}
