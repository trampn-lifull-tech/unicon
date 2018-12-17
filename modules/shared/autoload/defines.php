<?php

define('DOCTRINE_ENTITY_MANAGER', 'Doctrine\ORM\EntityManager');
define('M1_VARS', 'M1\Vars\Vars');

define('CHAOS_QUERY_LIMIT', 10);
define('CHAOS_QUERY_MAX_PER_PAGE', 100);

define('CHAOS_MATCH_ASC_DESC', '#([^\s]+)\s*(asc|desc)?\s*(.*)#i');
define('CHAOS_REPLACE_COMMA_SEPARATOR', '#\s*,\s*#');
define('CHAOS_REPLACE_SPACE_SEPARATOR', '#\s+#');
