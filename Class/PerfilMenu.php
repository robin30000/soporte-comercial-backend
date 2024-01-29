<?php
require_once 'db.php';

class PerfilMenu
{
    private $_DB;

    public function __construct()
    {
        $this->_DB = new DB;
    }

    public function listMenu()
    {
        try {
            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT * FROM menu");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function listPerfil()
    {
        try {
            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT * FROM perfil");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function editaMenu($data)
    {
        try {
            if ($data['estado'] === 'Activo') {
                $estado = 'Inactivo';
            } else {
                $estado = 'Activo';
            }

            $stmt = $this->_DB->SeguimientoPedidos()->prepare("UPDATE menu SET estado = :estado WHERE id = :id");
            $stmt->execute(array(':estado' => $estado, ':id' => $data['menu']));

            if ($stmt->rowCount() === 1) {
                $data = array('state' => true, 'msj' => 'El menu ahora se encuentra ' . $estado);
            } else {
                $data = array('state' => false, 'msj' => 'Ha ocurrido un error interno, intentalo nuevamente en unos minutos.');
            }

            return $data;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function editaPerfil($data)
    {
        try {
            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT
                                                                        pe.perfil,
                                                                        mp.perfil_id,
                                                                        mp.menu_id,
                                                                        me.menu,
                                                                        mp.estado 
                                                                    FROM
                                                                        menu_perfil mp
                                                                        INNER JOIN menu me ON mp.menu_id = me.id
                                                                        INNER JOIN perfil pe ON pe.id = mp.perfil_id 
                                                                    WHERE
                                                                        mp.perfil_id = :id");
            $stmt->execute(array(':id' => $data['perfil']));

            if ($stmt->rowCount()) {
                $data = array('state' => true, 'msj' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            } else {
                $data = array('state' => false, 'msj' => 'Ha ocurrido un error interno, intentalo nuevamente en unos minutos.');
            }

            return $data;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function cambiaEstadoMenu($data)
    {
        try {
            $estado = $data['estado'] === 'Inactivo' ? 'activo' : 'Inactivo';
            $stmt = $this->_DB->SeguimientoPedidos()->prepare("UPDATE menu_perfil 
                                                                        SET estado = :estado 
                                                                        WHERE
                                                                            perfil_id = :perfil
                                                                            AND menu_id = :menu");
            $stmt->execute(array(':estado' => $estado, ':perfil' => $data['perfil_id'], ':menu' => $data['menu_id']));

            if ($stmt->rowCount()) {
                $data = array('state' => true, 'msj' => 'El menu ahora se encuentra ' . $estado);
            } else {
                $data = array('state' => false, 'msj' => 'Ha ocurrido un error interno, intentalo nuevamente en unos minutos.');
            }

            return $data;

        } catch (PDOException $e) {
            var_dump($e);
        }
    }
}