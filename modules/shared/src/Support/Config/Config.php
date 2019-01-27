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
     * {@inheritdoc}
     *
     * @param   mixed|array $items
     * @return  void
     */
    public function __construct($items = [])
    {
        if (2 === func_num_args()) {
            $vars = new Vars($items, func_get_arg(1));
            $items = $vars->getContent();
        }

        parent::__construct($items);
    }
}
