<?php

namespace App\Entity;

use App\Repository\StoryRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=StoryRepository::class)
 */
class Story
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Le titre n'est pas null")
     * @Assert\Length(
     *      min = 5,
     *      max = 150,
     *      minMessage = "minimum {{ limit }} caractères",
     *      maxMessage = "maximum {{ limit }} caractère"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\Length(
     *      min = 10,
     *      max = 1150,
     *      minMessage = "minimum {{ limit }} caractères",
     *      maxMessage = "maximum {{ limit }} caractère"
     * )
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * A story can have many categories
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="story", cascade={"persist", "remove"})
     */
    private $categories;

    public function __construct()
    {
        $this->createdAt = new DateTime('now');
        $this->published = false;
        $this->categories = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getCategories(): ArrayCollection
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     */
    public function addCategories(Category $category): void
    {
        $this->categories->add($category);
        $category->setStory($this);
    }

    /**
     * @param Category $category
     */
    public function removeCategory(Category $category): void
    {
        $this->categories->removeElement($category);
    }

    /**
     * @return Image
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage(Image $image): void
    {
        $this->image = $image;
    }

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @return bool
     */
    public function getPublished(): Boolean
    {
        return $this->published;
    }

    /**
     * @param Boolean $published
     */
    public function setPublished(Boolean $published): void
    {
        $this->published = $published;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}