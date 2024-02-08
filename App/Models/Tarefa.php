<?php

namespace App\Models;
use MF\Model\Model;

class Tarefa extends Model {
	private $id;
	private $id_usuario;
	private $descricao;
	private $titulo;
	private $data;
	private $situacao;
    private $dataConclusao;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	public function salvar() {


		$query = "insert into Tarefas(id_usuario, descricao, titulo, situacao)values(:id_usuario, :descricao, :titulo, 1)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $_SESSION['id']);
		$stmt->bindValue(':descricao', $_POST['descricao']);
		$stmt->bindValue(':titulo', $_POST['titulo'] ?? 'Título Padrão');

		$stmt->execute();

		$id_tarefa = $this->db->lastInsertId();
		$acao = "Criou uma nova tarefa com o título '{$_POST['titulo']}'";
		 $this->registrarAuditoria($acao, $id_tarefa);

		return $this;
	}
	
	public function removeTarefa($id_tarefa, $id_usuario) {
	    $query = "update tarefas set situacao = 0 WHERE id = :id_tarefa AND id_usuario = :id_usuario";
	    
	    $stmt = $this->db->prepare($query);
	    $stmt->bindValue(':id_tarefa', $id_tarefa);
	    $stmt->bindValue(':id_usuario', $id_usuario);
	    $stmt->execute();

	    $acao = "Removeu a tarefa com ID {$id_tarefa}";
        $this->registrarAuditoria($acao, $id_tarefa);


	    return $stmt->rowCount() > 0;
	}

	public function concluirTarefa($id_tarefa, $id_usuario, $situacao) {

	    $query = "update tarefas set situacao = :situacao, dataConclusao = NOW() WHERE id = :id_tarefa AND id_usuario = :id_usuario ";
	    
	    $stmt = $this->db->prepare($query);
	    $stmt->bindValue(':id_tarefa', $id_tarefa);
	    $stmt->bindValue(':id_usuario', $id_usuario);
	    $stmt->bindValue(':situacao', $situacao);
	    $stmt->execute();

	    $acao = "Concluiu a tarefa.";
        $this->registrarAuditoria($acao, $id_tarefa);

	    return $stmt->rowCount() > 0;
	}

	//recuperar
	public function getAll() {

		$query = "
			select 
				t.id, 
				t.id_usuario, 
				u.nome, 
				t.titulo,
				t.descricao, 
				t.situacao as t_situacao,
				s.situacao as s_situacao,
                DATE_FORMAT(t.dataConclusao, '%d/%m/%Y %H:%i') as dataConclusao,
				DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
			from 
				tarefas as t
				left join usuarios as u on (t.id_usuario = u.id)
				left join situacao as s on (t.situacao = s.idSituacao)
			where 
				t.situacao !=0 AND 
				(t.id_usuario = :id_usuario
				or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario))
			order by
				t.dataConclusao, t.data,  desc
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getPorPagina($limit, $offset) {

	    $query = "
	        SELECT 
	            t.id, 
	            t.id_usuario, 
	            u.nome,
	            t.titulo,
	            t.descricao,
	            t.situacao as t_situacao,
	            s.situacao as s_situacao,
	            DATE_FORMAT(t.dataConclusao, '%d/%m/%Y %H:%i') as dataConclusao,
	            DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
	        FROM 
	            tarefas as t
	            LEFT JOIN usuarios as u ON (t.id_usuario = u.id)
	            LEFT JOIN situacao as s ON (t.situacao = s.idSituacao)
	        WHERE 
	        	t.situacao !=0 AND 
	            (t.id_usuario = :id_usuario OR t.id_usuario IN (SELECT id_usuario_seguindo FROM usuarios_seguidores WHERE id_usuario = :id_usuario))

	        ORDER BY
	            t.data DESC, t.dataConclusao DESC
	        LIMIT
	            :limit
	        OFFSET
	            :offset
	    ";

	    $stmt = $this->db->prepare($query);
	    $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
	    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
	    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
	    $stmt->execute();

	    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getPorPaginaPerfil($limit, $offset) {

		$query = "
			select 
				t.id, 
				t.id_usuario, 
				u.nome, 
				t.titulo,
				t.descricao, 
				t.situacao as t_situacao,
				s.situacao as s_situacao,
                DATE_FORMAT(t.dataConclusao, '%d/%m/%Y %H:%i') as dataConclusao,
				DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
			from 
				tarefas as t
				left join usuarios as u on (t.id_usuario = u.id)
				left join situacao as s on (t.situacao = s.idSituacao)
			where 
				t.situacao !=0 AND 
				t.id_usuario = :id_usuario
			order by
				s.situacao, t.data, t.dataConclusao  desc
			limit
				$limit
			offset
				$offset
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function getTotalRegistros() {

		$query = "
			select 
				count(t.id) as total 
			from 
				tarefas as t
				left join usuarios as u on (t.id_usuario = u.id)
			where 
				t.situacao !=0 AND 
				(t.id_usuario = :id_usuario
				or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario))
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	public function getTotalRegistrosPerfil() {

		$query = "
			select 
				count(t.id) as total 
			from 
				tarefas as t
				left join usuarios as u on (t.id_usuario = u.id)
			where 
				t.situacao !=0 AND 
				t.id_usuario = :id_usuario
		";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}