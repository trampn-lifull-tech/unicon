<?php

define('ENTITY_MANAGER', 'Doctrine\ORM\EntityManager');
define('VARS', 'M1\Vars\Vars');

define('CHAOS_MAX_QUERY', 10);

define('CHAOS_MATCH_ASC_DESC', '#([^\s]+)\s*(asc|desc)?\s*(.*)#i');
define('CHAOS_REPLACE_COMMA_SEPARATOR', '#\s*,\s*#');
define('CHAOS_REPLACE_SPACE_SEPARATOR', '#\s+#');
