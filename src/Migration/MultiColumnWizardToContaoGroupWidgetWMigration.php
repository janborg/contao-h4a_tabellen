<?php

declare(strict_types=1);

/*
 * This file is part of contao-h4a_tabellen.
 *
 * (c) Jan Lünborg
 *
 * @license MIT
 */

namespace Janborg\H4aTabellen\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

class MultiColumnWizardToContaoGroupWidgetWMigration extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->db->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_calendar'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_calendar');

        if (!isset($columns['h4a_seasons'])) {
            return false;
        }

        return (int) $this->db->fetchOne("SELECT COUNT(*) FROM tl_calendar WHERE h4a_seasons LIKE 'a:1:{i:0;%'") > 0;
    }

    public function run(): MigrationResult
    {
        foreach ($this->db->fetchAllAssociative("SELECT * FROM tl_calendar WHERE h4a_seasons LIKE 'a:1:{i:0;%'") as $field) {
            $templates = [];

            foreach (StringUtil::deserialize($field['h4a_seasons'], true) as $key => $template) {
                if (is_numeric($key)) {
                    ++$key;
                }

                $templates[$key] = $template;
            }

            $this->db->update('tl_calendar', ['h4a_seasons' => serialize($templates)], ['id' => $field['id']]);
        }

        return $this->createResult(true);
    }
}
