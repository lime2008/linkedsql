<?php
class StmtInit
{
    public function StmtInit($a)
    {
        $b = mysqli_stmt_init($a);
        if (!$b) {
            return array('statu' => false, 'reason' => 'stmt失败');
        } else {
            return array('statu' => true, 'reason' => null, 'callback' => $b);
        }
    }
}