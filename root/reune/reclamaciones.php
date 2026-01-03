<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema REUNE - Captura L13</title>
    
    <script>
        if (!sessionStorage.getItem('sessionToken') || !sessionStorage.getItem('userData')) {
            window.location.href = '../index.html';
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-bg: #f8fafc;
            --sidebar-color: #1e293b;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --text-main: #334155;
        }

        body { background-color: var(--primary-bg); font-family: 'Inter', sans-serif; color: var(--text-main); }
        .app-header { background: var(--sidebar-color); color: white; padding: 1.5rem; border-bottom: 4px solid var(--accent-color); margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; }
        .btn-regresar { color: #cbd5e1; text-decoration: none; font-weight: 500; display: flex; align-items: center; transition: 0.2s; }
        .btn-regresar:hover { color: white; transform: translateX(-3px); }
        .form-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); margin-bottom: 1.5rem; padding: 2rem; border: none; }
        .section-header { display: flex; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; }
        .section-icon { width: 32px; height: 32px; background: #eff6ff; color: var(--accent-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-weight: bold; }
        .section-title { font-size: 1rem; font-weight: 700; color: var(--sidebar-color); margin: 0; text-transform: uppercase; }
        .form-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 0.5rem; }
        .form-control, .form-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem; font-size: 0.9rem; }
        .readonly-field { background-color: #f1f5f9 !important; font-weight: 600; }
        .btn-enviar { background-color: var(--success-color); border: none; color: white; padding: 1rem 2rem; font-weight: 700; border-radius: 10px; transition: 0.2s; }
        .btn-enviar:hover { background-color: #059669; transform: translateY(-2px); }
        .cp-highlight { background: #f0f9ff; border: 1px dashed var(--accent-color); }
    </style>
</head>
<body>

<header class="app-header shadow">
    <a href="../index.html" class="btn-regresar">← Panel Principal</a>
    <div class="text-center">
        <h4 class="mb-0 fw-bold">Registro de Reclamaciones</h4>
        <span class="badge bg-primary" id="badge-entidad">Cargando Entidad...</span>
    </div>
    <div style="width: 120px"></div>
</header>

<div class="container pb-5">
    <form id="formReclamacion">
        <input type="hidden" name="INSTITUCION_NOMBRE" id="inst_nombre">
        <input type="hidden" name="INSTITUCION_SECTOR_TEXTO" id="inst_sector">
        
        <input type="hidden" name="modulo" value="reclamacion">
        <input type="hidden" name="trimestre" value="1">
        <input type="hidden" name="pori" value="NO">
        <input type="hidden" name="estado_id" id="id_estado">
        <input type="hidden" name="municipio_id" id="id_mun">
        <input type="hidden" name="localidad_id" id="id_loc">

        <div class="form-card">
            <div class="section-header">
                <div class="section-icon">01</div>
                <h5 class="section-title">Control y Trazabilidad</h5>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <label class="form-label">Folio Interno</label>
                    <input type="text" name="folio" class="form-control" placeholder="Ej: CSSJ-2026-001" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Folio Condusef</label>
                    <input type="text" name="folio_condusef" class="form-control" placeholder="Opcional">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Recepción</label>
                    <input type="date" name="fecha_recepcion" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Resolución</label>
                    <input type="date" name="fecha_resolucion" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Notificación</label>
                    <input type="date" name="fecha_notificacion" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="section-header">
                <div class="section-icon">02</div>
                <h5 class="section-title">Clasificación de la Queja</h5>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <label class="form-label">Operación</label>
                    <select id="sel_operacion" class="form-select" required></select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Producto</label>
                    <select id="sel_producto" name="producto_id" class="form-select" required><option value="">Seleccione...</option></select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subproducto</label>
                    <select id="sel_subproducto" class="form-select" required><option value="">Seleccione...</option></select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Causa</label>
                    <select id="sel_causa" name="causa_id" class="form-select" required><option value="">Seleccione...</option></select>
                </div>
            </div>
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <label class="form-label">Sentido de Resolución</label>
                    <select name="sentido_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="1">Totalmente favorable</option>
                        <option value="2">Desfavorable</option>
                        <option value="3">Parcialmente favorable</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nivel de Atención</label>
                    <select id="sel_nivel" name="nivel_id" class="form-select" required></select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Medio de Recepción</label>
                    <select id="sel_medio" name="medio_id" class="form-select" required></select>
                </div>
            </div>
        </div>

        <div class="form-card cp-highlight">
            <div class="section-header">
                <div class="section-icon">03</div>
                <h5 class="section-title">Datos Geográficos</h5>
            </div>
            <div class="row g-4 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Cód. Postal</label>
                    <div class="input-group">
                        <input type="text" id="txt_cp" name="cp_busqueda" class="form-control" maxlength="5">
                        <button class="btn btn-primary" type="button" id="btn_buscar_cp">🔎</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <input type="text" id="nom_estado" class="form-control readonly-field" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Municipio</label>
                    <input type="text" id="nom_mun" class="form-control readonly-field" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Colonia (Localidad)</label>
                    <select id="sel_colonia" name="colonia_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="section-header">
                <div class="section-icon">04</div>
                <h5 class="section-title">Perfil e Importes</h5>
            </div>
            <div class="row g-4">
                <div class="col-md-2">
                    <label class="form-label">Persona</label>
                    <select name="tipo_persona" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="1">Física</option>
                        <option value="2">Moral</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="H">Hombre</option>
                        <option value="M">Mujer</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Edad</label>
                    <input type="number" name="edad" class="form-control" placeholder="Ej: 30" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">¿Es Monetario?</label>
                    <select name="monetario" id="sel_monetario" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="NO">No, Informativa</option>
                        <option value="SI">Sí, Reclama Importe</option>
                    </select>
                </div>
                <div class="col-md-3" id="wrapper_monto" style="display: none;">
                    <label class="form-label">Monto Reclamado</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="monto" id="input_monto" class="form-control" value="0.00">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-enviar px-5 shadow-lg" id="btn_enviar">
                GENERAR Y ENVIAR JSON L13
            </button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Inicialización de Sesión
    const userData = JSON.parse(sessionStorage.getItem('userData'));
    if (userData) {
        $('#inst_nombre').val(userData.empresa);
        $('#inst_sector').val(userData.sector);
        $('#badge-entidad').text(userData.empresa);
    }

    const URL_CAT = '../php/get_catalogos.php';
    const URL_SEP = '../php/get_sepomex.php';
    const URL_SEND = '../php/send_reclamacion.php';

    // 2. Comportamiento de Monto
    $('#sel_monetario').change(function() {
        if ($(this).val() === 'SI') {
            $('#wrapper_monto').fadeIn();
        } else {
            $('#wrapper_monto').fadeOut();
            $('#input_monto').val('0.00');
        }
    });

    // 3. Catálogos
    cargarCombo('#sel_operacion', 'operaciones');
    cargarCombo('#sel_medio', 'medios');
    cargarCombo('#sel_nivel', 'niveles');

    $('#sel_operacion').change(function() { 
        $('#sel_producto, #sel_subproducto, #sel_causa').empty().append('<option value="">Seleccione...</option>');
        if($(this).val()) cargarCombo('#sel_producto', 'productos', $(this).val()); 
    });
    $('#sel_producto').change(function() { 
        $('#sel_subproducto, #sel_causa').empty().append('<option value="">Seleccione...</option>');
        if($(this).val()) cargarCombo('#sel_subproducto', 'subproductos', $(this).val()); 
    });
    $('#sel_subproducto').change(function() { 
        $('#sel_causa').empty().append('<option value="">Seleccione...</option>');
        if($(this).val()) cargarCombo('#sel_causa', 'causas', $(this).val()); 
    });

    // 4. SEPOMEX
    $('#btn_buscar_cp').click(function() {
        let cp = $('#txt_cp').val();
        if(!cp) return;
        let $btn = $(this).prop('disabled', true).text('...');
        
        $.getJSON(URL_SEP, { cp: cp }, function(data) {
            if(data.id_estado) {
                $('#id_estado').val(data.id_estado);
                $('#id_mun').val(data.id_municipio);
                $('#id_loc').val(data.id_localidad || data.id_municipio);
                $('#nom_estado').val(data.estado);
                $('#nom_mun').val(data.municipio);
                let $s = $('#sel_colonia').empty().append('<option value="">Seleccione Colonia...</option>');
                data.colonias.forEach(c => $s.append($('<option>', { value: c.id_colonia || c.id_loc, text: c.nombre })));
            } else { Swal.fire('Error', 'CP no encontrado', 'error'); }
        }).always(() => $btn.prop('disabled', false).text('🔎'));
    });

    // 5. Envío con SweetAlert2
    $('#formReclamacion').submit(function(e) {
        e.preventDefault();
        const btn = $('#btn_enviar').prop('disabled', true);
        
        Swal.fire({
            title: 'Procesando Envío',
            text: 'Generando archivo JSON L13...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.post(URL_SEND, $(this).serialize(), function(res) {
            if(res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Reporte Enviado!',
                    text: res.message
                }).then(() => { window.location.href = "../index.html"; });
            } else {
                Swal.fire('Error', res.message, 'error');
                btn.prop('disabled', false);
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'Fallo de conexión con el servidor', 'error');
            btn.prop('disabled', false);
        });
    });

    function cargarCombo(sel, tipo, pId = null) {
        $.getJSON(URL_CAT, { tipo: tipo, parentId: pId }, function(data) {
            let $s = $(sel).empty().append('<option value="">Seleccione...</option>');
            $.each(data, (i, item) => $s.append($('<option>', { value: item.id || item, text: item.nombre || item })));
        });
    }
});
</script>
</body>
</html>