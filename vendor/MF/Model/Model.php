<?php


namespace MF\Model;

abstract class Model {

	protected $db;

	public function __construct(\PDO $db) {
		$this->db = $db;
	}


     protected function registrarAuditoria($acao, $id_tarefa = NULL) {
            // Verifica se a sessão está ativa para obter o ID do usuário
            $id_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : null;

            // Verifica se o ID do usuário está definido antes de registrar a auditoria
            if ($id_usuario !== null) {
                $query = "INSERT INTO Auditoria (id_usuario, id_tarefa, acao, data) VALUES (:id_usuario, :id_tarefa, :acao, NOW())";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':id_usuario', $id_usuario);
                $stmt->bindValue(':id_tarefa', $id_tarefa);
                $stmt->bindValue(':acao', $acao);
                $stmt->execute();
            } else {
                // Se o ID do usuário não estiver definido, não registre a auditoria
                // Você pode optar por registrar um tipo de auditoria diferente ou ignorar completamente
                // Neste exemplo, estou apenas exibindo uma mensagem de aviso
                echo "A sessão do usuário não está ativa. Não é possível registrar a auditoria.";
            }
    }

}


?>