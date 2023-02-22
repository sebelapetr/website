<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:32
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\Order;
use App\Model\Role;
use Nextras\Orm\Collection\ICollection;

class DashboardPresenter extends BaseAppPresenter
{
    public function renderDefault(): void
    {
        $this->getTemplate()->newOrders = $this->orm->orders->findNewOrders();
        $this->getTemplate()->notCompletedOrders = $this->orm->orders->findNotCompletedOrders();
    }

}