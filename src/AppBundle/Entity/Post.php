<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $is_pinned = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $pinnedAt;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $social_provider;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $social_id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="origin_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $originUser;

    /**
     * @var Tradeland
     *
     * @ORM\ManyToOne(targetEntity="Tradeland", inversedBy="posts")
     * @ORM\JoinColumn(name="tradeland_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $tradeland;

    /**
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post", cascade={"persist"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="Media", mappedBy="post", cascade={"persist"})
     */
    private $media;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(name="post_votes",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $post_votes;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="post_hidden")
     * @ORM\JoinTable(name="post_hidden",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $post_hidden;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->updatedTimestamps();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime("now"));

        /*   if(is_null($this->getCreatedAt()))
           {
               $this->setCreatedAt(new \DateTime("now"));
           }*/
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Post
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Post
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Post
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set originUser
     *
     * @param \AppBundle\Entity\User $originUser
     *
     * @return Post
     */
    public function setOriginUser(\AppBundle\Entity\User $originUser = null)
    {
        $this->originUser = $originUser;

        return $this;
    }

    /**
     * Get originUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getOriginUser()
    {
        return $this->originUser;
    }

    /**
     * Set tradeland
     *
     * @param \AppBundle\Entity\Tradeland $tradeland
     *
     * @return Post
     */
    public function setTradeland(\AppBundle\Entity\Tradeland $tradeland = null)
    {
        $this->tradeland = $tradeland;

        return $this;
    }

    /**
     * Get tradeland
     *
     * @return \AppBundle\Entity\Tradeland
     */
    public function getTradeland()
    {
        return $this->tradeland;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Post
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set socialProvider
     *
     * @param string $socialProvider
     *
     * @return Post
     */
    public function setSocialProvider($socialProvider)
    {
        $this->social_provider = $socialProvider;

        return $this;
    }

    /**
     * Get socialProvider
     *
     * @return string
     */
    public function getSocialProvider()
    {
        return $this->social_provider;
    }

    /**
     * Set socialId
     *
     * @param string $socialId
     *
     * @return Post
     */
    public function setSocialId($socialId)
    {
        $this->social_id = $socialId;

        return $this;
    }

    /**
     * Get socialId
     *
     * @return string
     */
    public function getSocialId()
    {
        return $this->social_id;
    }

    /**
     * Detects whether the post is mine or not
     *
     * @return string
     */
    public function isMine()
    {
        return \App::getInstance()->getUserId() == $this->user->getId();
    }

    /**
     * Add medium
     *
     * @param \AppBundle\Entity\Media $medium
     *
     * @return Post
     */
    public function addMedia(\AppBundle\Entity\Media $medium)
    {
        $this->media[] = $medium;

        return $this;
    }

    /**
     * Remove medium
     *
     * @param \AppBundle\Entity\Media $medium
     */
    public function removeMedia(\AppBundle\Entity\Media $medium)
    {
        $this->media->removeElement($medium);
    }

    /**
     * Get media
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Get videos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
         return $this->media->filter(function(Media $media) {
         	return $media->getTypeId() == 2;
         });
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
         return $this->media->filter(function(Media $media) {
         	return $media->getTypeId() == 1;
         });
    }

    public function getNumVotes() {
    	return \App::getTable("AppBundle:Post")->numVotes($this->id);
    }

    public function getVoteList() {
    	return \App::getTable("AppBundle:Post")->listVoted($this->id);
    }

    /**
     * Set pinned
     *
     * @param boolean $pinned
     *
     * @return Post
     */
    public function setPinned($pinned)
    {
        $this->is_pinned = $pinned;

        return $this;
    }

    /**
     * Get pinned
     *
     * @return boolean
     */
    public function getPinned()
    {
        return $this->is_pinned;
    }

    /**
     * Set pinnedAt
     *
     * @param \DateTime $pinnedAt
     *
     * @return Post
     */
    public function setPinnedAt($pinnedAt)
    {
        $this->pinnedAt = $pinnedAt;

        return $this;
    }

    /**
     * Get pinnedAt
     *
     * @return \DateTime
     */
    public function getPinnedAt()
    {
        return $this->pinnedAt;
    }

    public function isPinned() {
    	return $this->is_pinned && ((time() - $this->pinnedAt->getTimestamp()) < 604800);
    }
}
