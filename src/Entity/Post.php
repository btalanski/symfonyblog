<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use App\Entity\Category;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository") @ORM\Table(name="posts")
 * @UniqueEntity("slug")
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=1500)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=300)
     * @Assert\NotBlank
     */
    private $excerpt;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $keywords;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $text;

    /**
     * @ORM\Column(type="smallint")
     */
    private $published;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdateDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Media", inversedBy="posts")
     */
    
    private $media;

    /**
     * @Assert\File(mimeTypes={ "image/png", "image/jpg", "image/jpeg" })
     */
    
    private $mediaData;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $tag;

    /**
     * Many Posts have Many Categories.
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="posts")
     * @ORM\JoinTable(name="posts_categories")
     * @Assert\NotBlank
     */
    private $categories;

    private $selectedCategories;

    public function __construct() {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getPublished(): ?int
    {
        return $this->published;
    }

    public function setPublished(int $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getCreateDateTime(): ?\DateTimeInterface
    {
        return $this->createDateTime;
    }

    public function setCreateDateTime(\DateTimeInterface $createDateTime): self
    {
        $this->createDateTime = $createDateTime;

        return $this;
    }

    public function getLastUpdateDateTime(): ?\DateTimeInterface
    {
        return $this->lastUpdateDateTime;
    }

    public function setLastUpdateDateTime(?\DateTimeInterface $lastUpdateDateTime): self
    {
        $this->lastUpdateDateTime = $lastUpdateDateTime;

        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getMediaData()
    {
        return $this->mediaData;
    }

    public function setMediaData($media)
    {
        $this->mediaData = $media;

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function getSelectedCategories(): self
    {
        return $this->selectedCategories;
    }

    public function addCategory(Category $category): void
    {
        // First we check if we already have this category in our collection
        if ($this->categories->contains($category)){
            // Do nothing if its already part of our collection
            return;
        }

        // Add category to our array collection
        $this->categories->add($category);

        // We also add this blog post to the category. This way both entities are 'linked' together.
        // In a manyToMany relationship both entities need to know that they are linked together.
        $category->addPost($this);
    }

    public function removeCategory(Category $category): void
    {
        // If the category does not exist in the collection, then we don't need to do anything
        if (!$this->categories->contains($category)) {
            return;
        }

        // Remove category from the collection
        $this->categories->removeElement($category);

        // Also remove this from the blog post collection of the category
        $category->removePost($this);
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
