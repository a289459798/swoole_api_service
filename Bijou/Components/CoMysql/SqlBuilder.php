<?php

namespace Bijou\Components\CoMysql;
class SqlBuilder
{
    private $transLock = '';
    private $colLiteral = '`';
    private $valLiteral = "'";

    /**
     * @param $table
     * @param string $column
     * @param null $cond
     * @param null $order
     * @param int $limit
     * @param null $group
     * @param null $having
     * @return string
     */
    final public function getAllSql($table, $column = "*", $cond = null, $order = null, $limit = 0, $group = null, $having = null)
    {
        return $this->getSelectSQL($table, $column, $cond, $order, $limit, $group, $having);
    }

    /**
     * @param $table
     * @param $column
     * @param null $cond
     * @param null $order
     * @param int $limit
     * @param null $group
     * @param null $having
     * @return bool|string
     */
    final public function getColumnSql($table, $column, $cond = null, $order = null, $limit = 0, $group = null, $having = null)
    {
        if (!empty($table) && !empty($column)) {
            return $this->getSelectSQL($table, $column, $cond, $order, $limit, $group, $having);
        } else {
            return false;
        }
    }

    /**
     * @param $table
     * @param string $column
     * @param null $cond
     * @param null $order
     * @param int $seek
     * @param null $group
     * @param null $having
     * @return bool|string
     */
    final public function getOneSql($table, $column = "*", $cond = null, $order = null, $seek = 0, $group = null, $having = null)
    {
        if (!empty($table)) {
            return $this->getSelectSQL($table, $column, $cond, $order, array($seek, 1), $group, $having);
        } else {
            return false;
        }
    }

    /**
     * @param $table
     * @param $column
     * @param null $cond
     * @param null $order
     * @param int $seek
     * @return bool|string
     */
    final public function getCellSql($table, $column, $cond = null, $order = null, $seek = 0)
    {
        if (!empty($table) && !empty($column)) {
            return $this->getSelectSQL($table, $column, $cond, $order, array($seek, 1));
        } else {
            return false;
        }
    }

    /**
     * @param $table
     * @param null $cond
     * @param null $colName
     * @param null $group
     * @param null $having
     * @return bool|string
     */
    final public function getCountSql($table, $cond = null, $colName = null, $group = null, $having = null)
    {
        if (!empty($table)) {
            return $this->getSelectSQL($table, "{DB_COUNT}" . (!empty($colName) ? $colName : "*"), $cond, null, 1, $group, $having);
        } else
            return false;
    }

    /**
     * @param $table
     * @param $colName
     * @param null $cond
     * @param null $group
     * @param null $having
     * @return bool|float
     */
    final public function getSumSql($table, $colName, $cond = null, $group = null, $having = null)
    {
        if (!empty($table)) {
            return $this->getSelectSQL($table, "{DB_SUM}" . (!empty($colName) ? $colName : ""), $cond, null, 1, $group, $having);
        } else
            return false;
    }

    /**
     * @param $table
     * @param $colDatas
     * @return bool|string
     */
    final public function getInsertSql($table, $colDatas)
    {
        if (!empty($table) && is_array($colDatas) && !empty($colDatas)) {
            $this->transLock = '';
            $tableStr = $this->convertSplit($this->escapeStr($table));
            $setCol = '';
            foreach ($colDatas as $colName => $colData)
                $setCol .= (!empty($setCol) ? ", " : "") . $this->getColNm($colName) . " = " . $this->getColVal($colData, $colName);
            return "INSERT INTO {$this->colLiteral}{$tableStr}{$this->colLiteral} SET {$setCol}";
        } else return false;
    }

