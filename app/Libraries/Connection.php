<?php
require_once __DIR__ . "/../Config/Config.php";
class Connection {
    public function connect(){
        $mysql = new mysqli(server, user, password, database);
        $mysql->set_charset(charset);
        if(mysqli_errno($mysql)){
            echo "Error ".mysqli_errno($mysql)." ,no conectado a la base de datos";
            exit();
        }
        return $mysql;
    }
}
?>
