<?php

namespace App\Domain\User\Entity;

use App\Domain\EmailVerificationToken\Entity\EmailVerificationToken;
use App\Domain\Feed\Entity\Feed;
use App\Domain\License\Collection\LicenseCollection;
use App\Domain\License\Entity\License;
use App\Domain\Security\UserRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(name="`users`")
 *
 * @final
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private UuidV4 $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @OneToOne(targetEntity="App\Domain\EmailVerificationToken\Entity\EmailVerificationToken", cascade={"all"})
     *
     * @JoinColumn(name="email_verification_token", referencedColumnName="token", onDelete="SET NULL")
     */
    private EmailVerificationToken $emailVerificationToken;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\OneToMany (targetEntity="App\Domain\Feed\Entity\Feed", mappedBy="user")
     */
    private $feeds;

    /**
     * @ORM\OneToMany (targetEntity="App\Domain\License\Entity\License", mappedBy="user")
     */
    private $licenses;

    public function __construct(UuidV4 $uuid, string $email)
    {
        $this->id = $uuid;
        $this->email = $email;
        $this->emailVerificationToken = new EmailVerificationToken($this);

        $this->licenses = new ArrayCollection();
        $this->feeds = new ArrayCollection();
    }

    public function getId(): ?UuidV4
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getEmailVerificationToken(): EmailVerificationToken
    {
        return $this->emailVerificationToken;
    }

    /**
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $roles[] = UserRole::ROLE_USER;

        $this->roles = array_unique($roles);

        return $this;
    }

    public function addRole(string $role): self
    {
        if (in_array($role, $this->getRoles(), true)) {
            return $this;
        }

        $this->roles[] = $role;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): void
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFeeds()
    {
        return $this->feeds;
    }

    public function addFeed(Feed $feed): self
    {
        $this->feeds[] = $feed;

        return $this;
    }

    public function getLicenses(): LicenseCollection
    {
        return new LicenseCollection($this->licenses);
    }

    public function addLicense(License $license): self
    {
        $this->licenses[] = $license;

        return $this;
    }
}