    /**
     * @param $table
     * @param $fields
     * @param $values
     * @return bool|string
     */
    final public function getInsertsSql($table, $fields, $values)
    {
        if (!is_array($fields) || !is_array($values) || empty($fields) || empty($values)) return false;
        if (!empty($table) && is_array($fields) && !empty($values)) {
            $this->transLock = '';
            $tableStr = $this->convertSplit($this->escapeStr($table));
            $iColField = '';
            $insertValues = '';
            $countField = 0;
            foreach ($fields as $colName) {
                $countField++;
                $iColField .= (!empty($iColField) ? ", " : "") . $this->getColNm($colName);
            }
            if (count($values) === 1) $values = array(reset($values));
            foreach ($values as $value) {
                $insertValue = "";
                if (!empty($value) && is_array($value) && count($value) === $countField) {
                    foreach ($value as $val) $insertValue .= (!empty($insertValue) ? ", " : "") . $this->getColVal($val);
                    $insertValues .= (!empty($insertValues) ? ", " : "") . "({$insertValue})";
                }
            }
            return (!empty($insertValues)) ? "INSERT INTO {$this->colLiteral}{$tableStr}{$this->colLiteral} ({$iColField}) VALUES {$insertValues}" : false;
        } else
            return false;
    }

    /**
     * @param $table
     * @param $colDatas
     * @param null $cond
     * @param null $order
     * @param null $limit
     * @return bool|string
     */
    final public function getUpdateSql($table, $colDatas, $cond = null, $order = null, $limit = null)
    {
        if (!empty($table) && is_array($colDatas) && !empty($colDatas)) {
            $this->transLock = '';
            $tableStr = $this->convertSplit($this->escapeStr($table));
            $setCol = '';
            foreach ($colDatas as $colName => $colData) $setCol .= (!empty($setCol) ? ", " : "") . $this->getColNm($colName) . " = " . $this->getColVal($colData, $colName);
            return "UPDATE {$this->colLiteral}{$tableStr}{$this->colLiteral} SET {$setCol}" . $this->getCondition($cond) . $this->getOrder($order) . $this->getLimit($limit);
        } else
            return false;
    }

    /**
     * @param $table
     * @param $colDatas
     * @return bool|string
     */
    final public function getInsertUpdateSql($table, $colDatas)
    {
        if (!empty($table) && is_array($colDatas) && !empty($colDatas)) {
            $this->transLock = '';
            $tableStr = $this->convertSplit($this->escapeStr($table));
            $columnUpdate = '';
            $setCol = '';
            foreach ($colDatas as $colName => $colData) {
                $setCol .= (!empty($setCol) ? ", " : "") . $this->getColNm($colName) . " = " . $this->getColVal($colData, $colName);
                $columnUpdate .= (!empty($columnUpdate) ? ", " : "") . "{$colName} = VALUES({$colName})";
            }
            return "INSERT INTO {$this->colLiteral}{$tableStr}{$this->colLiteral} SET {$setCol} ON DUPLICATE KEY UPDATE {$columnUpdate}";
        } else return false;
    }

    /**
     * @param $table
     * @param $fields
     * @param $values
     * @return bool|string
     */
    final public function getInsertsUpdateSql($table, $fields, $values)
    {
        if (!is_array($fields) || !is_array($values) || empty($fields) || empty($values)) return false;
        if (!empty($table) && is_array($fields) && !empty($values)) {
            $this->transLock = '';
            $tableStr = $this->convertSplit($this->escapeStr($table));
            $iColField = '';
            $insertValues = '';
            $columnUpdate = '';
            $countField = 0;
            foreach ($fields as $colName) {
                $countField++;
                $colName = $this->getColNm($colName);
                $iColField .= (!empty($iColField) ? ", " : "") . $colName;
                $columnUpdate .= (!empty($columnUpdate) ? ", " : "") . "{$colName} = VALUES({$colName})";
            }
            if (count($values) === 1) $values = array(reset($values));
            foreach ($values as $value) {
                $insertValue = "";
                if (!empty($value) && is_array($value) && count($value) === $countField) {
                    foreach ($value as $val) $insertValue .= (!empty($insertValue) ? ", " : "") . $this->getColVal($val);
                    $insertValues .= (!empty($insertValues) ? ", " : "") . "({$insertValue})";
                }
            }
            return (!empty($insertValues) && !empty($columnUpdate)) ? "INSERT INTO {$this->colLiteral}{$tableStr}{$this->colLiteral} ({$iColField}) VALUES {$insertValues} ON DUPLICATE KEY UPDATE {$columnUpdate}" : false;
        } else
            return false;
    }

