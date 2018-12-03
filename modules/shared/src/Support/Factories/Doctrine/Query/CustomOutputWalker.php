<?php

namespace Chaos\Common\Support\Factories\Doctrine\Query;

use Doctrine\ORM\Query\SqlWalker;

/**
 * Class CustomOutputWalker
 * @author ntd1712
 */
final class CustomOutputWalker extends SqlWalker
{
    /**
     * {@inheritdoc} @override
     *
     * @param   \Doctrine\ORM\Query\AST\WhereClause $whereClause
     * @return  string The SQL.
     * @throws  \Doctrine\ORM\Query\AST\ASTException
     */
    public function walkWhereClause($whereClause)
    {
        $sql = parent::walkWhereClause($whereClause);

        if (null !== ($options = $this->getQuery()->getHint('options')) && $options['multitenant']['enabled']) {
            $fromClause = $this->getQuery()->getAST()->fromClause;
            $declarations = $fromClause->identificationVariableDeclarations;

            if (1 === count($declarations)) {
                /** @var \Doctrine\ORM\Mapping\ClassMetadata $metadata */
                $dqlAlias = $declarations[0]->rangeVariableDeclaration->aliasIdentificationVariable;
                $metadata = $this->getQueryComponent($dqlAlias)['metadata'];
                $keymap = $options['multitenant']['keymap'];

                if (isset($metadata->fieldMappings[$keymap])) {
                    $parts = explode(' ', $fromClause->dispatch($this));

                    return ($sql ? $sql . ' AND ' : ' WHERE ') . sprintf(
                        "%s.%s='%s'",
                        end($parts),
                        $metadata->fieldMappings[$keymap]['columnName'],
                        $options['app']['key']
                    );
                }
            }
        }

        return $sql;
    }
}
