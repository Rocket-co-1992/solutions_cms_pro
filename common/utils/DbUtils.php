<?php

namespace Pandao\Common\Utils;

use PDO;

/**
 * Class DbUtils
 * - lastRowCount
 * - dbPrepareInsert
 * - dbPrepareUpdate
 * - dbTableExists
 * - dbColumnExists
 * - dbDescrTable
 * - dbListColumns
 * - dbColumnType
 * - dbGetFieldValue
 * - dbGetSearchRequest
 */

class DbUtils
{
    /**
     * Retrieves the number of rows from the last query.
     *
     * @param Database $db The PDO instance to use for querying.
     * @return int The number of rows.
     */
    public static function lastRowCount($db)
    {
        return $db->query('SELECT FOUND_ROWS()')->fetchColumn();
    }

    /**
     * Prepares an SQL INSERT statement based on the provided data.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table to insert data into.
     * @param array $data The data to insert, with column names as keys.
     * @return PDOStatement The prepared statement ready for execution.
     */
    public static function dbPrepareInsert($db, $table, $data)
    {
        // Fetch columns and build the insert query
        $list_cols = self::dbListColumns($db, $table);
        $nb_cols = count($list_cols);
        $query = 'INSERT INTO ' . $table . ' VALUES(';
        foreach ($list_cols as $i => $column) {
            $query .= ':' . $column;
            if ($i < $nb_cols - 1) $query .= ', ';
        }
        $query .= ')';
        $result = $db->prepare($query);
        foreach ($list_cols as $i => $column) {
            if (array_key_exists($column, $data)) {
                $col_type = self::dbColumnType($db, $table, $column);
                $value = (is_null($data[$column]) || (preg_match('/.*(char|text).*/i', $col_type) !== 1 && $data[$column] == '')) ? null : html_entity_decode($data[$column]);
                $result->bindValue(':' . $column, $value);
            } else {
                $result->bindValue(':' . $column, null);
            }
        }
        return $result;
    }

    /**
     * Prepares an SQL UPDATE statement based on the provided data.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table to update data in.
     * @param array $data The data to update, with column names as keys.
     * @return PDOStatement The prepared statement ready for execution.
     */
    public static function dbPrepareUpdate($db, $table, $data)
    {
        // Build the update query
        $list_cols = self::dbListColumns($db, $table);
        $count_cols = 0;
        $nb_cols = 0;
        foreach ($list_cols as $column) {
            if ($column != 'id' && $column != 'lang' && array_key_exists($column, $data)) {
                $nb_cols++;
            }
        }
        $query = 'UPDATE ' . $table . ' SET ';
        foreach ($list_cols as $i => $column) {
            if ($column != 'id' && $column != 'lang' && array_key_exists($column, $data)) {
                $query .= '`' . $column . '` = :' . $column;
                if ($count_cols < $nb_cols - 1) {
                    $query .= ', ';
                }
                $count_cols++;
            }
        }
        $query .= ' WHERE id = ' . $data['id'];
        if (isset($data['lang']) && self::dbColumnExists($db, $table, 'lang')) {
            $query .= ' AND lang = ' . $db->quote($data['lang']);
        }
        $result = $db->prepare($query);
        foreach ($list_cols as $i => $column) {
            if ($column != 'id' && $column != 'lang' && array_key_exists($column, $data)) {
                $col_type = self::dbColumnType($db, $table, $column);
                $value = (is_null($data[$column]) || (preg_match('/.*(char|text).*/i', $col_type) !== 1 && $data[$column] == '')) ? null : html_entity_decode($data[$column]);
                $result->bindValue(':' . $column, $value);
            }
        }
        return $result;
    }

    /**
     * Checks if a table exists in the database.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table to check.
     * @return bool True if the table exists, false otherwise.
     */
    public static function dbTableExists($db, $table)
    {
        $result = $db->query('SHOW TABLES LIKE ' . $db->quote($table));
        return $result !== false && self::lastRowCount($db) > 0;
    }

    /**
     * Checks if a column exists in a specific table.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table to check.
     * @param string $column The name of the column to check.
     * @return bool True if the column exists, false otherwise.
     */
    public static function dbColumnExists($db, $table, $column)
    {
        $result = $db->query('SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = ' . $db->quote($table) . ' AND TABLE_SCHEMA = ' . $db->quote(PMS_DB_NAME) . ' AND COLUMN_NAME = ' . $db->quote($column));
        return $result !== false && self::lastRowCount($db) > 0;
    }

    /**
     * Describes a table and returns its structure.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table to describe.
     * @param string $column The name of a specific column to describe (optional).
     * @return array The structure of the table or the column.
     */
    public static function dbDescrTable($db, $table, $column = '')
    {
        $query = 'DESCRIBE ' . $table;
        if ($column != '') {
            $query .= ' `' . $column . '`';
        }
        return $db->query($query)->fetchAll();
    }

    /**
     * Returns a list of columns for a given table.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table.
     * @return array|bool The list of column names, or false if none found.
     */
    public static function dbListColumns($db, $table)
    {
        $descr = self::dbDescrTable($db, $table);
        if (is_array($descr) && count($descr) > 0) {
            $fields = [];
            foreach ($descr as $field) {
                $fields[] = $field['Field'];
            }
            return $fields;
        }
        return false;
    }

    /**
     * Retrieves the data type of a specific column in a table.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table.
     * @param string|int $column The name or index of the column.
     * @return string|bool The data type of the column, or false if not found.
     */
    public static function dbColumnType($db, $table, $column)
    {
        $type = false;
        if (is_numeric($column)) {
            $descr = self::dbDescrTable($db, $table);
            if (is_array($descr) && isset($descr[$column])) {
                $type = $descr[$column]['Type'];
            }
        } else {
            $descr = self::dbDescrTable($db, $table, $column);
            if (is_array($descr) && count($descr) == 1) {
                $type = $descr[0]['Type'];
            }
        }
        return $type;
    }

