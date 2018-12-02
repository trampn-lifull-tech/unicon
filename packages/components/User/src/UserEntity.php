<?php

namespace Chaos\Component\User;

/**
 * Class UserEntity
 * @author ntd1712
 *
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="Chaos\Component\User\UserRepository")
 * @Doctrine\ORM\Mapping\EntityListeners({ "Chaos\Component\User\UserListener" })
 * @Doctrine\ORM\Mapping\Table(name="user")
 */
class UserEntity // extends Entity
{
    // use IdentityAware, AuditAware;

    /**
     * @Doctrine\ORM\Mapping\Column(name="name", type="string")
     */
    protected $Name;

    /**
     * @Doctrine\ORM\Mapping\Column(name="email", type="string", nullable=true)
     */
    protected $Email;

    /**
     * @Doctrine\ORM\Mapping\Column(name="password", type="string", nullable=true)
     */
    private $Password;

    /**
     * @Doctrine\ORM\Mapping\Column(name="password_expiry_date", type="datetime", nullable=true)
     */
    private $PasswordExpiryDate;

    /**
     * @Doctrine\ORM\Mapping\Column(name="remember_token", type="string", length=100, nullable=true)
     */
    private $RememberToken;

    /**
     * @Doctrine\ORM\Mapping\Column(name="open_id", type="string", length=64, nullable=true)
     */
    private $OpenId;

    /**
     * @Doctrine\ORM\Mapping\Column(name="locale", type="string", length=20, nullable=true)
     */
    protected $Locale = 'en';

    /**
     * @Doctrine\ORM\Mapping\Column(name="timezone", type="string", nullable=true)
     */
    protected $Timezone = 'UTC';

    /**
     * @Doctrine\ORM\Mapping\Column(name="profile", type="json_array", length=65535, nullable=true)
     */
    protected $Profile;
}
