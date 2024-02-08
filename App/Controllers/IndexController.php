<?php

namespace App\Controllers;

//recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->render('index');
	}

	public function inscreverse() {

		$this->view->usuario = array(
				'nome' => '',
				'email' => '',
				'senha' => '',
			);

		$this->view->erroCadastro = false;

		$this->render('inscreverse');
	}

	public function registrar() {


		 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obter os dados do formulário
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $confirmarSenha = $_POST['confirmar_senha'];

        // Verificar se a senha e a confirmação de senha coincidem
        if ($senha !== $confirmarSenha) {
            // Senha e confirmação de senha não coincidem, exibir mensagem de erro
            $this->view->erroCadastro = true;
            // Redirecionar de volta ao formulário com a mensagem de erro
            $this->render('inscreverse');
            return; // Encerrar a execução do método para evitar o processamento adicional do formulário
        }



			$usuario = Container::getModel('Usuario');

			$usuario->__set('nome', $_POST['nome']);
			$usuario->__set('email', $_POST['email']);
			$usuario->__set('senha', md5($_POST['senha']));

			
			if($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) {
			
					$usuario->salvar();

					$this->render('cadastro');

			} else {

				$this->view->usuario = array(
					'nome' => $_POST['nome'],
					'email' => $_POST['email'],
					'senha' => $_POST['senha'],
				);

				$this->view->erroCadastro = true;

				$this->render('inscreverse');
			}

		} else {
        	  header('Location: /inscreverse?mensagem=As senhas fornecidas não coincidem. Por favor, tente novamente.');
    	}

	}

}


?>