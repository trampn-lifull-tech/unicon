<?php

namespace Chaos\Repository;

use Chaos\Support\Contract\ContainerAware;
use Chaos\Support\Object\Model;

/**
 * Class Entity
 * @author ntd1712
 *
 * Entity Data Model is a model that describes entities and the relationships between them.
 */
class Entity extends Model implements Contract\IEntity
{
    use ContainerAware;
}
