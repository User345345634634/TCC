<?php

namespace App;

class Connection {

	public static function getDb() {
		try {

			$conn = new \PDO(
				"mysql:host=localhost;dbname=listacompartilhada;charset=utf8",
				"root",
				"" 
			);

			return $conn;

		} catch (\PDOException $e) {
			echo 'Erro: '.$e->getCode().' Mensagem: '.$e->getMenssage();
		}
	}
}

?>