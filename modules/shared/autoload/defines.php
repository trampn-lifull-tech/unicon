<?php

define('ENTITY_MANAGER', 'Doctrine\ORM\EntityManager');
define('VARS', 'M1\Vars\Vars');

// application
define('CHAOS_MAX_QUERY', 10);

// regex
define('CHAOS_MATCH_ASC_DESC', '#([^\s]+)\s*(asc|desc)?\s*(.*)#i');
define('CHAOS_MATCH_DATE', '#\d{1,4}([-\/.])\d{1,2}\1\d{1,4}#x');
define('CHAOS_REPLACE_COMMA_SEPARATOR', '#\s*,\s*#');
define('CHAOS_REPLACE_SPACE_SEPARATOR', '#\s+#');
