<style>
    #panelCiudadanosMonitoreo {
        color: #252A30;
    }

    #panelCiudadanosMonitoreo[hidden],
    #panelCiudadanosMonitoreo [hidden],
    .contador-identidad[hidden] {
        display: none !important;
    }

    .contador-identidad {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        margin-left: 8px;
        padding: 0 6px;
        border-radius: 20px;
        background: #F7E9EE;
        color: #691C32;
        font-size: 12px;
        font-weight: 700;
        box-sizing: border-box;
    }

    #menuCiudadanos .contador-identidad {
        background: #FFFFFF;
        margin-left: auto;
    }

    #menuCiudadanos {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    #panelCiudadanosMonitoreo .ciudadanos-pestanas {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
        border-bottom: 1px solid #E5DDE0;
    }

    #panelCiudadanosMonitoreo .ciudadanos-pestanas button {
        display: flex;
        align-items: center;
        min-height: 48px;
        padding: 10px 0;
        border: 0;
        border-bottom: 3px solid transparent;
        background: transparent;
        color: #62676C;
        font: inherit;
        font-size: 14px;
        cursor: pointer;
    }

    #panelCiudadanosMonitoreo .ciudadanos-pestanas button.activa {
        border-bottom-color: #9F2241;
        color: #691C32;
        font-weight: 700;
    }

    #panelCiudadanosMonitoreo .ciudadanos-panel {
        padding: 20px;
        border: 1px solid #E5DDE0;
        border-radius: 12px;
        background: #FFFFFF;
    }

    #panelCiudadanosMonitoreo .ciudadanos-filtros {
        display: flex;
        align-items: end;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    #panelCiudadanosMonitoreo .ciudadanos-campo {
        flex: 1 1 220px;
    }

    #panelCiudadanosMonitoreo .ciudadanos-campo label {
        display: block;
        margin-bottom: 7px;
        font-size: 13px;
        font-weight: 600;
    }

    #panelCiudadanosMonitoreo .ciudadanos-campo input,
    #panelCiudadanosMonitoreo .ciudadanos-campo select {
        width: 100%;
        height: 44px;
        padding: 10px 12px;
        border: 1px solid #D8D3D5;
        border-radius: 8px;
        background: #FFFFFF;
        color: #252A30;
        font: inherit;
        font-size: 14px;
        box-sizing: border-box;
    }

    #panelCiudadanosMonitoreo .ciudadanos-boton {
        min-height: 40px;
        padding: 10px 16px;
        border: 1px solid #691C32;
        border-radius: 8px;
        background: #691C32;
        color: #FFFFFF;
        font: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    #panelCiudadanosMonitoreo .ciudadanos-boton-secundario {
        background: #FFFFFF;
        color: #691C32;
    }

    #panelCiudadanosMonitoreo .ciudadanos-boton:hover:not(:disabled) {
        background: #9F2241;
        color: #FFFFFF;
    }

    #panelCiudadanosMonitoreo .ciudadanos-boton:disabled {
        opacity: 0.5;
        cursor: default;
    }

    #panelCiudadanosMonitoreo .ciudadanos-tabla-contenedor {
        overflow-x: auto;
    }

    #panelCiudadanosMonitoreo .ciudadanos-tabla {
        width: 100%;
        min-width: 680px;
        border-collapse: collapse;
        font-size: 13px;
    }

    #panelCiudadanosMonitoreo .ciudadanos-tabla th,
    #panelCiudadanosMonitoreo .ciudadanos-tabla td {
        padding: 14px 12px;
        border-bottom: 1px solid #E5DDE0;
        text-align: left;
        vertical-align: middle;
    }

    #panelCiudadanosMonitoreo .ciudadanos-tabla th {
        background: #FAF8F9;
        color: #62676C;
        font-size: 12px;
        font-weight: 600;
    }

    #panelCiudadanosMonitoreo .ciudadanos-paginacion {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
        font-size: 13px;
        color: #62676C;
    }

    #panelCiudadanosMonitoreo .ciudadanos-paginacion-acciones {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #panelCiudadanosMonitoreo .ciudadanos-mensaje {
        font-size: 13px;
        line-height: 1.5;
    }

    #panelCiudadanosMonitoreo .ciudadanos-mensaje:empty {
        display: none;
    }

    #panelCiudadanosMonitoreo #panelVerificacionIdentidad .titulo-pagina {
        display: none;
    }

    #fichaCiudadanoMonitoreo {
        width: min(560px, calc(100% - 32px));
        max-height: 85vh;
        padding: 24px;
        overflow-y: auto;
        border: 1px solid #E5DDE0;
        border-radius: 12px;
        background: #FFFFFF;
        color: #252A30;
        box-sizing: border-box;
    }

    #fichaCiudadanoMonitoreo::backdrop {
        background: rgba(0, 0, 0, 0.4);
    }

    #fichaCiudadanoMonitoreo .ficha-ciudadano-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    #fichaCiudadanoMonitoreo h3 {
        margin: 0;
        font-size: 20px;
    }

    #fichaCiudadanoMonitoreo dl {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin: 0 0 20px;
    }

    #fichaCiudadanoMonitoreo dt {
        color: #62676C;
        font-size: 12px;
    }

    #fichaCiudadanoMonitoreo dd {
        margin: 5px 0 0;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    #fichaCiudadanoMonitoreo .ficha-ciudadano-ancho {
        grid-column: 1 / -1;
    }
