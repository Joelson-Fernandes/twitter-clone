<?php

    namespace App\Models;
    use MF\Model\Model;

    class Usuario extends Model {
        private $id;
        private $nome;
        private $senha;
        private $email;

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
           // return $this->$atributo;
        }

        //salvar
        public function salvar() {
            $query = "INSERT INTO usuarios(nome, email, senha) 
                      VALUES (:usuario, :email, :senha)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':usuario', $this->__get('nome'));
            $stmt->bindValue(':email', $this->__get('email')); //md5 -> hash 32 caracteres
            $stmt->bindValue(':senha', $this->__get('senha'));
           
            $stmt->execute();

            return $this;
        }

        //validar se um cadastro pode ser feiro
        public function validarCadastro() {
            $valido = true;

            if (strlen($this->__get('nome') < 3)) {
                $valido = false;
            }
            
            if(strlen($this->__get('email') < 3)) {
                $valido = false;
            }

            if(strlen($this->__get('senha') < 3)) {
                $valido = false;
            }

            return $valido;
        }

        //recuperar usuario por email
        public function getUsuarioPorEmail() {
          $query = 'SELECT nome, email
                    FROM usuarios 
                    WHERE email = :email';
          $stmt = $this->db->prepare($query);
          $stmt->bindValue(':email', $this->__get('email'));
          $stmt->execute();

          return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function autenticar() {
            $query = 'SELECT id, nome, email 
                      FROM usuarios 
                      WHERE email = :email AND senha = :senha';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha'));
            $stmt->execute();

            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            
            if(!empty($usuario['id']) && !empty($usuario['nome'])) {
                $this->__set('id', $usuario['id']);
                $this->__set('nome', $usuario['nome']);
            }

            return $this;
          }

        public function getAll() {         
            $query = 'SELECT u.id, u.nome, u.email, 
                    (
                        SELECT COUNT(*)
                        FROM usuarios_seguidores AS us
                        WHERE us.id_usuario = :id_usuario AND us.id_usuario_seguindo = u.id
                    ) AS seguindo_sn
                      FROM usuarios AS u 
                      WHERE u.nome LIKE :nome AND u.id != :id_usuario';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
            $stmt->bindValue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return  $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
        }

          //informaçoes do usuario
        public function getInfoUsuario() {
            $query = "SELECT u.nome
                      FROM usuarios AS u 
                      WHERE u.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return  $stmt->fetch(\PDO::FETCH_ASSOC);

        }

          //total de tweets
        public function getTotalTweets() {
            $query = "SELECT COUNT(*) AS total_tweets
                      FROM tweets 
                      WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }

          //total de seguidores
          public function getTotalSeguidores() {
            $query = "SELECT COUNT(*) AS total_seguidores
                      FROM usuarios_seguidores 
                      WHERE id_usuario_seguindo = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }
 
          //total seguindo
        public function getTotalSeguindo() {
            $query = "SELECT COUNT(*) AS total_seguindo
                      FROM usuarios_seguidores 
                      WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }

    }
    
?>