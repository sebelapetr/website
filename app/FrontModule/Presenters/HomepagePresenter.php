<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;


class HomepagePresenter extends BasePresenter
{
    public function renderDefault(): void
    {
        $this->getTemplate()->setFile(__DIR__ . "/../templates/Homepage/default.latte");
    }
}