</style>

<section id="panelCiudadanosMonitoreo" hidden>


    <nav class="ciudadanos-pestanas" aria-label="Apartados de ciudadanos">
        <button
            id="btnListadoCiudadanos"
            type="button"
            class="activa"
            aria-pressed="true"
            onclick="mostrarSeccionMonitoreo('ciudadanos')">
            Ciudadanos registrados
        </button>

        <button
            id="menuVerificacionIdentidad"
            type="button"
            aria-pressed="false"
            onclick="mostrarSeccionMonitoreo('identidad')">
            Verificación de identidad
            <span class="contador-identidad" data-contador-identidad hidden></span>
        </button>
    </nav>

    <div id="vistaListadoCiudadanos" class="ciudadanos-panel">
        <form
            id="formBuscarCiudadanos"
            class="ciudadanos-filtros"
            onsubmit="buscarCiudadanos(event)">

            <div class="ciudadanos-campo">
                <label for="buscarCurpCiudadano">Buscar por CURP</label>
                <input
                    id="buscarCurpCiudadano"
                    type="text"
                    maxlength="18"
                    placeholder="Escribe los 18 caracteres"
                    autocomplete="off">
            </div>

            <div class="ciudadanos-campo">
                <label for="filtroIdentidadCiudadano">Verificación de identidad</label>
                <select id="filtroIdentidadCiudadano">
                    <option value="">Todas</option>
                    <option value="PENDIENTE">Pendiente</option>
                    <option value="VERIFICADA">Verificada</option>
                    <option value="RECHAZADA">Rechazada</option>
                </select>
            </div>

            <button
                id="btnBuscarCiudadanos"
                type="submit"
                class="ciudadanos-boton">
                Buscar
            </button>

        </form>

        <p
            id="mensajeListadoCiudadanos"
            class="ciudadanos-mensaje"
            role="status"></p>

        <div class="ciudadanos-tabla-contenedor">
            <table class="ciudadanos-tabla" aria-label="Ciudadanos registrados">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">CURP</th>
                        <th scope="col">Cuenta</th>
                        <th scope="col">Identidad</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaCiudadanosMonitoreo">
                    <tr>
                        <td colspan="5">El listado se cargará al abrir esta sección.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="ciudadanos-paginacion">
            <span id="totalListadoCiudadanos"></span>

            <button
                id="btnVerMasCiudadanos"
                type="button"
                class="ciudadanos-boton"
                onclick="verMasCiudadanos()"
                hidden>
                Ver más
            </button>
        </div>
    </div>

    @include('monitoreo.verificacion-identidad')

    <dialog
        id="fichaCiudadanoMonitoreo"
        aria-labelledby="tituloFichaCiudadano">

        <div class="ficha-ciudadano-cabecera">
            <h3 id="tituloFichaCiudadano">Ficha del ciudadano</h3>

            <button
                type="button"
                class="ciudadanos-boton ciudadanos-boton-secundario"
                onclick="cerrarFichaCiudadano()">
                Cerrar
            </button>
        </div>

        <p id="mensajeFichaCiudadano" class="ciudadanos-mensaje" role="status"></p>

        <dl id="datosFichaCiudadano" hidden>
            <div class="ficha-ciudadano-ancho">
                <dt>Nombre completo</dt>
                <dd id="fichaCiudadanoNombre"></dd>
            </div>

            <div>
                <dt>CURP</dt>
                <dd id="fichaCiudadanoCurp"></dd>
            </div>

            <div>
                <dt>Teléfono</dt>
                <dd id="fichaCiudadanoTelefono"></dd>
            </div>

            <div class="ficha-ciudadano-ancho">
                <dt>Correo electrónico</dt>
                <dd id="fichaCiudadanoCorreo"></dd>
            </div>

            <div>
                <dt>Estado de la cuenta</dt>
                <dd id="fichaCiudadanoEstatus"></dd>
            </div>

            <div>
                <dt>Verificación de identidad</dt>
                <dd id="fichaCiudadanoIdentidad"></dd>
            </div>

            <div>
                <dt>Fecha de registro</dt>
                <dd id="fichaCiudadanoRegistro"></dd>
            </div>

            <div>
                <dt>Identidad verificada el</dt>
                <dd id="fichaCiudadanoVerificada"></dd>
            </div>
        </dl>

        <button
            id="btnEditarCiudadano"
            type="button"
            class="ciudadanos-boton ciudadanos-boton-secundario"
            onclick="mostrarEdicionCiudadano()"
            hidden>
            Editar datos
        </button>

        <form
            id="formEditarCiudadano"
            onsubmit="guardarEdicionCiudadano(event)"
            hidden>
            <div class="ciudadanos-campo">
                <label for="editarNombreCiudadano">Nombre completo</label>
                <input
                    id="editarNombreCiudadano"
                    type="text"
                    maxlength="100"
                    required>
            </div>

            <div class="ciudadanos-campo">
                <label for="editarTelefonoCiudadano">Teléfono</label>
                <input
                    id="editarTelefonoCiudadano"
                    type="tel"
                    inputmode="numeric"
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    required>
            </div>

            <button
                id="btnGuardarCiudadano"
                type="submit"
                class="ciudadanos-boton">
                Guardar cambios
            </button>

            <button
                type="button"
                class="ciudadanos-boton ciudadanos-boton-secundario"
                onclick="cancelarEdicionCiudadano()">
                Cancelar
            </button>
        </form>

        <p
            id="motivoRechazoFichaCiudadano"
            class="ciudadanos-mensaje"
            hidden></p>

        <button
            id="btnRevisarIdentidadCiudadano"
            type="button"
            class="ciudadanos-boton"
            onclick="abrirPendientesDesdeFicha()"
            hidden>
            Ver verificaciones pendientes
        </button>
    </dialog>
