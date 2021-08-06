<?php
namespace Library\DB;

interface IAdaptor{

    public function query($sql);
    public function escape($value);
    public function countAffected();
    public function getLastId();
    public function connected();
}