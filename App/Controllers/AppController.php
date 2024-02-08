<?php

namespace App\Controllers;

//recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {


	public function perfil() {

		$this->validaAutenticacao();

		$id_usuario_perfil = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : $_SESSION['id'];

	    $usuario = Container::getModel('Usuario');
	    $usuario->__set('id', $id_usuario_perfil);

		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tarefas = $usuario->getTotaltarefas();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();
		$this->view->seguindo_sn = $usuario->isSeguindo()['seguindo_sn']; 

		$tarefa = Container::getModel('tarefa');
		$tarefa->__set('id_usuario', $id_usuario_perfil);

		//variaveis de paginação
		$total_registros_pagina = 10;
		$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
		$deslocamento = ($pagina-1)*$total_registros_pagina;

		//$tarefas = $tarefa->getAll();
		$tarefas = $tarefa->getPorPaginaPerfil($total_registros_pagina, $deslocamento);
		$total_tarefas = $tarefa->getTotalRegistrosPerfil();
		$this->view->total_de_paginas = ceil($total_tarefas['total'] / $total_registros_pagina);
		$this->view->tarefas = $tarefas;


		$this->render('perfil');
	}

	public function timeline() {

		$this->validaAutenticacao();
			
		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		//recuperação dos tarefas
		$tarefa = Container::getModel('tarefa');
		$tarefa->__set('id_usuario', $_SESSION['id']);


		//variaveis de paginação
		$total_registros_pagina = 10;
		$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
		$deslocamento = ($pagina-1)*$total_registros_pagina;


		//$tarefas = $tarefa->getAll();
		$tarefas = $tarefa->getPorPagina($total_registros_pagina, $deslocamento);
		$total_tarefas = $tarefa->getTotalRegistros();
		$this->view->total_de_paginas = ceil($total_tarefas['total'] / $total_registros_pagina);
		$this->view->tarefas = $tarefas;
		$this->render('timeline');
			
	}

	public function navbar() {

		$this->validaAutenticacao();
			
		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);
			
	}

	public function tarefa() {

		$this->validaAutenticacao();

		 if (empty($_POST['titulo'])) {

		    header('Location:\timeline?mensagem=O titulo é obrigatório.');
		    exit();
		}

		$tarefa = Container::getModel('tarefa');

		$tarefa->__set('descricao', $_POST['descricao']);
		$tarefa->__set('id_usuario', $_SESSION['id']);

		$tarefa->salvar();

		header('Location: /timeline');
		
	}

	public function removeTarefa() {
	    $this->validaAutenticacao();
	    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
	    $id_tarefa = isset($_GET['id_tarefa']) ? $_GET['id_tarefa'] : '';

	    $usuario = Container::getModel('Usuario');
	    $usuario->__set('id', $_SESSION['id']);

	    $tarefa = Container::getModel('Tarefa');
	    $tarefa->__set('id', $id_tarefa);

	    // Obtém a URL anterior
		$url_anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/timeline';

		if ($acao == 'remover') {
		    // Verifica se o usuário autenticado é o criador da tarefa antes de remover
		    if ($tarefa->removeTarefa($id_tarefa, $_SESSION['id'])) {
		        // Obtém o caminho relativo da URL anterior
		        $path_anterior = parse_url($url_anterior, PHP_URL_PATH);
		        
		        // Redireciona para o caminho relativo com a mensagem na URL
		        header('Location: ' . $path_anterior . '?mensagem=Tarefa removida com sucesso!');
		        exit();
		    } else {

		    	$path_anterior = parse_url($url_anterior, PHP_URL_PATH);
		        // Redireciona para o caminho relativo com a mensagem na URL
		        header('Location: ' . $path_anterior . '?mensagem=Você não tem permissão para remover a tarefa.');
		        exit();
		    }
		}

	}

	public function concluirTarefa() {
	    $this->validaAutenticacao();
	    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
	    $id_tarefa = isset($_GET['id_tarefa']) ? $_GET['id_tarefa'] : '';

	    $usuario = Container::getModel('Usuario');
	    $usuario->__set('id', $_SESSION['id']);

	    $tarefa = Container::getModel('Tarefa');
	    $tarefa->__set('id', $id_tarefa);

	    // Obtém a URL anterior
		$url_anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/timeline';

		if ($acao == 'concluir') {
		    // Verifica se o usuário autenticado é o criador da tarefa antes de remover
		    if ($tarefa->concluirTarefa($id_tarefa, $_SESSION['id'], 2)) {
		        // Obtém o caminho relativo da URL anterior
		        $path_anterior = parse_url($url_anterior, PHP_URL_PATH);
		        
		        // Redireciona para o caminho relativo com a mensagem na URL
		        header('Location: ' . $path_anterior . '?mensagem=Tarefa concluida com sucesso!');
		        exit();
		    } else {
		        // Redireciona para o caminho relativo com a mensagem na URL
		        header('Location: ' . $path_anterior . '?mensagem=Você não tem permissão para concluir a tarefa.');
		        exit();
		    }
		}

	}

	public function validaAutenticacao() {

		session_start();

		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');
		}	

	}

	public function quemSeguir() {

		$this->validaAutenticacao();

		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';
		
		$usuarios = array();

		if($pesquisarPor != '') {
			
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisarPor);
			$usuario->__set('id', $_SESSION['id']);
			$usuarios = $usuario->getAll();

		}

		$this->view->usuarios = $usuarios;

		$this->render('quemSeguir');
	}	

	public function seguir() {

		$this->validaAutenticacao();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		if($acao == 'seguir') {
			$usuario->seguirUsuario($id_usuario_seguindo);

		} else if($acao == 'deixar_de_seguir') {
			$usuario->deixarSeguirUsuario($id_usuario_seguindo);
		}

		// Obtém a URL anterior
	    $url_anterior = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/quem_seguir';

	    // Redireciona para a URL anterior
	    header("Location: {$url_anterior}");
	}
}

?>