<?php

/**
 * LibreDTE
 * Copyright (C) SASCO SpA (https://sasco.cl)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o
 * modificarlo bajo los términos de la Licencia Pública General Affero de GNU
 * publicada por la Fundación para el Software Libre, ya sea la versión
 * 3 de la Licencia, o (a su elección) cualquier versión posterior de la
 * misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General Affero de GNU para
 * obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

// namespace del controlador
namespace website\Dte\Informes;

/**
 * Clase para informes de los documentos recibidos
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2019-06-30
 */
class Controller_DteRecibidos extends \Controller_App
{

    /**
     * Acción principal del informe de compras
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2019-06-30
     */
    public function index()
    {
        $Receptor = $this->getContribuyente();
        $desde = isset($_POST['desde']) ? $_POST['desde'] : date('Y-m-01');
        $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : date('Y-m-d');
        $this->set([
            'Receptor' => $Receptor,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
        if (isset($_POST['submit'])) {
            $DteRecibidos = (new \website\Dte\Model_DteRecibidos())->setContribuyente($Receptor);
            $this->set([
                'por_tipo' => $DteRecibidos->getPorTipo($desde, $hasta),
                'por_dia' => $DteRecibidos->getPorDia($desde, $hasta),
                'por_sucursal' => $DteRecibidos->getPorSucursal($desde, $hasta),
                'por_usuario' => $DteRecibidos->getPorUsuario($desde, $hasta),
            ]);
        }
    }

    /**
     * Acción que entrega el informe de compras en CSV
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2021-05-17
     */
    public function csv($desde, $hasta)
    {
        extract($this->getQuery([
            'detalle' => false,
        ]));
        $Emisor = $this->getContribuyente();
        $cols = [
            'ID',
            'Documento',
            'Folio',
            'Fecha',
            'RUT',
            'Razón social',
            'Exento',
            'Neto',
            'IVA',
            'Total CLP',
            'Período',
            'Sucursal',
            'Usuario',
            'Acción RC',
            'Tipo transacción',
        ];
        if ($detalle) {
            $cols[] = 'Línea';
            $cols[] = 'Tipo Cód.';
            $cols[] = 'Código';
            $cols[] = 'Exento';
            $cols[] = 'Item';
            $cols[] = 'Cantidad';
            $cols[] = 'Unidad';
            $cols[] = 'Neto';
            $cols[] = 'Descuento %';
            $cols[] = 'Descuento $';
            $cols[] = 'Imp. Adic.';
            $cols[] = 'Subtotal';
        }
        $cols[] = 'No facturable';
        $cols[] = 'Monto período';
        $cols[] = 'Saldo anterior';
        $cols[] = 'Valor a pagar';
        $aux = (new \website\Dte\Model_DteRecibidos())->setContribuyente($Emisor)->getDetalle($desde, $hasta, $detalle);
        if ($aux and $detalle) {
            $recibidos = [];
            foreach($aux as $r) {
                foreach ($r['items'] as $item) {
                    if ($item[0]==1) {
                        $recibido = array_slice($r, 0, 15);
                    } else {
                        $recibido = array_fill(0, 15, '');
                    }
                    $recibido = array_merge($recibido, $item);
                    $recibidos[] = $recibido;
                }
            }
            unset($aux);
        } else {
            $recibidos = $aux;
        }
        array_unshift($recibidos, $cols);
        $csv = \sowerphp\general\Utility_Spreadsheet_CSV::get($recibidos);
        $this->response->sendContent($csv, 'recibidos_'.$Emisor->rut.'_'.$desde.'_'.$hasta.'.csv');
    }

    /**
     * Acción que
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2021-02-22
     */
    public function sin_xml()
    {
        $Receptor = $this->getContribuyente();
        $desde = isset($_POST['desde']) ? $_POST['desde'] : date('Y-m-01');
        $hasta = isset($_POST['hasta']) ? $_POST['hasta'] : date('Y-m-d');
        $this->set([
            'Receptor' => $Receptor,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
        if (isset($_POST['submit'])) {
            $DteRecibidos = (new \website\Dte\Model_DteRecibidos())->setContribuyente($Receptor);
            $this->set([
                'documentos' => $DteRecibidos->getDocumentosSinXML($desde, $hasta),
            ]);
        }
    }

}
