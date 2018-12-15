<?php

namespace Chaos\Common\Repository;

use Chaos\Common\Contract\ContainerAware;
use Chaos\Common\Object\Model;

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
