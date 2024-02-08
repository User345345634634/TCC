<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = array(
			'route' => '/',
			'controller' => 'indexController',
			'action' => 'index'
		);

		$routes['inscreverse'] = array(
			'route' => '/inscreverse',
			'controller' => 'indexController',
			'action' => 'inscreverse'
		);

		$routes['registrar'] = array(
			'route' => '/registrar',
			'controller' => 'indexController',
			'action' => 'registrar'
		);

		$routes['autenticar'] = array(
			'route' => '/autenticar',
			'controller' => 'AuthController',
			'action' => 'autenticar'
		);

		$routes['timeline'] = array(
			'route' => '/timeline',
			'controller' => 'AppController',
			'action' => 'timeline'
		);

		$routes['perfil'] = array(
			'route' => '/perfil',
			'controller' => 'AppController',
			'action' => 'perfil'
		);

		$routes['navbar'] = array(
			'route' => '/navbar',
			'controller' => 'AppController',
			'action' => 'navbar'
		);

		$routes['sair'] = array(
			'route' => '/sair',
			'controller' => 'AuthController',
			'action' => 'sair'
		);

		$routes['tarefa'] = array(
			'route' => '/tarefa',
			'controller' => 'AppController',
			'action' => 'tarefa'
		);

		$routes['removetarefa'] = array(
			'route' => '/removetarefa',
			'controller' => 'AppController',
			'action' => 'removetarefa'
   		 );

		$routes['concluirtarefa'] = array(
			'route' => '/concluirtarefa',
			'controller' => 'AppController',
			'action' => 'concluirtarefa'
   		 );

		$routes['quem_seguir'] = array(
			'route' => '/quem_seguir',
			'controller' => 'AppController',
			'action' => 'quemSeguir'
		);

		$routes['acao'] = array(
			'route' => '/seguir',
			'controller' => 'AppController',
			'action' => 'seguir'
		);

		$this->setRoutes($routes);
	}

}

?>