    /**
     * Retrieves the value of a field from a table by its ID.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table.
     * @param string $col The column name to retrieve.
     * @param int $id The ID of the record.
     * @param int $lang The language ID (optional).
     * @return string|bool The field value or false on failure.
     */
    public static function dbGetFieldValue($db, $table, $col, $id, $lang = 0)
    {
        $query = 'SELECT ' . $col . ' FROM ' . $table . ' WHERE id = ' . $id;
        if ($lang > 0 && self::dbColumnExists($db, $table, 'lang')) {
            $query .= ' AND lang = ' . $db->quote($lang);
        }
        $result = $db->query($query);
        if ($result !== false && self::lastRowCount($db) > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $values = [];
            $cols = explode(',', $col);
            foreach ($cols as $col) {
                $values[] = $row[$col];
            }
            return implode(' ', $values);
        } else {
            return false;
        }
    }

    /**
     * Builds an SQL SELECT query for searching a table.
     *
     * @param Database $db The PDO instance for the database connection.
     * @param string $table The name of the table to search.
     * @param array $cols The columns to search within.
     * @param string $q The search query.
     * @param int $limit The maximum number of rows to return (optional).
     * @param int $offset The offset of the first row to return (optional).
     * @param string $condition Additional SQL conditions (optional).
     * @param string $other_condition Other SQL conditions (optional).
     * @param string $order The ORDER BY clause (optional).
     * @param string $sort The sorting direction (optional).
     * @param string $select The columns to select (optional).
     * @param int $len_min The minimum length of each search word (optional).
     * @return string The constructed SQL query.
     */
    public static function dbGetSearchRequest($db, $table, $cols, $q, $limit = 0, $offset = 0, $condition = '', $other_condition = '', $order = '', $sort = 'asc', $select = '', $len_min = 0)
    {
        $search = StrUtils::formatSearch($q, $len_min);
        $q = $search[0];
        $wds = $search[1];

        $nb_wds = count($wds);
        $nb_cols = count($cols);

        $query = 'SELECT';

        if ($select != '') $query .= ' ' . $select . ', ';

        foreach ($cols as $j => $col) {
            $query .= ' (UPPER(`' . $col . '`) LIKE ' . $db->quote($q) . ') AS found_exact_col' . $j . ', ';

            if ($nb_wds > 0) {
                for ($i = 0; $i < $nb_wds; $i++) {
                    $wd = $wds[$i];
                    if ($i == 0) $query .= '(';
                    $query .= '(CASE WHEN(UPPER(`' . $col . '`) LIKE ' . $db->quote('%' . $wd . '%') . ') THEN 1 ELSE 0 END)';
                    if ($i <= $nb_wds - 2) $query .= ' + ';
                    if ($i == $nb_wds - 1) $query .= ') AS found_count_col' . $j . ', ';
                }
                for ($i = 0; $i < $nb_wds; $i++) {
                    $wd = $wds[$i];
                    $query .= '(UPPER(`' . $col . '`) LIKE ' . $db->quote('%' . $wd . '%') . ') AS found_wd' . $i . '_col' . $j;
                    if ($i <= $nb_wds - 2) $query .= ', ';
                }
                $query .= ', ';
            }
        }
        $query .= ' `' . $table . '`.* FROM `' . $table . '` WHERE 1 ' . $condition;

        if (($nb_wds > 0 && $nb_cols > 0) || $other_condition != '') $query .= ' AND ';
        
        if ($nb_wds > 0 && $nb_cols > 0 && $other_condition != '') $query .= ' ( ';

        if ($nb_wds > 0) {
            foreach ($cols as $j => $col) {

                for ($i = 0; $i < $nb_wds; $i++) {
                    if ($condition != '' && $i == 0 && $j == 0) $query .= ' (';
                    $wd = $wds[$i];
                    if ($i == 0) $query .= '(';
                    $query .= 'UPPER(`' . $col . '`) LIKE ' . $db->quote('%' . $wd . '%');
                    if ($i <= $nb_wds - 2) $query .= ' OR ';
                    if ($i == $nb_wds - 1) $query .= ')';
                    if ($condition != '' && $i == $nb_wds - 1 && $j == $nb_cols - 1) $query .= ') ';
                }
                if ($j <= $nb_cols - 2) $query .= ' OR ';
            }
        }

        if ($other_condition != '') {
            if ($nb_wds > 0 && $nb_cols > 0) $query .= ' OR ';
            $query .= '(' . $other_condition . ')';
            if ($nb_wds > 0 && $nb_cols > 0) $query .= ' ) ';
        }

        if (($nb_wds > 0 && $nb_cols > 0) || $order != '') {

            $query .= ' ORDER BY ';

            if ($order == '' && $nb_cols > 0) {
                foreach ($cols as $j => $col) {
                    $query .= 'found_exact_col' . $j . ' DESC, ';
                }

                if ($nb_wds > 0) {
                    foreach ($cols as $j => $col) {
                        $query .= 'found_count_col' . $j . ' DESC, ';
                    }

                    foreach ($cols as $j => $col) {
                        for ($i = 0; $i < $nb_wds; $i++) {
                            $query .= 'found_wd' . $i . '_col' . $j . ' DESC, ';
                        }
                    }
                }
                $query .= '`' . implode('`, `', $cols) . '`';
            } else {
                $query .= '`'.$order.'` ' . $sort;
            }
        }

        if ($limit > 0) {
            $query .= ' LIMIT ' . $limit;
            if ($offset > 0) $query .= ' OFFSET ' . $offset;
        }
        return $query;
    }
}