    /**
     * @param $table
     * @param null $cond
     * @param null $order
     * @param null $limit
     * @return bool|string
     */
    public function getDeleteSql($table, $cond = null, $order = null, $limit = null)
    {
        if (!empty($table)) {
            $this->transLock = '';
            $tableStr = $this->convertSplit($this->escapeStr($table));
            return "DELETE FROM {$this->colLiteral}{$tableStr}{$this->colLiteral}" . $this->getCondition($cond) . $this->getOrder($order) . $this->getLimit($limit);
        } else
            return false;
    }

    /**
     * @param bool $forUpdate
     */
    final public function setLockMode($forUpdate = true)
    {
        $this->transLock = ($forUpdate === true ? 'FOR UPDATE' : "LOCK IN SHARE MODE");
    }

    /**
     * @param $tables
     * @param string $Mode
     * @return string
     */
    final public function getLockTableSql($tables, $Mode = 'WRITE')
    {
        $Mode = ($Mode === 'WRITE' || $Mode === 'READ') ? $Mode : 'WRITE';
        $tableStr = '';
        if (is_array($tables) && !empty($tables)) {
            foreach ($tables as $table)
                $tableStr .= (!empty($tableStr) ? ", " : "") . $this->colLiteral . $this->escapeStr($table) . "{$this->colLiteral} {$Mode}";
        } elseif (!empty($tables))
            $tableStr = $this->colLiteral . $this->escapeStr($tables) . "{$this->colLiteral} {$Mode}";
        if (!empty($tableStr))
            $tableStr = "LOCK TABLES {$tableStr}";
        return $tableStr;
    }

    /**
     * @return string
     */
    final public function getUnlockTableSql()
    {
        return "UNLOCK TABLES";
    }

    /**
     * @param $colName
     * @param null $colAlias
     * @param bool $alias
     * @param bool $Fnc
     * @return string
     */
    final private function getColNm($colName, $colAlias = null, $alias = true, $Fnc = false)
    {
        $result = '';
        $pattern = "/^\\{DB_([A-Z_]+)\\}/";
        if (preg_match("/^{$this->valLiteral}(.+){$this->valLiteral}$/", $colName, $matchs)) $result = $this->valLiteral . $this->escapeStr($matchs[1]) . $this->valLiteral; else {
            $colName = $this->convertSplit($colName);
            if (preg_match($pattern, $colName, $matchs)) {
                $match = strtoupper($matchs[1]);
                switch ($match) {
                    case 'DISTINCT':
                        $result = "DISTINCT " . $this->getColNm(str_replace($matchs[0], "", $colName), null, false);
                        break;
                    case 'NO_CACHE':
                        $result = "SQL_NO_CACHE " . $this->getColNm(str_replace($matchs[0], "", $colName), null, false);
                        break;
                    case 'HIGH_PRIORITY':
                        $result = "HIGH_PRIORITY " . $this->getColNm(str_replace($matchs[0], "", $colName), null, false);
                        break;
                    case 'LOW_PRIORITY':
                        $result = "LOW_PRIORITY " . $this->getColNm(str_replace($matchs[0], "", $colName), null, false);
                        break;
                    case "MAX":
                        $colName = str_replace($matchs[0], "", $colName);
                        $result = "MAX(" . $this->getColNm($colName, null, false) . ")";
                        break;
                    case "MIN":
                        $colName = str_replace($matchs[0], "", $colName);
                        $result = "MIN(" . $this->getColNm($colName, null, false) . ")";
                        break;
                    case 'FNC':
                        $result = $this->getColNm(str_replace($matchs[0], "", $colName), null, false, true);
                        break;
                    case 'VAL':
                        $result = "'" . $this->getColNm(str_replace($matchs[0], "", $colName), null, false, true) . "'";
                        break;
                    case "SUM":
                        $colName = str_replace($matchs[0], "", $colName);
                        $result = "SUM(" . $this->getColNm($colName, null, false) . ")";
                        break;
                    case "NOW":
                        //$colName = str_replace($matchs[0], "", $colName);
                        $result = "NOW()";
                        break;
                    case "COUNT":
                        $colName = str_replace($matchs[0], "", $colName);
                        $result = ($colName === "*" ? "COUNT(*)" : "COUNT(" . $this->getColNm($colName, null, false) . ")");
                        break;
                    case in_array($match, range('A', 'Z')):
                        $result = $this->getColNm(str_replace($matchs[0], "", $colName), null, false);
                        break;
                }
            } elseif ($Fnc === false && strpos($colName, $this->colLiteral) === false && $colName !== '*') $result = $this->colLiteral . $this->escapeStr($colName) . $this->colLiteral;
            else $result = $this->escapeStr($colName);
            if ($alias) $result .= (!is_int($colAlias) && !empty($colAlias) ? " AS {$this->valLiteral}" . $this->escapeStr($colAlias) . $this->valLiteral : "");
        }
        return $result;
    }

