BEGIN;
INSERT INTO grupo (grupo, activo) VALUES ('dte_plus', true);
INSERT INTO auth (grupo, recurso) VALUES
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/documentos*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/admin/dte_folios*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/admin/firma_electronicas*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/contribuyentes*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_tmps*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_emitidos*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_recibidos*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_compras*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_ventas*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_intercambios*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/sii*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_guias*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/admin/respaldos*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/informes*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dashboard*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_boletas*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/dte_boleta_consumos*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/cobranzas*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/admin/item*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/api/dte/*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/boleta_honorarios*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/boleta_terceros*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/cesiones/*'),
    ((SELECT id FROM grupo WHERE grupo = 'dte_plus'), '/dte/registro_compras*')
;
COMMIT;
