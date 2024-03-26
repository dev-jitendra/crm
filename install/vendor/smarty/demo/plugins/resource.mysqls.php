<?php


class Smarty_Resource_Mysqls extends Smarty_Resource_Custom
{
    
    protected $db;
    
    protected $fetch;

    public function __construct()
    {
        try {
            $this->db = new PDO("mysql:dbname=test;host=127.0.0.1", "smarty");
        } catch (PDOException $e) {
            throw new SmartyException('Mysql Resource failed: ' . $e->getMessage());
        }
        $this->fetch = $this->db->prepare('SELECT modified, source FROM templates WHERE name = :name');
    }

    
    protected function fetch($name, &$source, &$mtime)
    {
        $this->fetch->execute(array('name' => $name));
        $row = $this->fetch->fetch();
        $this->fetch->closeCursor();
        if ($row) {
            $source = $row['source'];
            $mtime = strtotime($row['modified']);
        } else {
            $source = null;
            $mtime = null;
        }
    }
}
