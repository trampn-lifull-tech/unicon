<?php

namespace Chaos\SharedModule\Repository;

use Chaos\SharedModule\Support\Object\Model;
use Chaos\SharedModule\Support\Contract\ContainerAware;

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
