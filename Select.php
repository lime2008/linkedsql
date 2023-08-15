<?php
namespace GetSqlFunc\Select;
class Select
{
    private $stmt;
    private $bind_mark;
    private $bind;
    function Stmt_Loader($value)
    {
        $this->stmt = $value;
    }
    public function Do($data)
    {
        $exception = true;
        try {
            if (!isset($data['clause'])) {
                $sql = $this->GenerateSql(false, $data['key'], $data['table']);
                mysqli_stmt_prepare($this->stmt, $sql);
                mysqli_stmt_execute($this->stmt);
                goto common;
            }
            if (isset($data['bind'])) {
                $this->bind = $data['bind'];
            }
            $sql = $this->GenerateSql($data['clause'], $data['key'], $data['table']);
            mysqli_stmt_prepare($this->stmt, $sql);
            $this->stmt->bind_param($this->bind_mark, ...(array) $data['bind']);
            mysqli_stmt_execute($this->stmt);
        common:
        
        $tmparray = array();
        $res = $this->stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            array_unshift($tmparray, $row);
        }
        if (empty(mysqli_stmt_error($this->stmt))) {
            return array('status' => true, 'reason' => null, 'callback' => $tmparray);
        } else {
            return array('status' => false, 'reason' => '查询失败');
        }
        } catch (Exception $e) {
            return array('status' => false, 'reason' => '查询失败'.$e);
        }
    }
    private function GenerateSql($clause, $args, $table)
    {
        $key_data = '';
        foreach ($args as $tmp) {
            if ($tmp !== end($args)) {
                $key_data .= $tmp . ', ';
            } else {
                $key_data .= $tmp;
            }
        }
        if (!$clause) {
            $sql = 'SELECT ' . $key_data . ' FROM ' . $table;
            return $sql;
        }
        $bind_data = '';
        $mark_data = '';
        $tmpnum = 0;
        $notend = true;
        $count = count($this->bind) - 1;
        foreach ($this->bind as $tmp) {
            if ($tmpnum == $count) {
                $notend = false;
            }
            if (is_int($tmp)) {
                $this->bind_mark .= 'i';
            } else {
                $this->bind_mark .= 's';
            }
            if ($notend) {
                $bind_data .= $tmp . ', ';
                $mark_data .= '? ,';
            } else {
                $bind_data .= $tmp;
                $mark_data .= '?';
            }
            $tmpnum++;
        }
        $sql = 'SELECT ' . $key_data . ' FROM ' . $table . ' WHERE ' . $clause;
        return $sql;
    }
}