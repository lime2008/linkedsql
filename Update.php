<?php
namespace GetSqlFunc\Update;
class Update
{
    private $stmt;
    private $bind_mark;
    private $bind;
    private $e;
    private $value;
    function Stmt_Loader($value)
    {
        $this->stmt = $value;
    }
    public function Do($data)
    {
        $exception = true;
        try {
            if (isset($data['bind'])) {
                $this->bind = $data['bind'];
            }
            $this->value = $data['value'];
            if (!isset($data['clause'])) {
            $sql = $this->GenerateSql(false, $data['key'], $data['table']);
            }else{
                $sql = $this->GenerateSql($data['clause'], $data['key'], $data['table']);
            }
            mysqli_stmt_prepare($this->stmt, $sql);
            if (!$data['clause']) {
                $this->stmt->bind_param($this->bind_mark, ...(array) $data['value']);
                mysqli_stmt_execute($this->stmt);
                goto common;
            }
          
            $this->stmt->bind_param($this->bind_mark, ...(array) array_merge($data['value'], $data['bind']));
            mysqli_stmt_execute($this->stmt);
            
        } catch (Exception $e) {
            $exception = false;
            $this->e = $e;
        }
        common:
        if (mysqli_affected_rows($data['con']) == 0) {
            $res = false;
        } else {
            $res = true;
        }
        if ($res && $exception && empty(mysqli_stmt_error($this->stmt))) {
            return array('status' => true, 'reason' => null);
        } else {
            
            return array('status' => false, 'reason' => '执行失败或没有匹配的数据', 'error' => $this->e . ' ' . mysqli_stmt_error($this->stmt));
        }
    }
    private function GenerateSql($clause, $args, $table)
    {
        $key_data = '';
        foreach ($args as $tmp) {
            if ($tmp !== end($args)) {
                $key_data .= $tmp . '= ? , ';
            } else {
                $key_data .= $tmp . '= ? ';
            }
        }
        foreach ($this->value as $tmp) {
            if (is_int($tmp)) {
                $this->bind_mark .= 'i';
            } else {
                $this->bind_mark .= 's';
            }
        }
        if (!$clause) {
            $sql = 'UPDATE ' . $table . ' SET ' . $key_data;
            return $sql;
        }
        $mark_data = '';
        foreach ($this->bind as $tmp) {
            if (is_int($tmp)) {
                $this->bind_mark .= 'i';
            } else {
                $this->bind_mark .= 's';
            }
        }
        
        $sql = 'UPDATE ' . $table . ' SET ' . $key_data . ' WHERE ' . $clause;
        return $sql;
    }
}