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

namespace website;

/**
 * Obtiene los valores de la UF para un año determinado
 * Los valores disponibles en SII son desde 1990
 * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
 * @version 2020-02-15
 */
class Shell_Command_Indicadores_Uf extends \sowerphp\core\Shell_App
{

    /**
     * Método principal del comando
     * @param anio Año para el cual se desea obtener la UF o null para año actual
     * @return =0 si todo fue ok, !=0 si hubo algún error
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2020-02-15
     */
    public function main($anio = null, $save = null)
    {
        if ($anio!==null and !is_numeric($anio)) {
            $this->out('<error>Año debe ser un número entero</error>');
            return 1;
        }
        if ($save!==null and $save!='--save') {
            $this->out('<error>Segundo argumento del comando solo puede ser --save</error>');
            return 1;
        }
        if ($anio===null) $anio = date('Y');
        $this->out('Obteniendo valor de la UF para el año '.$anio);
        $response = libredte_api_consume('/sii/indicadores/uf/anual/'.$anio);
        if ($response['status']['code']!=200 or empty($response['body'])) {
            $msg = 'No fue posible obtener los valores de la UF para el año '.$anio;
            if ($response['body']) {
                $msg .= ': '.$response['body'];
            }
            $this->out('<error>'.$msg.'</error>');
            return 2;
        }
        if ($save=='--save') {
            $this->saveValores($response['body']);
        } else {
            $this->showValores($response['body']);
        }
        $this->showStats();
    }

    /**
     * Método que muestra los valores de un año de UF
     * @param $valores Arreglo con los valores de la UF de todos los meses de uno o varios años
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2020-02-15
     */
    private function showValores($valores)
    {
        // crear arreglo con dimensiones: año, día y mes (para poder imprimir)
        $aux = [];
        foreach ($valores as $anio => $meses) {
            foreach ($meses as $mes => $dias) {
                foreach ($dias as $dia => $valor) {
                    $aux[$anio][$dia][$mes] = $valor;
                }
            }
        }
        unset($valores);
        // crear tabla con valores
        foreach ($aux as $anio => $dias) {
            $this->out(str_repeat('=',134));
            $this->out('Valores de UF para el año '.$anio);
            $this->out(str_repeat('=',134));
            $this->out('   ',0);
            foreach (range(1,12) as $mes) {
                $this->out(sprintf('%10s ', $mes), 0);
            }
            $this->out();
            $this->out(str_repeat('-',134));
            foreach ($dias as $dia => $meses) {
                $this->out(sprintf('%2s ', $dia), 0);
                foreach ($meses as $mes => $valor) {
                    if ($valor) {
                        $valor = num($valor, 2);
                    }
                    $this->out(sprintf('%10s ', $valor), 0);
                }
                $this->out();
            }
            $this->out(str_repeat('=',134));
        }
    }

    /**
     * Método que guarda los valores de la UF en la base de datos
     * @param $valores Arreglo con los valores de la UF de todos los meses de uno o varios años
     * @author Esteban De La Fuente Rubio, DeLaF (esteban[at]sasco.cl)
     * @version 2020-09-06
     */
    private function saveValores($valores)
    {
        foreach ($valores as $anio => $meses) {
            foreach ($meses as $mes => $dias) {
                foreach ($dias as $dia => $valor) {
                    $aux[$anio][$dia][$mes] = $valor;
                    if (!$valor) {
                        continue;
                    }
                    $fecha = $anio.'-'.$mes.'-'.$dia;
                    $Moneda = new \sowerphp\app\Sistema\General\Model_MonedaCambio('CLF', 'CLP', $fecha);
                    $Moneda->valor = $valor;
                    $Moneda->save();
                }
            }
        }
    }

}
