<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\AdminModule\Components\BaseComponent;
use App\Model\Enum\FlashMessages;
use App\Model\Orm;
use App\Model\Role;
use App\Model\User;
use Contributte\Translation\Translator;
use Nette;

class UserForm extends BaseComponent
{
    public ?User $user;

    public function __construct(Orm $orm, ?User $user, Translator $translator)
    {
        $this->user = $user;
        parent::__construct($orm, $translator);
    }

    public function render(): void
    {
        $this->getTemplate()->setFile(__DIR__ . '/UserForm.latte');
        $this->getTemplate()->user = $this->user;
        $this->getTemplate()->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addEmail('email', $this->translator->translate($this->langDomain.'.email'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('name', $this->translator->translate($this->langDomain.'.name'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('surname', $this->translator->translate($this->langDomain.'.surname'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addText('phoneNumber', $this->translator->translate($this->langDomain.'.phoneNumber'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $rolesArr = [];
        $roles = $this->orm->roles->findAll()->fetchPairs('id', 'intName');
        foreach ($roles as $id => $name)
        {
            $rolesArr[$id] = $this->translator->translate('roles.'.$name);
        }
        $form->addSelect('role', $this->translator->translate($this->langDomain.'.role'), $rolesArr)
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired();

        $form->addSubmit('send', $this->translator->translate($this->langDomain.'.'.($this->user ? 'edit' : 'add')))
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess'];

        if ($this->user)
        {
            $defaults = $this->user->toArray();
            unset($defaults['role']);
            $defaults['role'] = $this->user->role->id;
            $form->setDefaults($defaults);
        }

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form): void
    {
        $values = $form->getValues();

        if (!$this->user)
        {
            $user = new User();
        } else {
            $user = $this->user;
        }

        $user->name = $values->name;
        $user->surname = $values->surname;
        $user->email = $values->email;
        $user->phoneNumber = $values->phoneNumber;
        $user->active = true;
        $user->createdAt = new \DateTimeImmutable();

        /** @var Role|null $role */
        $role = $this->orm->roles->getById($values->role);

        if ($role == null) {
            throw new \Exception("Role " . $values->role . " not found");
        }
        $user->role = $role;

        $this->orm->persistAndFlush($user);

        if ($this->user) {
            $this->getPresenter()->flashMessage($this->langDomain.'.userEdited', FlashMessages::SUCCESS);
        } else {
            $this->getPresenter()->flashMessage($this->langDomain.'.userAdded', FlashMessages::SUCCESS);
        }

        $this->getPresenter()->redirect('edit', ['id' => $user->id]);
    }
}