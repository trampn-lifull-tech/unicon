<?php

namespace Chaos\Common\Repository;

use Chaos\Common\Support\Contract\ContainerAware;

/**
 * @todo
 *
 * Class Entity
 * @author ntd1712
 *
 * Entity Data Model is a model that describes entities and the relationships between them.
 */
abstract class Entity /*extends AbstractBaseObjectItem*/ implements Contract\IEntity
{
    use ContainerAware;
}
