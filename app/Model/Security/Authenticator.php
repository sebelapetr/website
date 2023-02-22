<?php
declare(strict_types=1);

namespace App\Model\Security;

use App\Model\Orm;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class Authenticator implements IAuthenticator
{

    private Orm $orm;
    private Passwords $passwords;

    public function __construct(Orm $orm, Passwords $passwords)
    {
        $this->orm = $orm;
        $this->passwords = $passwords;
    }

    /**
     * @param array{0: string, 1: string} $credentials
     * @return IIdentity
     */
    function authenticate(array $credentials): IIdentity
    {
        /** @var \App\Model\User|null $user */
        $user = $this->orm->users->getBy(['email'=>$credentials[0]]);

        if($user == NULL){
            throw new \LogicException("forms.LoginForm.userNotFound");
        }

        if (!$user->active)
        {
            throw new \LogicException("forms.LoginForm.userIsNotActive");
        }

        if($user->password == null)
        {
            throw new \LogicException("forms.LoginForm.passwordNotSet");
        }

        if (!$this->passwords->verify($credentials[1], $user->password)) {
            throw new \LogicException('forms.LoginForm.badPassword');
        }

        $user->lastLogin = new \DateTimeImmutable();
        $this->orm->persistAndFlush($user);

        return new Identity($user->id, $user->role->intName);
    }
}