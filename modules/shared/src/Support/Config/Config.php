<?php

namespace Chaos\Support\Config;

use Illuminate\Config\Repository;
use M1\Vars\Vars;

/**
 * Class Config
 * @author ntd1712
 */
class Config extends Repository implements Contract\ConfigInterface
{
    /**
     * Sets the resource contents.
     *
     * @param   string|array $resource The main configuration resource.
     * @param   array $options The options being used.
     * @return  self
     */
    public function __invoke($resource, $options = [])
    {
        $vars = new Vars($resource, $options);
        $this->items = $vars->getContent();

        return $this;
    }
}