    /**
     * @param null $colValue
     * @param null $colName
     * @return string
     */
    final private function getColVal($colValue = null, $colName = null)
    {
        if ($colValue === null)
            return "NULL";
        else {
            $pattern = "/^\\{DB_([A-Z_]+)\\}/";
            if (is_array($colValue) || is_object($colValue)) $colValue = json_encode($colValue);
            if (preg_match($pattern, $colValue, $matchs)) {
                $colValue = $this->escapeStr(str_replace($matchs[0], "", $colValue));
                switch (strtoupper($matchs[1])) {
                    case 'COL':
                        return $this->getColNm($colValue);
                    case 'FNC':
                        return $colValue;
                    case "NOW":
                        return "NOW()";
                    case "SUM":
                        return $this->getColNm($colValue);
                    case "COUNT":
                        return $this->getColNm($colValue);
                    case "TIME":
                        return "CURRENT_TIME()";
                    case "DATE":
                        return "CURRENT_DATE()";
                    case "INC":
                        $colValue = (double)$colValue;
                        return $this->getColNm($colName) . " + {$colValue}";
                        break;
                    case "DEC":
                        $colValue = (double)$colValue;
                        return $this->getColNm($colName) . " - {$colValue}";
                    case "TIMES":
                        $colValue = (double)$colValue;
                        return $this->getColNm($colName) . " * {$colValue}";
                    case "DIV":
                        $colValue = (double)$colValue;
                        return $this->getColNm($colName) . " / {$colValue}";
                    case "POWER":
                        $colValue = (double)$colValue;
                        return $this->getColNm($colName) . " ^ {$colValue}";
                }
            } else
                return $this->valLiteral . $this->escapeStr($colValue) . $this->valLiteral;
        }
        return 'NULL';
    }

    /**
     * @param $column
     * @param bool $alias
     * @return string
     */
    final private function getColSQL($column, $alias = true)
    {
        $columnStr = "";
        if (!empty($column)) {
            switch (gettype($column)) {
                case 'array':
                    foreach ($column as $ColAlies => $colName) $columnStr .= (!empty($columnStr) ? ", " : "") . $this->getColNm($colName, $ColAlies, $alias);
                    break;
                case 'string';
                    $columnStr = $this->getColNm($column, null, $alias);
            }
        }
        return $columnStr;
    }

