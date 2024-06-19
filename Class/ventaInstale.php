<?php
//error_reporting(0);
require_once 'db.php';

class ventaInstale
{

    private $_DB;

    public function __construct()
    {
        $this->_DB = new DB;
    }

    public function guardaPedidoVentaInstale($data)
    {
        $fecha = explode('T', $data['fecha_atencion']);
        $hoy = date('Y-m-d H:i:s');
        $hora_minima = date('Y-m-d 06:00:00');
        $hora_maxima = date('Y-m-d 21:00:00');

        try {

            if ($hoy >= $hora_minima && $hoy <= $hora_maxima) {
                $stmt = $this->_DB->seguimiento()->prepare("SELECT
                                                                        *
                                                                    FROM
                                                                        ventasInstaleTiendas
                                                                    WHERE
                                                                        pedido = :pedido
                                                                    AND en_gestion != 2
                                                                    ");
                $stmt->execute(array(':pedido' => $data['pedido']));
                if ($stmt->rowCount() > 0) {
                    $data = array('state' => 0, 'msj' => 'El pedido ingresado ya tiene una solicitud en gestión.');
                } else {

                    if ($data['jornada_atencion'] == 'MASIVO') {
                        $stmt = $this->_DB->seguimiento()->prepare("INSERT INTO ventasInstaleTiendas (fecha_atencion,jornada_atencion, pedido,documento_cliente,numero_contacto_cliente,login_despacho,observacion_canal,regional,documento_tecnico,nombre_tecnico,categoria, hora_ingreso, fecha_ingreso, tipificacion, obs_tipificacion, fecha_gestion, observacion_gestion, login_gestion, en_gestion)
        VALUES (:fecha,:jornada_atencion,:pedido,:documento_cliente,:numero_contacto_cliente,:login_despacho,:observacion_canal,:regional, :documento_tecnico, :nombre_tecnico,:categoria, :hora_ingreso, :fecha_ingreso, :tipificacion, :obs_tipificacion, :fecha_gestion, :observacion_gestion, :login_gestion, :en_gestion)");
                        $stmt->execute(
                            array(
                                ':fecha' => $fecha[0],
                                ':jornada_atencion' => $data['jornada_atencion'],
                                ':pedido' => $data['pedido'],
                                ':documento_cliente' => $data['documento_cliente'],
                                ':numero_contacto_cliente' => $data['contacto_cliente'],
                                ':login_despacho' => $data['login_despacho'],
                                ':observacion_canal' => $data['observacion_canal'],
                                ':regional' => $data['regional'],
                                ':documento_tecnico' => $data['documento_tecnico'],
                                ':nombre_tecnico' => $data['nombre_tecnico'],
                                ':categoria' => $data['categoria'],
                                ':hora_ingreso' => date('H:i:s'),
                                ':fecha_ingreso' => date('Y-m-d H:i:s'),
                                ':tipificacion' => "masivo",
                                ':obs_tipificacion' => "masivo",
                                ':fecha_gestion' => date('Y-m-d H:i:s'),
                                ':observacion_gestion' => "masivo",
                                ':login_gestion' => "masivo",
                                ':en_gestion' => 2
                            )
                        );

                        if ($stmt->rowCount() == 1) {
                            $data = array('state' => 1, 'msj' => 'La solicitud se realizo exitosamente');
                        } else {
                            $data = array('state' => 0, 'msj' => 'Ha ocurrido un error interno intentalo nuevamente en unos minutos');
                        }
                    } else {
                        $stmt = $this->_DB->seguimiento()->prepare("INSERT INTO ventasInstaleTiendas (fecha_atencion,jornada_atencion, pedido,documento_cliente,numero_contacto_cliente,login_despacho,observacion_canal,regional,documento_tecnico,nombre_tecnico,categoria, hora_ingreso, fecha_ingreso)
        VALUES (:fecha,:jornada_atencion,:pedido,:documento_cliente,:numero_contacto_cliente,:login_despacho,:observacion_canal,:regional, :documento_tecnico, :nombre_tecnico,:categoria, :hora_ingreso, :fecha_ingreso)");
                        $stmt->execute(
                            array(
                                ':fecha' => $fecha[0],
                                ':jornada_atencion' => $data['jornada_atencion'],
                                ':pedido' => $data['pedido'],
                                ':documento_cliente' => $data['documento_cliente'],
                                ':numero_contacto_cliente' => $data['contacto_cliente'],
                                ':login_despacho' => $data['login_despacho'],
                                ':observacion_canal' => $data['observacion_canal'],
                                ':regional' => $data['regional'],
                                ':documento_tecnico' => $data['documento_tecnico'],
                                ':nombre_tecnico' => $data['nombre_tecnico'],
                                ':categoria' => $data['categoria'],
                                ':hora_ingreso' => date('H:i:s'),
                                ':fecha_ingreso' => date('Y-m-d H:i:s')

                            )
                        );

                        if ($stmt->rowCount() == 1) {
                            $data = array('state' => 1, 'msj' => 'La solicitud se realizo exitosamente');
                        } else {
                            $data = array('state' => 0, 'msj' => 'Ha ocurrido un error interno intentalo nuevamente en unos minutos');
                        }
                    }
                }
            } else {
                $data = array('state' => 0, 'msj' => 'El horario para el ingreso de solicitudes es de 6:00 am y 9:00 pm');
            }
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        $this->_BD = null;
        echo json_encode($data);
    }

    public function respuestasPedidos($data)
    {

        try {
            $condicion = "";
            if ($data['pedido'] != 0) {
                $pedido = $data['pedido'];
                $condicion = " AND pedido LIKE '%$pedido%' ";
            }

            $pagenum = $data['pageNumber'];
            $pagesize = $data['pageSize'];
            $offset = ($pagenum - 1) * $pagesize;

            $stmt = $this->_DB->seguimiento()->prepare("SELECT
                                                    *
                                                FROM
                                                    ventasInstaleTiendas
                                                WHERE login_despacho = :login_despacho");
            $stmt->execute(array(':login_despacho' => $data['login']));
            $count = $stmt->rowCount();

            $stmt = $this->_DB->seguimiento()->prepare("SELECT
                                                    pedido,
                                                    observacion_gestion,
                                                    tipificacion,
                                                    obs_tipificacion,
                                                    fecha_gestion,
                                                    fecha_ingreso
                                                FROM
                                                    ventasInstaleTiendas
                                                WHERE 1 = 1 $condicion
                                                    AND login_despacho = :login_despacho 
                                                ORDER BY id DESC    
                                                LIMIT $offset, $pagesize
                                                ");
            $stmt->execute(array(':login_despacho' => $data['login']));

            if ($stmt->rowCount() > 0) {
                $data = array('state' => 1, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'count' => $count);
            } else {
                $data = array('state' => 0, 'msj' => 'No se encontraron registros');
            }
        } catch (PDOException $e) {
            var_dump($e);
        }
        $this->_BD = null;
        echo json_encode($data);
    }

    public function documento_tecnico($data)
    {
        try {
            $stmt = $this->_DB->seguimiento()->prepare("SELECT
                                    nombre
                                FROM
                                    tecnicos
                                WHERE
                                    identificacion = :doc");
            $stmt->execute(array(':doc' => $data['documento_tecnico']));

            if ($stmt->rowCount() == 1) {
                $data = array('state' => 1, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            } else {
                $data = array('state' => 0, 'msj' => 'El documento ingresado no se encuentra registrado.');
            }
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        $this->_BD = '';
        echo json_encode($data);
    }

    public function observaciones()
    {
        try {
            $hora = date('Y-m-d');
            $stmt = $this->_DB->seguimiento()->prepare("SELECT * FROM observacion_venta_instale_despacho WHERE hora BETWEEN '$hora 00:00:00' and '$hora 23:59:59'");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $response = array('state' => 1, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            } else {
                $response = array('state' => 0, 'msj' => 'No se encontraron registros.');
            }
        } catch (PDOException $th) {
            var_dump($th->getMessage());
        }
        $this->_BD = '';
        echo json_encode($response);
    }

    public function export($data){
        try{
            $stmt = $this->_DB->seguimiento()->prepare("SELECT
                                                    pedido,
                                                    observacion_gestion,
                                                    tipificacion,
                                                    obs_tipificacion,
                                                    fecha_ingreso,
                                                    fecha_gestion
                                                    
                                                FROM
                                                    ventasInstaleTiendas
                                                WHERE login_despacho = :login_despacho 
                                                ORDER BY id DESC    
                                                ");
            $stmt->execute(array(':login_despacho' => $data['login']));
            if ($stmt->rowCount() > 0) {
                $data = array('state' => 1, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            } else {
                $data = array('state' => 0, 'msj' => 'No se encontraron registros');
            }
        } catch (PDOException $th) {
            var_dump($th->getMessage());
        }

        $this->_BD = '';
        echo json_encode($data);
    }
}