<?php

namespace Chaos\Common\Repository;

use Chaos\Common\Support\Object\Model;
use Chaos\Common\Support\Contract\ContainerAware;

/**
 * Class Entity
 * @author ntd1712
 *
 * Entity Data Model is a model that describes entities and the relationships between them.
 */
abstract class Entity extends Model implements Contract\IEntity
{
    use ContainerAware;
}
