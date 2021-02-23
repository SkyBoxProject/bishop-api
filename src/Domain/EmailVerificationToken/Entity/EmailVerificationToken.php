<?php

namespace App\Domain\EmailVerificationToken\Entity;

use App\Domain\Security\UserRole;
use App\Domain\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(name="`email_verification_tokens`")
 *
 * @final
 */
class EmailVerificationToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $token;

    /**
     * @OneToOne(targetEntity="App\Domain\User\Entity\User")
     *
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private bool $verified;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->verified = false;

        $this->token = $this->generateToken();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function markAsVerified(): self
    {
        $this->verified = true;
        $this->user->addRole(UserRole::ROLE_VERIFIED);

        return $this;
    }

    private function generateToken(): string
    {
        $bytes = random_bytes(100);
        $integer = random_int(10000, 99999);

        return mb_substr(sprintf('%s%s', bin2hex($bytes), bin2hex($integer)), 0, 180);
    }
}