</section>

<script>
    let consultaListadoCiudadanos = null;
    let paginaCiudadanos = 1;
    let ultimaPaginaCiudadanos = 1;
    let filtrosCiudadanos = {
        curp: '',
        verificacion: ''
    };

    function textoIdentidadCiudadano(ciudadano) {
        if (!ciudadano.tiene_foto_ine) return 'Sin foto del INE';

        const estados = {
            PENDIENTE: 'Pendiente de revisión',
            VERIFICADA: 'Verificada',
            RECHAZADA: 'Foto rechazada'
        };

        return estados[ciudadano.estatus_verificacion] ?? 'Sin información';
    }

    async function cargarCiudadanos(pagina = paginaCiudadanos, silenciosa = false) {
        if (!tokenMonitoreo) return;

        consultaListadoCiudadanos?.abort();

        const consulta = new AbortController();
        consultaListadoCiudadanos = consulta;

        const tabla = document.getElementById('tablaCiudadanosMonitoreo');
        const mensaje = document.getElementById('mensajeListadoCiudadanos');
        const buscar = document.getElementById('btnBuscarCiudadanos');
        const verMas = document.getElementById('btnVerMasCiudadanos');

        const cantidadSolicitada = pagina * 20;
        const porPagina = Math.min(cantidadSolicitada, 100);
        const filtros = {
            ...filtrosCiudadanos
        };

        buscar.disabled = true;
        verMas.disabled = true;
        tabla.setAttribute('aria-busy', 'true');

        if (!silenciosa) {
            mensaje.style.color = '#62676C';
            mensaje.textContent = 'Consultando ciudadanos…';
        }

        try {
            const ciudadanos = new Map();
            let paginaConsulta = 1;
            let ultimaPaginaConsulta = 1;
            let total = 0;

            do {
                const parametros = new URLSearchParams({
                    page: String(paginaConsulta),
                    por_pagina: String(porPagina)
                });

                if (filtros.curp) {
                    parametros.set('curp', filtros.curp);
                }

                if (filtros.verificacion) {
                    parametros.set('verificacion', filtros.verificacion);
                }

                const respuesta = await fetch(
                    API + '/monitoreo/ciudadanos?' + parametros, {
                        signal: consulta.signal,
                        headers: {
                            'Authorization': 'Bearer ' + tokenMonitoreo,
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    }
                );

                if (consulta.signal.aborted) return;

                if (respuesta.status === 401) {
                    cerrarSesionMonitoreo();
                    return;
                }

                if (respuesta.status === 429) {
                    throw new Error(
                        'Hay demasiadas consultas. Espera un momento e intenta otra vez.'
                    );
                }

                const datos = await respuesta.json();

                if (consulta.signal.aborted) return;

                if (!respuesta.ok) {
                    throw new Error(
                        datos.errors?.curp?.[0] ??
                        datos.message ??
                        'No se pudo consultar el listado.'
                    );
                }

                if (
                    !Array.isArray(datos.ciudadanos) ||
                    !Number.isInteger(datos.paginacion?.total) ||
                    !Number.isInteger(datos.paginacion?.ultima)
                ) {
                    throw new Error('La respuesta del listado no es válida.');
                }

                total = datos.paginacion.total;
                ultimaPaginaConsulta = datos.paginacion.ultima;

                for (const ciudadano of datos.ciudadanos) {
                    ciudadanos.set(ciudadano.id, ciudadano);
                }

                paginaConsulta++;
            } while (
                ciudadanos.size < cantidadSolicitada &&
                paginaConsulta <= ultimaPaginaConsulta
            );

            if (
                consulta.signal.aborted ||
                consultaListadoCiudadanos !== consulta
            ) {
                return;
            }

            const visibles = Array.from(ciudadanos.values())
                .sort((a, b) => b.id - a.id)
                .slice(0, cantidadSolicitada);

            const contenido = document.createDocumentFragment();

            for (const ciudadano of visibles) {
                const fila = document.createElement('tr');

                const cuenta = ciudadano.estatus === 'ACTIVO' ?
                    'Activa' :
                    ciudadano.estatus;

                const valores = [
                    ciudadano.nombre,
                    ciudadano.curp,
                    cuenta,
                    textoIdentidadCiudadano(ciudadano)
                ];

                for (const valor of valores) {
                    const celda = document.createElement('td');
                    celda.textContent = valor ?? 'Sin información';
                    fila.appendChild(celda);
                }

                const acciones = document.createElement('td');
                const boton = document.createElement('button');

                boton.type = 'button';
                boton.className =
                    'ciudadanos-boton ciudadanos-boton-secundario';
                boton.textContent = 'Ver ficha';

                boton.setAttribute(
                    'aria-label',
                    'Ver ficha de ' + ciudadano.nombre
                );

                boton.addEventListener('click', () => {
                    abrirFichaCiudadano(ciudadano.id);
                });

                acciones.appendChild(boton);
                fila.appendChild(acciones);
                contenido.appendChild(fila);
            }

            if (visibles.length === 0) {
                const fila = document.createElement('tr');
                const celda = document.createElement('td');

                celda.colSpan = 5;
                celda.textContent =
                    'No se encontraron ciudadanos con esos filtros.';

                fila.appendChild(celda);
                contenido.appendChild(fila);
            }

            tabla.replaceChildren(contenido);

            ultimaPaginaCiudadanos = Math.max(1, Math.ceil(total / 20));
            paginaCiudadanos = Math.min(pagina, ultimaPaginaCiudadanos);

            document.getElementById('totalListadoCiudadanos').textContent =
                `Mostrando ${visibles.length} de ${total}`;

            verMas.hidden = visibles.length >= total;
            mensaje.textContent = '';
        } catch (error) {
            if (
                !consulta.signal.aborted &&
                consultaListadoCiudadanos === consulta
            ) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    error.message || 'No fue posible consultar los ciudadanos.';
            }
        } finally {
            if (consultaListadoCiudadanos === consulta) {
                consultaListadoCiudadanos = null;
                buscar.disabled = false;
                verMas.disabled = false;
                tabla.removeAttribute('aria-busy');
            }
        }
    }

    function buscarCiudadanos(evento) {
        evento.preventDefault();

        const campo = document.getElementById('buscarCurpCiudadano');
        const curp = campo.value.trim().toUpperCase();
        const mensaje = document.getElementById('mensajeListadoCiudadanos');

        if (curp && !/^[A-Z0-9]{1,18}$/.test(curp)) {
            mensaje.style.color = '#9F2241';
            mensaje.textContent =
                'Escribe únicamente letras y números, hasta 18 caracteres.';
            campo.focus({
                preventScroll: true
            });
            return;
        }

        campo.value = curp;

        filtrosCiudadanos = {
            curp,
            verificacion: document.getElementById(
                'filtroIdentidadCiudadano'
            ).value
        };

        cargarCiudadanos(1);
    }

    function limpiarBusquedaCiudadanos() {
        document.getElementById('formBuscarCiudadanos').reset();
        filtrosCiudadanos = {
            curp: '',
            verificacion: ''
        };
        cargarCiudadanos(1);
    }

    function verMasCiudadanos() {
        if (
            temporizadorCurpCiudadanos ||
            consultaListadoCiudadanos ||
            paginaCiudadanos >= ultimaPaginaCiudadanos
        ) {
            return;
        }

        const curp = document.getElementById('buscarCurpCiudadano')
            .value.trim().toUpperCase();

        const verificacion = document.getElementById(
            'filtroIdentidadCiudadano'
        ).value;

        if (
            curp !== filtrosCiudadanos.curp ||
            verificacion !== filtrosCiudadanos.verificacion
        ) {
            document.getElementById('formBuscarCiudadanos').requestSubmit();
            return;
        }

        cargarCiudadanos(paginaCiudadanos + 1);
    }

    let consultaFichaCiudadano = null;
    let ciudadanoEnFicha = null;
    let consultaEdicionCiudadano = null;

    function mostrarEdicionCiudadano() {
        if (!ciudadanoEnFicha) return;

        document.getElementById('editarNombreCiudadano').value =
            ciudadanoEnFicha.nombre ?? '';

        document.getElementById('editarTelefonoCiudadano').value =
            ciudadanoEnFicha.telefono ?? '';

        document.getElementById('formEditarCiudadano').hidden = false;
        document.getElementById('btnEditarCiudadano').hidden = true;
    }

    function cancelarEdicionCiudadano() {
        document.getElementById('formEditarCiudadano').hidden = true;
        document.getElementById('btnEditarCiudadano').hidden = !ciudadanoEnFicha;
    }

    async function guardarEdicionCiudadano(evento) {
        evento.preventDefault();

        if (!ciudadanoEnFicha || consultaEdicionCiudadano) return;

        const nombre = document.getElementById('editarNombreCiudadano')
            .value.trim();

        const telefono = document.getElementById('editarTelefonoCiudadano')
            .value.trim();

        const mensaje = document.getElementById('mensajeFichaCiudadano');
        const guardar = document.getElementById('btnGuardarCiudadano');
        const id = ciudadanoEnFicha.id;

        if (!nombre || !/^[0-9]{10}$/.test(telefono)) {
            mensaje.style.color = '#9F2241';
            mensaje.textContent =
                'Escribe un nombre y un teléfono de 10 dígitos.';
            return;
        }

        const consulta = new AbortController();
        consultaEdicionCiudadano = consulta;
        guardar.disabled = true;
        mensaje.style.color = '#62676C';
        mensaje.textContent = 'Guardando cambios…';

        try {
            const respuesta = await fetch(
                API + '/monitoreo/ciudadanos/' +
                encodeURIComponent(id), {
                    method: 'PATCH',
                    signal: consulta.signal,
                    headers: {
                        'Authorization': 'Bearer ' + tokenMonitoreo,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nombre,
                        telefono
                    }),
                    cache: 'no-store'
                }
            );

            if (respuesta.status === 401) {
                cerrarSesionMonitoreo();
                return;
            }

            const datos = await respuesta.json();

            if (!respuesta.ok) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    datos.errors?.nombre?.[0] ??
                    datos.errors?.telefono?.[0] ??
                    datos.message ??
                    'No se pudieron guardar los cambios.';
                return;
            }

            if (
                consulta.signal.aborted ||
                !document.getElementById('fichaCiudadanoMonitoreo').open ||
                ciudadanoEnFicha?.id !== id
            ) return;

            ciudadanoEnFicha.nombre = datos.ciudadano.nombre;
            ciudadanoEnFicha.telefono = datos.ciudadano.telefono;
            ciudadanoEnFicha.estatus_verificacion =
                datos.ciudadano.estatus_verificacion;

            document.getElementById('fichaCiudadanoNombre').textContent =
                ciudadanoEnFicha.nombre;

            document.getElementById('fichaCiudadanoTelefono').textContent =
                ciudadanoEnFicha.telefono;

            document.getElementById('fichaCiudadanoIdentidad').textContent =
                textoIdentidadCiudadano(ciudadanoEnFicha);

            document.getElementById('fichaCiudadanoVerificada').textContent =
                datos.ciudadano.estatus_verificacion === 'VERIFICADA' ?
                document.getElementById(
                    'fichaCiudadanoVerificada'
                ).textContent :
                'No registrada';

            cancelarEdicionCiudadano();

            mensaje.style.color = '#235B4E';
            mensaje.textContent = datos.message;

            cargarCiudadanos(paginaCiudadanos, true);
            cargarResumenCiudadanos();
        } catch (error) {
            if (!consulta.signal.aborted) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    'No fue posible guardar los cambios. Intenta nuevamente.';
            }
        } finally {
            if (consultaEdicionCiudadano === consulta) {
                consultaEdicionCiudadano = null;
                guardar.disabled = false;
            }
        }
    }

    function fechaCiudadano(valor) {
        if (!valor) return 'No registrada';

        const fecha = new Date(valor);

        return Number.isNaN(fecha.getTime()) ?
            'Fecha no disponible' :
            fecha.toLocaleString('es-MX');
    }

    function cerrarFichaCiudadano() {
        consultaFichaCiudadano?.abort();
        consultaFichaCiudadano = null;

        ciudadanoEnFicha = null;
        document.getElementById('formEditarCiudadano').hidden = true;
        document.getElementById('btnEditarCiudadano').hidden = true;
        document.getElementById('fichaCiudadanoMonitoreo').close();
    }

    async function abrirFichaCiudadano(idCiudadano) {
        if (!tokenMonitoreo) return;

        consultaFichaCiudadano?.abort();

        const consulta = new AbortController();
        consultaFichaCiudadano = consulta;

        const ficha = document.getElementById('fichaCiudadanoMonitoreo');
        const mensaje = document.getElementById('mensajeFichaCiudadano');
        const datosFicha = document.getElementById('datosFichaCiudadano');
        const motivo = document.getElementById('motivoRechazoFichaCiudadano');
        const revisar = document.getElementById('btnRevisarIdentidadCiudadano');
        ciudadanoEnFicha = null;
        document.getElementById('formEditarCiudadano').hidden = true;
        document.getElementById('btnEditarCiudadano').hidden = true;

        datosFicha.hidden = true;
        motivo.hidden = true;
        revisar.hidden = true;

        mensaje.style.color = '#62676C';
        mensaje.textContent = 'Consultando datos del ciudadano…';

        if (!ficha.open) {
            ficha.showModal();
        }

        try {
            const respuesta = await fetch(
                API + '/monitoreo/ciudadanos/' + encodeURIComponent(idCiudadano), {
                    signal: consulta.signal,
                    headers: {
                        'Authorization': 'Bearer ' + tokenMonitoreo,
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                }
            );

            if (
                consulta.signal.aborted ||
                consultaFichaCiudadano !== consulta ||
                !ficha.open
            ) {
                return;
            }

            if (respuesta.status === 401) {
                cerrarSesionMonitoreo();
                return;
            }

            const datos = await respuesta.json();

            if (
                consulta.signal.aborted ||
                consultaFichaCiudadano !== consulta ||
                !ficha.open
            ) {
                return;
            }

            if (!respuesta.ok) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    datos.message ?? 'No se pudo abrir la ficha.';
                return;
            }

            const ciudadano = datos.ciudadano;
            ciudadanoEnFicha = ciudadano;
            document.getElementById('btnEditarCiudadano').hidden = false;

            const campos = {
                fichaCiudadanoNombre: ciudadano.nombre,
                fichaCiudadanoCurp: ciudadano.curp,
                fichaCiudadanoTelefono: ciudadano.telefono,
                fichaCiudadanoCorreo: ciudadano.email,
                fichaCiudadanoEstatus: ciudadano.estatus === 'ACTIVO' ?
                    'Activa' : ciudadano.estatus,
                fichaCiudadanoIdentidad: textoIdentidadCiudadano(ciudadano),
                fichaCiudadanoRegistro: fechaCiudadano(ciudadano.fecha_registro),
                fichaCiudadanoVerificada: fechaCiudadano(
                    ciudadano.identidad_verificada_at
                )
            };

            for (const [id, valor] of Object.entries(campos)) {
                document.getElementById(id).textContent =
                    valor || 'No registrado';
            }

            motivo.textContent = ciudadano.motivo_rechazo ?
                'Motivo del rechazo: ' + ciudadano.motivo_rechazo :
                '';

            motivo.hidden = !ciudadano.motivo_rechazo;

            revisar.hidden = !(
                ciudadano.estatus === 'ACTIVO' &&
                ciudadano.estatus_verificacion === 'PENDIENTE' &&
                ciudadano.tiene_foto_ine
            );

            datosFicha.hidden = false;
            mensaje.textContent = '';
        } catch (error) {
            if (
                !consulta.signal.aborted &&
                consultaFichaCiudadano === consulta &&
                ficha.open
            ) {
                mensaje.style.color = '#9F2241';
                mensaje.textContent =
                    'No fue posible cargar la ficha. Intenta nuevamente.';
            }
        } finally {
            if (consultaFichaCiudadano === consulta) {
                consultaFichaCiudadano = null;
            }
        }
    }

    function abrirPendientesDesdeFicha() {
        cerrarFichaCiudadano();
        mostrarSeccionMonitoreo('identidad');
    }

    document.getElementById('fichaCiudadanoMonitoreo')
        .addEventListener('close', () => {
            if (!document.getElementById('fichaCiudadanoMonitoreo').open) {
                consultaFichaCiudadano?.abort();
                consultaFichaCiudadano = null;
            }
        });

    let consultaResumenCiudadanos = null;

    function actualizarContadoresIdentidad(total) {
        const cantidad = Number(total);

        if (!Number.isInteger(cantidad) || cantidad < 0) {
            return;
        }

        document.querySelectorAll('[data-contador-identidad]')
            .forEach((contador) => {
                const texto = cantidad > 99 ? '99+' : String(cantidad);

                if (contador.textContent !== texto) {
                    contador.textContent = texto;
                }

                contador.hidden = cantidad === 0;
                contador.title =
                    `${cantidad} verificaciones de identidad pendientes`;
            });
    }

    async function cargarResumenCiudadanos() {
        if (
            !tokenMonitoreo ||
            document.hidden ||
            consultaResumenCiudadanos
        ) {
            return;
        }

        const consulta = new AbortController();
        consultaResumenCiudadanos = consulta;

        try {
            const respuesta = await fetch(
                API + '/monitoreo/ciudadanos/resumen', {
                    signal: consulta.signal,
                    headers: {
                        'Authorization': 'Bearer ' + tokenMonitoreo,
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                }
            );

            if (consulta.signal.aborted) return;

            if (respuesta.status === 401) {
                cerrarSesionMonitoreo();
                return;
            }

            if (!respuesta.ok) return;

            const datos = await respuesta.json();

            if (!consulta.signal.aborted) {
                actualizarContadoresIdentidad(datos.pendientes_identidad);
            }
        } catch (error) {
            if (!consulta.signal.aborted) {
                console.error('No se pudo actualizar el contador de identidad:', error);
            }
        } finally {
            if (consultaResumenCiudadanos === consulta) {
                consultaResumenCiudadanos = null;
            }
        }
    }

    function actualizarContadorCiudadanosVisible() {
        if (!tokenMonitoreo || document.hidden) {
            return;
        }

        const verificaciones = document.getElementById(
            'panelVerificacionIdentidad'
        );

        if (verificaciones.hidden) {
            cargarResumenCiudadanos();
        }

        const panel = document.getElementById('panelCiudadanosMonitoreo');
        const listado = document.getElementById('vistaListadoCiudadanos');
        const ficha = document.getElementById('fichaCiudadanoMonitoreo');
        const tabla = document.getElementById('tablaCiudadanosMonitoreo');

        const filtrosCoinciden =
            document.getElementById('buscarCurpCiudadano')
            .value.trim().toUpperCase() === filtrosCiudadanos.curp &&
            document.getElementById('filtroIdentidadCiudadano')
            .value === filtrosCiudadanos.verificacion;

        if (
            !panel.hidden &&
            !listado.hidden &&
            !ficha.open &&
            !consultaListadoCiudadanos &&
            !temporizadorCurpCiudadanos &&
            !tabla.contains(document.activeElement) &&
            filtrosCoinciden
        ) {
            cargarCiudadanos(paginaCiudadanos, true);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        setInterval(actualizarContadorCiudadanosVisible, 15000);

        document.addEventListener(
            'visibilitychange',
            actualizarContadorCiudadanosVisible
        );
    });

    window.addEventListener('pagehide', () => {
        consultaListadoCiudadanos?.abort();
        consultaFichaCiudadano?.abort();
        consultaResumenCiudadanos?.abort();
    });

    let temporizadorCurpCiudadanos = null;

    const campoBusquedaCurp = document.getElementById(
        'buscarCurpCiudadano'
    );

    const formularioBusquedaCiudadanos = document.getElementById(
        'formBuscarCiudadanos'
    );

    campoBusquedaCurp.addEventListener('input', () => {
        clearTimeout(temporizadorCurpCiudadanos);
        consultaListadoCiudadanos?.abort();

        temporizadorCurpCiudadanos = setTimeout(() => {
            temporizadorCurpCiudadanos = null;
            formularioBusquedaCiudadanos.requestSubmit();
        }, 500);
    });

    formularioBusquedaCiudadanos.addEventListener('submit', () => {
        clearTimeout(temporizadorCurpCiudadanos);
        temporizadorCurpCiudadanos = null;
    });

    formularioBusquedaCiudadanos.addEventListener('reset', () => {
        clearTimeout(temporizadorCurpCiudadanos);
        temporizadorCurpCiudadanos = null;
    });
</script>