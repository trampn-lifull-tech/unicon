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
     * @param   string[]|string $resource A path or an array of paths where to look for resources.
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
