<?php
namespace GetSqlFunc\Delete;
class Delete
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
        try{
            // if (!isset($data['bind']) or !isset($data['clause'])) {
            //     return array('status' => false, 'reason' => '没有绑定数据');
            // }else{
            //     $this->bind=$data['bind'];
            // }
            if (isset($data['bind'])) {
                $this->bind = $data['bind'];
            }
            if (!isset($data['clause'])) {
                $sql = $this->GenerateSql(false, false, $data['table']);
                mysqli_stmt_prepare($this->stmt, $sql);
                mysqli_stmt_execute($this->stmt);
                goto common;
            }else{
                $sql = $this->GenerateSql($data['clause'], $data['bind'], $data['table']);
            }
            mysqli_stmt_prepare($this->stmt, $sql);
            $this->stmt->bind_param($this->bind_mark, ...(array) $data['bind']);
            mysqli_stmt_execute($this->stmt);
            }
        
        catch(Exception $e){
        
            $exception = false;
        }
        common:
        $res=true;
        if(mysqli_affected_rows($data['con']) == 0){
            $res=false;
        }
        if ($res && $exception && empty(mysqli_stmt_error($this->stmt))) {
            return array('status' => true, 'reason' => null);
        } else {
            return array('status' => false, 'reason' => '执行失败或没有匹配的数据');
        }
    }
    private function GenerateSql($clause, $args, $table)
    {
        if (!$clause) {
            $sql = 'DELETE FROM ' . $table;
            return $sql;
        }
        $bind_data = '';
        $mark_data = '';
        foreach ($this->bind as $tmp) {
            if (is_int($tmp)) {
                $this->bind_mark .= 'i';
            } else {
                $this->bind_mark .= 's';
            }
            if ($tmp !== end($args)) {
                $bind_data .= $tmp . ', ';
            } else {
                $bind_data .= $tmp;
                $mark_data .= '?';
            }
        }
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $clause;
        //echo $sql;
        return $sql;
    }
}
