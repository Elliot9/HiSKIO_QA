<?php
/**
 * 問題一：
 * 您正在爬樓梯，樓梯具有 n 層階梯，您可以一次爬 1 層階梯，或是一次爬 2 層階梯，請問 n 層階梯有多少種方法可以登頂？請使用 PHP 寫出解決方案。
 *
 */
function Calc(int $RemainLevel){
    if($RemainLevel <= 0){
        throw new Exception('RemainLevel must be Positive');
    }
    if($RemainLevel == 1 || $RemainLevel == 2 ){
        return $RemainLevel;
    }
    return Calc($RemainLevel - 1) + Calc($RemainLevel - 2);
}
var_dump(Calc(10));
