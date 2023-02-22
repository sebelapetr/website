<?php
declare(strict_types=1);

namespace App\AdminModule\Components\Datagrids;

use App\Model\Utils\StringUtils;
use Contributte\Translation\Translator;
use ReflectionClass;

abstract class BasicDatagrid extends BaseDatagrid implements ISetupDatagrid
{
    /** @var Translator */
    public $translator;
}