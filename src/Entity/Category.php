<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\Post;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository") @ORM\Table(name="categories")
 * @UniqueEntity("slug")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=200)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $keywords;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="exclude_from_index")
     */
    private $excludeFromIndex;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDateTime;

    /**
     * Many Categories have Many Posts.
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="categories")
     */
    private $posts;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }
    
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): void
    {
        // First we check if we already have this blog post in our collection
        if ($this->posts->contains($post)) {
            // Do nothing if its already part of our collection
            return;
        }

        // Add blog post to our array collection
        $this->posts->add($post);

        // We also add this category to the blog post. This way both entities are 'linked' together.
        // In a manyToMany relationship both entities need to know that they are linked together.
        $post->addCategory($this);
    }

    public function removePost(Post $post): void
    {
        // If the blog post does not exist in the collection, then we don't need to do anything
        if (!$this->posts->contains($post)) {
            return;
        }

        // Remove blog post from the collection
        $this->posts->removeElement($post);

        // Also remove this from the category collection of the blog post
        $post->removeCategory($this);
    }
    
    public function setExcludeFromIndex($exclude = false){
        $this->excludeFromIndex = $exclude;
    }

    public function getExcludeFromIndex(){
        return $this->excludeFromIndex;
    }
}