    /**
     * @param $colName
     * @param $colValue
     * @return string
     */
    final private function getCond($colName, $colValue)
    {
        $condStr = '';
        switch (gettype($colValue)) {
            case 'array':
                $value = reset($colValue);
                $key = key($colValue);
                $condStr = $this->getColNm($colName);
                $result = array();
                switch (strtoupper($key)) {
                    case "LIST":
                    case "IN":
                        if (!is_array($value)) $value = array();
                        if (!empty($value)) foreach ($value as $k => $v) $result[] = $this->escapeStr($v);
                        $condStr .= " IN ({$this->valLiteral}" . implode("{$this->valLiteral},{$this->valLiteral}", $result) . "{$this->valLiteral})";
                        break;
                    case "XLIST":
                    case "XIN":
                        if (!is_array($value)) $value = array();
                        if (!empty($value)) foreach ($value as $k => $v) $result[] = $this->escapeStr($v);
                        $condStr .= " NOT IN ({$this->valLiteral}" . implode("{$this->valLiteral},{$this->valLiteral}", $result) . "{$this->valLiteral})";
                        break;
                    case "BETWEEN":
                        if (is_array($value) && count($value) === 2) {
                            $value = array_values($value);
                            $condStr .= " BETWEEN {$this->valLiteral}" . $this->escapeStr($value[0]) . "{$this->valLiteral} AND {$this->valLiteral}" . $this->escapeStr($value[1]) . $this->valLiteral;
                        }
                        break;
                    case "XBETWEEN":
                        if (is_array($value) && count($value) === 2) {
                            $value = array_values($value);
                            $condStr .= " NOT BETWEEN {$this->valLiteral}" . $this->escapeStr($value[0]) . "{$this->valLiteral} AND {$this->valLiteral}" . $this->escapeStr($value[1]) . $this->valLiteral;
                        }
                        break;
                    default:
                        $condStr = "";
                }
                break;
            case 'double':
            case 'integer':
            case "string":
            case 'boolean';
                $pattern = "/^\\{DB_([A-Z]+)\\}/";
                $Operator = " = ";
                if (is_bool($colValue)) $colValue = '';
                if (preg_match($pattern, $colValue, $matchs)) {
                    switch (strtoupper($matchs[1])) {
                        case "NE":
                            $Operator = " != ";
                            break;
                        case "GE":
                            $Operator = " >= ";
                            break;
                        case "GT":
                            $Operator = " > ";
                            break;
                        case "LE":
                            $Operator = " <= ";
                            break;
                        case "LT":
                            $Operator = " < ";
                            break;
                        case "NNE":
                            $Operator = " <=> ";
                            break;
                        case "LIKE":
                            $Operator = " LIKE ";
                            break;
                        case "XLIKE":
                            $Operator = "NOT LIKE ";
                            break;
                        case "INULL":
                            $Operator = " IS NULL";
                            break;
                        case "XINULL":
                            $Operator = " IS NOT NULL";
                            break;
                    }
                    if ($Operator !== " = ") $colValue = str_replace($matchs[0], "", $colValue);
                }
                $condStr = $this->getColNm($colName) . "{$Operator}";
                $condStr .= substr($Operator, -1) === " " ? $this->getColVal($colValue, $colName) : "";
                break;
            case 'NULL':
                $condStr = $this->getColNm($colName) . " = null";
        }
        return $condStr;
    }

    /**
     * @param null $cond
     * @return string
     */
    final private function getCondition($cond = null)
    {
        $condStr = '';
        $matchs = array();
        if (!empty($cond)) {
            if (is_array($cond)) {
                $pattern = "/^\\{DB_(OR|XOR|LB|RB|AND)\\}$/i";
                $logic = true;
                $braket = 0;
                $matchs = array();
                foreach ($cond as $key => $value) {
                    if (is_string($value) && preg_match($pattern, $value, $matchs)) {
                        switch (strtoupper($matchs[1])) {
                            case "OR":
                                $condStr .= " OR ";
                                $logic = true;
                                break;
                            case "XOR":
                                $condStr .= " XOR ";
                                $logic = true;
                                break;
                            case "LB":
                                $condStr .= (!$logic ? " AND " : "") . "(";
                                $braket++;
                                $logic = true;
                                break;
                            case "RB":
                                if ($braket > 0) {
                                    $braket--;
                                    $condStr .= ")";
                                }
                                break;
                            default:
                                $condStr .= " AND ";
                                $logic = true;
                                break;
                        }
                    } else {
                        if (!empty($key)) {
                            $condStr .= (!$logic ? " AND " : "") . $this->getCond($key, $value);
                            $logic = false;
                        }
                    }
                }
                $condStr = preg_replace("/(\\s+)(AND|OR|XOR|\\()(\\s+)$/i", "", $condStr);
                if ($braket > 0) for ($i = $braket; $i > 0; $i--) $condStr .= ")";
            } elseif (preg_match_all("/({$this->colLiteral}?)([^\\W]|[\\w\\d\\-]+)({$this->colLiteral}?)(\\s*)([\\>=\\<!]|LIKE|IS NOT NULL|IN|NOT IN|BETWEEN|NOT BETWEEN)(\\s*)(({$this->valLiteral}([^{$this->valLiteral}]*){$this->valLiteral})|[\\d\\.]|(\\((.*)\\))*)/i", $cond, $matchs) <= 0) $condStr = $cond;
        }
        return $cond !== null ? " WHERE {$condStr}" : "";
    }

