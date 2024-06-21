<?php
class Migration_61 extends Migration {

    const DESCRIPTION_RU = 'Создание таблицы index_hours';

    const DESCRIPTION_EN = 'Create table index_hours';

    public static function up()
    {
        $table = self::getDb()->getPrefix() . 'index_hours';
        $oldTable = self::getDb()->getPrefix() . 'archive_hours';

        $sql = 'CREATE TABLE IF NOT EXISTS ' . $table. ' (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `campaign_id` int(10) unsigned NOT NULL,
              `date` date NOT NULL,
              `value` varchar(250) NOT NULL,
              `count` int(10) unsigned DEFAULT 0,
              `unique` int(10) unsigned DEFAULT 0,
              `leads` int(10) unsigned DEFAULT 0,
              `sales` int(10) unsigned DEFAULT 0,
              `revenue` DECIMAL(13, 4) unsigned DEFAULT 0,
              `cost` DECIMAL(13, 4) unsigned DEFAULT 0,
              PRIMARY KEY (`id`),
              KEY `date_value` (`date`,`value`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        self::execute($sql);

        $prefix = 'INSERT INTO ' . $table . ' (campaign_id, date, value, `count`) VALUES';
        foreach (self::execute('SELECT * FROM ' . $oldTable) as $row) {
            $values = array();
            foreach(explode(',', $row['counts']) as $hour => $count) {
                if ($count) {
                    $values[] = '(' . $row['group_id'] . ', "' . $row['date'] . '", '. $hour . ',' . $count . ')';
                }
            }

            if (count($values)) {
                $sql = $prefix . implode(',', $values);
                self::execute($sql);
            }
        }
    }
}