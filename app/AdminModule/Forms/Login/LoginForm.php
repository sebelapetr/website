<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\AdminModule\Components\BaseComponent;
use App\AdminModule\Presenters\AuthenticationPresenter;
use App\Model\Orm;
use Nette;

class LoginForm extends BaseComponent
{

    public function render(): void
    {
        $this->getTemplate()->setFile(__DIR__ . '/LoginForm.latte');
        $this->getTemplate()->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addText('email', $this->translator->translate($this->langDomain.'.email'))
            ->setHtmlAttribute('class', 'form-control form-control-user');

        $form->addPassword('password', $this->translator->translate($this->langDomain.'.password'))
            ->setHtmlAttribute('class', 'form-control form-control-user');

        $form->addSubmit('login', $this->translator->translate($this->langDomain.'.login'))
            ->setHtmlAttribute('class', 'btn btn-primary btn-user btn-block');

        $form->onSuccess[] = [$this, 'onSuccess']; /** @phpstan-ignore-line */
        $form->onValidate[] = [$this, 'onValidate'];

        return $form;
    }

    public function onValidate(Nette\Application\UI\Form $form): void
    {

    }

    public function onSuccess(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values): void
    {
        try {
            $this->getPresenter()->getUser()->login($values->email, $values->password);
        } catch (\Exception $exception) {
            $form->addError($exception->getMessage());
            return;
        }

        $this->getPresenter()->flashMessage('forms.LoginForm.success', 'SUCCESS');
        $this->getPresenter()->redirect(AuthenticationPresenter::BASE_LOGGED_LINK);
    }
}