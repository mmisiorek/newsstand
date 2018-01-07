<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Validator\Constraint as AppAssert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @ORM\Column(name="email", type="string", length=128, unique=true)
     * @Assert\Email()
     */
    private $email;

    private $plainPassword;

    /**
     * @ORM\Column(name="password", type="string", length=128, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(name="verification_hash", type="string", length=32, unique=true, nullable=false)
     */
    private $verificationHash;

    /**
     * @ORM\Column(name="is_verified", type="boolean")
     */
    private $isVerified;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\News", mappedBy="user")
     */
    private $newsCollection;

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
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $plainPassword string
     * @return $this
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return $this
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getSalt()
    {
        return null;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->newsCollection = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add newsCollection
     *
     * @param \AppBundle\Entity\News $newsCollection
     *
     * @return User
     */
    public function addNewsCollection(\AppBundle\Entity\News $newsCollection)
    {
        $this->newsCollection[] = $newsCollection;

        return $this;
    }

    /**
     * Remove newsCollection
     *
     * @param \AppBundle\Entity\News $newsCollection
     */
    public function removeNewsCollection(\AppBundle\Entity\News $newsCollection)
    {
        $this->newsCollection->removeElement($newsCollection);
    }

    /**
     * Get newsCollection
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNewsCollection()
    {
        return $this->newsCollection;
    }

    /**
     * Set verificationHash
     *
     * @param string $verificationHash
     *
     * @return User
     */
    public function setVerificationHash($verificationHash)
    {
        $this->verificationHash = $verificationHash;

        return $this;
    }

    /**
     * Get verificationHash
     *
     * @return string
     */
    public function getVerificationHash()
    {
        return $this->verificationHash;
    }

    /**
     * Set isVerified
     *
     * @param boolean $isVerified
     *
     * @return User
     */
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Get isVerified
     *
     * @return boolean
     */
    public function getIsVerified()
    {
        return $this->isVerified;
    }
}
