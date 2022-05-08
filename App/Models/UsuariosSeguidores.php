<?php

    namespace App\Models;
    use MF\Model\Model;

    class UsuariosSeguidores extends Model {
        private $id;
        private $id_usuario;
        private $id_usuario_seguindo;

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
           // return $this->$atributo;
        }

        public function seguirUsuario($usuario) {
            $query = "INSERT INTO usuarios_seguidores(id_usuario, id_usuario_seguindo)
                      VALUES (:usuario, :usuario_seguindo)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':usuario_seguindo', $usuario); 
            $stmt->execute();
             
            return true;
        }

        public function deixarDeSeguir($usuario) {
            $query = "DELETE FROM usuarios_seguidores 
                      WHERE id_usuario = :usuario AND id_usuario_seguindo = :usuario_seguindo";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':usuario', $this->__get('id_usuario'));
            $stmt->bindValue(':usuario_seguindo', $usuario); 
            $stmt->execute();
                
            return true;
        }

   
    }
    


?>