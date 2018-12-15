<?php

define('DOCTRINE_ENTITY_MANAGER', 'Doctrine\ORM\EntityManager');
define('M1_VARS', 'M1\Vars\Vars');

define('CHAOS_SQL_LIMIT', 10);
define('CHAOS_SQL_MAX_LIMIT', 1000);

define('CHAOS_MATCH_ASC_DESC', '#([^\s]+)\s*(asc|desc)?\s*(.*)#i');
define('CHAOS_REPLACE_COMMA_SEPARATOR', '#\s*,\s*#');
define('CHAOS_REPLACE_SPACE_SEPARATOR', '#\s+#');
