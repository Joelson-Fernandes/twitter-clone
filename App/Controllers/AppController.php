<?php

    namespace App\Controllers;

    use MF\controller\Action;
    use MF\Model\Container;

    class AppController extends Action {

        public function timeLine() {

                $this->validaAutenticacao();
                $this->getInfoUsuario();

                $tweet = Container::getModel('Tweet');
                $tweet->__set('id_usuario', $_SESSION['id']);

                //variaveis de páginação
                $total_registros_pagina = 10;
                //$deslocamento = 0;
                $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
                $deslocamento = ($pagina - 1) * $total_registros_pagina;

                //$tweets = $tweet->getAll();
                $tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento);
                $total_tweets = $tweet->getTotalRegistros();

                $this->view->total_de_paginas = ceil($total_tweets['total'] / $total_registros_pagina);

                $this->view->tweets = $tweets;
                $this->view->pagina_ativa = $pagina;

                $this->render('timeline');
          
        }

        public function tweet() {

                $this->validaAutenticacao();

                $tweet = Container::getModel('Tweet');
            
                $tweet->__set('tweet', $_POST['tweet']);
                $tweet->__set('id_usuario', $_SESSION['id']);

                $tweet->salvar();

                header('Location: /timeline');

        }

        public function validaAutenticacao() {

            session_start();

            if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '' ) {
                header('Location: /?login=erro');
            } 

        }

        public function quemSeguir() {

            $this->validaAutenticacao();
            $this->getInfoUsuario();
            
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

        public function acao() {

            $this->validaAutenticacao();

            $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

            $usuario = Container::getModel('UsuariosSeguidores');
            $usuario->__set('id_usuario', $_SESSION['id']);

            if($acao == 'seguir') {
                $usuario->seguirUsuario($id_usuario);
            } else if($acao == 'deixar_de_seguir') {
                $usuario->deixarDeSeguir($id_usuario);
            }
        
            header ('Location: /quem_seguir');
        }

        public function removerTweet() {

            $this->validaAutenticacao();

            $tweet = Container::getModel('Tweet');
            $tweet->__set('id', $_GET['id_tweet']);
            $tweet->remover();

            header ('Location: /timeline');

        }

        public function getInfoUsuario() {
            $usuario = Container::getModel('Usuario');
            $usuario->__set('id', $_SESSION['id']);

            $this->view->usuario_info = $usuario->getInfoUsuario();
            $this->view->total_tweets = $usuario->getTotalTweets();
            $this->view->total_seguindo = $usuario->getTotalSeguindo();
            $this->view->total_seguidores = $usuario->getTotalSeguidores();

            return $this;
        }

    }

?>