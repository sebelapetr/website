<?php

declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\HasMany;
use Nextras\Orm\Relationships\HasOne;
use Nextras\Orm\Relationships\ManyHasMany;

/**
 * Class Action
 * @package App\Model
 * @property int $id {primary}
 * @property string $name
 * @property HasOne|Right $right {m:1 Right::$actions}
 * @property ManyHasMany|Role $roles {m:m Role::$actions}
 */
class Action extends Entity
{

}