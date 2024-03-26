<?php


class Smarty_CacheResource_Mysql extends Smarty_CacheResource_Custom
{
    
    protected $db;
    protected $fetch;
    protected $fetchTimestamp;
    protected $save;

    public function __construct()
    {
        try {
            $this->db = new PDO("mysql:dbname=test;host=127.0.0.1", "smarty");
        } catch (PDOException $e) {
            throw new SmartyException('Mysql Resource failed: ' . $e->getMessage());
        }
        $this->fetch = $this->db->prepare('SELECT modified, content FROM output_cache WHERE id = :id');
        $this->fetchTimestamp = $this->db->prepare('SELECT modified FROM output_cache WHERE id = :id');
        $this->save = $this->db->prepare('REPLACE INTO output_cache (id, name, cache_id, compile_id, content)
            VALUES  (:id, :name, :cache_id, :compile_id, :content)');
    }

    
    protected function fetch($id, $name, $cache_id, $compile_id, &$content, &$mtime)
    {
        $this->fetch->execute(array('id' => $id));
        $row = $this->fetch->fetch();
        $this->fetch->closeCursor();
        if ($row) {
            $content = $row['content'];
            $mtime = strtotime($row['modified']);
        } else {
            $content = null;
            $mtime = null;
        }
    }

    
    protected function fetchTimestamp($id, $name, $cache_id, $compile_id)
    {
        $this->fetchTimestamp->execute(array('id' => $id));
        $mtime = strtotime($this->fetchTimestamp->fetchColumn());
        $this->fetchTimestamp->closeCursor();

        return $mtime;
    }

    
    protected function save($id, $name, $cache_id, $compile_id, $exp_time, $content)
    {
        $this->save->execute(array(
            'id' => $id,
            'name' => $name,
            'cache_id' => $cache_id,
            'compile_id' => $compile_id,
            'content' => $content,
        ));

        return !!$this->save->rowCount();
    }

    
    protected function delete($name, $cache_id, $compile_id, $exp_time)
    {
        
        if ($name === null && $cache_id === null && $compile_id === null && $exp_time === null) {
            
            $query = $this->db->query('TRUNCATE TABLE output_cache');

            return -1;
        }
        
        $where = array();
        
        if ($name !== null) {
            $where[] = 'name = ' . $this->db->quote($name);
        }
        
        if ($compile_id !== null) {
            $where[] = 'compile_id = ' . $this->db->quote($compile_id);
        }
        
        if ($exp_time !== null) {
            $where[] = 'modified < DATE_SUB(NOW(), INTERVAL ' . intval($exp_time) . ' SECOND)';
        }
        
        if ($cache_id !== null) {
            $where[] = '(cache_id = '. $this->db->quote($cache_id)
                . ' OR cache_id LIKE '. $this->db->quote($cache_id .'|%') .')';
        }
        
        $query = $this->db->query('DELETE FROM output_cache WHERE ' . join(' AND ', $where));

        return $query->rowCount();
    }
}