    /**
     * @param null $order
     * @return string
     */
    final private function getOrder($order = null)
    {
        $orderStr = '';
        if (!empty($order)) {
            if (is_array($order)) {
                foreach ($order as $key => $orderType) {
                    if (is_int($key)) {
                        if ($orderType === "{DB_RAND}") $key = $orderType; else {
                            $key = $orderType;
                            $orderType = "ASC";
                        }
                    } else {
                        $orderType = strtoupper($orderType);
                        if ($orderType != "DESC") $orderType = "ASC";
                    }
                    if ($key === "{DB_RAND}") $orderStr .= (!empty($orderStr) ? ", " : "") . "RAND()"; else {
                        $key = $this->getColNm($key);
                        $orderStr .= (!empty($orderStr) ? ", " : "") . "{$key} {$orderType}";
                    }
                }
            } elseif (!empty($order)) $orderStr = strtoupper($order) === "{DB_RAND}" ? "RAND()" : $this->getColNm($order) . " ASC";
        }
        return !empty($orderStr) ? " ORDER BY {$orderStr}" : "";
    }

    /**
     * @param null $group
     * @param null $having
     * @return string
     */
    final private function getGroup($group = null, $having = null)
    {
        if (is_array($group)) $group = array_values($group);
        $groupStr = $this->getColSQL($group);
        return !empty($groupStr) ? " GROUP BY {$groupStr}" . $this->getHaving($having) : "";
    }

    /**
     * @param null $cond
     * @return string
     */
    final private function getHaving($cond = null)
    {
        return !empty($cond) ? " HAVING " . substr($this->getCondition($cond), 7) : "";
    }

    /**
     * @param null $limit
     * @return string
     */
    final public function getLimit($limit = null)
    {
        $limitStr = '';
        if (!empty($limit)) {
            switch (gettype($limit)) {
                case "array":
                    $start = (int)reset($limit);
                    $limit = (int)next($limit);
                    if (!empty($limit)) $limitStr = "{$start}, {$limit}";
                    break;
                case "string":
                case "double":
                case "integer":
                    $limitStr = (int)$limit;
                    break;
            }
        }
        return !empty($limitStr) ? " LIMIT {$limitStr}" : "";
    }

    /**
     * @param $table
     * @param string $column
     * @param null $cond
     * @param null $order
     * @param null $limit
     * @param null $group
     * @param null $having
     * @return bool|string
     */
    final private function getSelectSQL($table, $column = "*", $cond = null, $order = null, $limit = null, $group = null, $having = null)
    {
        if (!empty($table)) {
            $tableStr = $this->convertSplit($this->escapeStr($table));
            $result = "SELECT " . $this->getColSQL($column) . " FROM {$this->colLiteral}{$tableStr}{$this->colLiteral}" . $this->getCondition($cond) . $this->getGroup($group, $having) . $this->getOrder($order) . $this->getLimit($limit) . " " . $this->transLock;
        } else {
            $result = false;
        }

        $this->transLock = '';
        return $result;
    }

    /**
     * @param $queryStr
     * @return mixed|string
     */
    final private function escapeStr($queryStr)
    {
        return str_replace(array("'", '`'), array("\\'", '\\`'), $queryStr);
    }

    /**
     * @param $data
     * @return mixed
     */
    final private function convertSplit($data)
    {
        return str_replace(array(".", ","), array("{$this->colLiteral}.{$this->colLiteral}", "{$this->colLiteral},{$this->colLiteral}"), $data);
    }
}
