<style>
    #panelPoliciasMonitoreo {
        color: #252A30;
    }

    #panelPoliciasMonitoreo[hidden],
    #panelPoliciasMonitoreo [hidden] {
        display: none !important;
    }

    #panelPoliciasMonitoreo .policias-panel {
        padding: 20px;
        border: 1px solid #E5DDE0;
        border-radius: 12px;
        background: #FFFFFF;
    }

    #panelPoliciasMonitoreo .policias-filtros {
        display: flex;
        align-items: end;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    #panelPoliciasMonitoreo .policias-campo {
        flex: 1 1 180px;
    }

    #panelPoliciasMonitoreo .policias-busqueda {
        flex: 2 1 340px;
    }

    #panelPoliciasMonitoreo label {
        display: block;
        margin-bottom: 7px;
        font-size: 13px;
        font-weight: 600;
    }

    #panelPoliciasMonitoreo input,
    #panelPoliciasMonitoreo select {
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

    #panelPoliciasMonitoreo .policias-boton {
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

    #panelPoliciasMonitoreo .policias-filtros .policias-boton {
        min-height: 44px;
    }

    #panelPoliciasMonitoreo .policias-boton-secundario {
        background: #FFFFFF;
        color: #691C32;
    }

    #panelPoliciasMonitoreo .policias-boton:hover:not(:disabled) {
        background: #9F2241;
        color: #FFFFFF;
    }

    #panelPoliciasMonitoreo .policias-boton:disabled {
        opacity: 0.5;
        cursor: default;
    }

    #panelPoliciasMonitoreo input:focus-visible,
    #panelPoliciasMonitoreo select:focus-visible,
    #panelPoliciasMonitoreo button:focus-visible {
        outline: 2px solid #9F2241;
        outline-offset: 2px;
    }

    #panelPoliciasMonitoreo .policias-tabla-contenedor {
        overflow-x: auto;
    }

    #panelPoliciasMonitoreo .policias-tabla {
        width: 100%;
        min-width: 680px;
        border-collapse: collapse;
        font-size: 13px;
    }

    #panelPoliciasMonitoreo .policias-tabla th,
    #panelPoliciasMonitoreo .policias-tabla td {
        padding: 14px 12px;
        border-bottom: 1px solid #E5DDE0;
        text-align: left;
        vertical-align: middle;
        overflow-wrap: anywhere;
    }

    #panelPoliciasMonitoreo .policias-tabla th {
        background: #FAF8F9;
        color: #62676C;
        font-size: 12px;
        font-weight: 600;
    }

    #panelPoliciasMonitoreo .policias-paginacion {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
        color: #62676C;
        font-size: 13px;
    }

    #panelPoliciasMonitoreo .policias-paginacion-acciones {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    #panelPoliciasMonitoreo .policias-mensaje {
        font-size: 13px;
        line-height: 1.5;
    }

    #panelPoliciasMonitoreo .policias-mensaje:empty {
        display: none;
    }

    #fichaPoliciaMonitoreo {
        width: min(640px, calc(100% - 32px));
        max-height: 85vh;
        padding: 24px;
        overflow-y: auto;
        border: 1px solid #E5DDE0;
        border-radius: 12px;
        background: #FFFFFF;
        color: #252A30;
        box-sizing: border-box;
    }

    #fichaPoliciaMonitoreo::backdrop {
        background: rgba(0, 0, 0, 0.4);
    }

    #fichaPoliciaMonitoreo .policias-ficha-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    #fichaPoliciaMonitoreo h3 {
        margin: 0;
        font-size: 20px;
    }

    #fichaPoliciaMonitoreo dl {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin: 0;
    }

    #fichaPoliciaMonitoreo dt {
        color: #62676C;
        font-size: 12px;
    }

    #fichaPoliciaMonitoreo dd {
        margin: 5px 0 0;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    #fichaPoliciaMonitoreo .policias-ficha-ancho {
        grid-column: 1 / -1;
    }

    @media (max-width: 480px) {
        #fichaPoliciaMonitoreo dl {
            grid-template-columns: 1fr;
        }
    }

    #formEditarDatosPolicia {
        margin-top: 18px;
    }

    #formEditarDatosPolicia .policias-campo {
        margin-bottom: 14px;
    }

    #formEditarDatosPolicia .policias-campo label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    #formEditarDatosPolicia .policias-campo input {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        border: 1px solid #D8D3D5;
        border-radius: 8px;
        font: inherit;
        box-sizing: border-box;
    }

    #formEditarDatosPolicia .policias-boton {
        margin: 4px 8px 0 0;
    }

    #controlEstadoPolicia {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid #E5DDE0;
    }

    #confirmacionEstadoPolicia {
        margin-top: 12px;
        padding: 12px;
        border: 1px solid #E5DDE0;
        border-radius: 8px;
        background: #FAF8F9;
    }

    #confirmacionEstadoPolicia p {
        margin: 0 0 12px;
        font-size: 13px;
    }

    #confirmacionEstadoPolicia .policias-boton {
        margin-right: 8px;
    }

    #panelPoliciasMonitoreo .policias-boton,
    #fichaPoliciaMonitoreo .policias-boton {
        border: 1px solid #691C32;
        background: #691C32;
        color: #FFFFFF;
    }

    #panelPoliciasMonitoreo .policias-boton:hover:not(:disabled),
    #fichaPoliciaMonitoreo .policias-boton:hover:not(:disabled) {
        border-color: #9F2241;
        background: #9F2241;
        color: #FFFFFF;
    }

    #btnEditarDatosPolicia {
        margin-top: 18px;
    }

    #panelPoliciasMonitoreo .policias-tabla td:last-child .policias-boton+.policias-boton {
        margin-left: 8px;
    }

    #dialogoServiciosPolicia {
        width: min(600px, calc(100% - 32px));
        max-height: 85vh;
        padding: 24px;
        overflow-y: auto;
        border: 1px solid #E5DDE0;
        border-radius: 12px;
        background: #FFFFFF;
        color: #252A30;
        box-sizing: border-box;
    }

    #dialogoServiciosPolicia::backdrop {
        background: rgba(0, 0, 0, 0.4);
    }

    #dialogoServiciosPolicia .policias-ficha-cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    #dialogoServiciosPolicia h3 {
        margin: 0;
        font-size: 20px;
    }

    #dialogoServiciosPolicia ul {
        margin: 0 0 18px;
        padding: 0;
        list-style: none;
    }

    #dialogoServiciosPolicia li {
        padding: 12px 0;
        border-bottom: 1px solid #E5DDE0;
        font-size: 14px;
        line-height: 1.5;
    }

    #dialogoServiciosPolicia .policias-boton {
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

    #dialogoServiciosPolicia .policias-boton:hover:not(:disabled) {
        background: #9F2241;
    }

    #dialogoServiciosPolicia .policias-boton:disabled {
        opacity: 0.5;
        cursor: default;
    }
</style>

<section
    id="panelPoliciasMonitoreo"
    aria-label="Policías registrados"
    hidden>

    <div class="policias-panel">
        <form
            id="formBuscarPolicias"
            class="policias-filtros"
            onsubmit="buscarPoliciasMonitoreo(event)">

            <div class="policias-campo policias-busqueda">
                <label for="buscarPoliciaMonitoreo">
                    Buscar policía
                </label>

                <input
                    id="buscarPoliciaMonitoreo"
                    name="buscar"
                    type="search"
                    maxlength="100"
                    placeholder="Nombre, número de empleado o CUIP completa"
                    autocomplete="off">
            </div>

            <div class="policias-campo">
                <label for="filtroCuentaPolicia">
                    Estado de la cuenta
                </label>

                <select id="filtroCuentaPolicia" name="estado">
                    <option value="">Todas</option>
                    <option value="ACTIVA">Activa</option>
                    <option value="INACTIVA">Inactiva</option>
                </select>
            </div>

            <button
                id="btnBuscarPoliciasMonitoreo"
                type="submit"
                class="policias-boton">
                Buscar
            </button>
        </form>

        <p
            id="mensajeListadoPolicias"
            class="policias-mensaje"
            role="status"></p>

        <div class="policias-tabla-contenedor">
            <table
                class="policias-tabla"
                aria-label="Listado de policías registrados">

                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Número de empleado</th>
                        <th scope="col">Unidad asignada</th>
                        <th scope="col">Cuenta</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaPoliciasMonitoreo">
                    <tr>
                        <td colspan="5">
                            El listado se cargará al abrir esta sección.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="policias-paginacion">
            <span id="totalListadoPolicias"></span>

            <button
                id="btnVerMasPolicias"
                type="button"
                class="policias-boton"
                onclick="verMasPoliciasMonitoreo()"
                hidden>
                Ver más
            </button>
        </div>
    </div>

    <dialog
        id="fichaPoliciaMonitoreo"
        aria-labelledby="tituloFichaPolicia">

        <div class="policias-ficha-cabecera">
            <h3 id="tituloFichaPolicia">Ficha del policía</h3>

            <button
                type="button"
                class="policias-boton policias-boton-secundario"
                onclick="cerrarFichaPoliciaMonitoreo()">
                Cerrar
            </button>
        </div>

        <p
            id="mensajeFichaPolicia"
            class="policias-mensaje"
            role="status"></p>

        <dl id="datosFichaPolicia" hidden>
            <div class="policias-ficha-ancho">
                <dt>Nombre completo</dt>
                <dd id="fichaPoliciaNombre"></dd>
            </div>

            <div>
                <dt>CURP</dt>
                <dd id="fichaPoliciaCurp"></dd>
            </div>

            <div>
                <dt>Teléfono</dt>
                <dd id="fichaPoliciaTelefono"></dd>
            </div>

            <div>
                <dt>CUIP</dt>
                <dd id="fichaPoliciaCuip"></dd>
            </div>

            <div>
                <dt>Número de empleado</dt>
                <dd id="fichaPoliciaEmpleado"></dd>
            </div>

            <div>
                <dt>Placa</dt>
                <dd id="fichaPoliciaPlaca"></dd>
            </div>

            <div>
                <dt>Unidad asignada</dt>
                <dd id="fichaPoliciaUnidad"></dd>
            </div>

            <div class="policias-ficha-ancho">
                <dt>Área de adscripción</dt>
                <dd id="fichaPoliciaAdscripcion"></dd>
            </div>

            <div>
                <dt>Estado de la cuenta</dt>
                <dd id="fichaPoliciaCuenta"></dd>
            </div>

            <div>
                <dt>Disponibilidad operativa</dt>
                <dd id="fichaPoliciaDisponibilidad"></dd>
            </div>
        </dl>

        <button
            id="btnEditarDatosPolicia"
            type="button"
            class="policias-boton policias-boton-secundario"
            onclick="mostrarEdicionPolicia()"
            hidden>
            Editar datos laborales
        </button>

        <form
            id="formEditarDatosPolicia"
            onsubmit="guardarDatosLaboralesPolicia(event)"
            hidden>
            <div class="policias-campo">
                <label for="editarEmpleadoPolicia">Número de empleado</label>
                <input
                    id="editarEmpleadoPolicia"
                    type="text"
                    maxlength="30"
                    required>
            </div>

            <div class="policias-campo">
                <label for="editarPlacaPolicia">Placa</label>
                <input
                    id="editarPlacaPolicia"
                    type="text"
                    maxlength="30"
                    required>
            </div>

            <div class="policias-campo">
                <label for="editarUnidadPolicia">Unidad asignada</label>
                <input
                    id="editarUnidadPolicia"
                    type="text"
                    maxlength="30"
                    required>
            </div>

            <div class="policias-campo">
                <label for="editarAdscripcionPolicia">Área de adscripción</label>
                <input
                    id="editarAdscripcionPolicia"
                    type="text"
                    maxlength="120"
                    required>
            </div>

            <button
                id="btnGuardarDatosPolicia"
                type="submit"
                class="policias-boton">
                Guardar cambios
            </button>

            <button
                type="button"
                class="policias-boton policias-boton-secundario"
                onclick="cancelarEdicionPolicia()">
                Cancelar
            </button>
        </form>

        <div id="controlEstadoPolicia" hidden>
            <button
                id="btnCambiarEstadoPolicia"
                type="button"
                class="policias-boton policias-boton-secundario"
                onclick="pedirCambioEstadoPolicia()">
            </button>

            <div id="confirmacionEstadoPolicia" hidden>
                <p id="textoConfirmacionEstadoPolicia"></p>

                <button
                    id="btnConfirmarEstadoPolicia"
                    type="button"
                    class="policias-boton"
                    onclick="confirmarCambioEstadoPolicia()">
                    Confirmar
                </button>

                <button
                    type="button"
                    class="policias-boton policias-boton-secundario"
                    onclick="cancelarCambioEstadoPolicia()">
                    Cancelar
                </button>
            </div>
        </div>
    </dialog>

    <dialog
        id="dialogoServiciosPolicia"
        aria-labelledby="tituloServiciosPolicia">

        <div class="policias-ficha-cabecera">
            <h3 id="tituloServiciosPolicia">Servicios del policía</h3>

            <button
                type="button"
                class="policias-boton"
                onclick="cerrarServiciosPolicia()">
                Cerrar
            </button>
        </div>

        <p
            id="mensajeServiciosPolicia"
            class="policias-mensaje"
            role="status"></p>

        <ul id="listaServiciosPolicia"></ul>

        <button
            id="btnVerMasServiciosPolicia"
            type="button"
            class="policias-boton"
            onclick="cargarServiciosPolicia()"
            hidden>
            Ver más
        </button>
    </dialog>

    <script>
        let paginaPoliciasMonitoreo = 1;
        let ultimaPaginaPoliciasMonitoreo = 1;
        let filtrosPoliciasMonitoreo = {
            buscar: '',
            estado: ''
        };
        let consultaListadoPoliciasMonitoreo = null;
        let consultaFichaPoliciaMonitoreo = null;
        let temporizadorBusquedaPoliciasMonitoreo = null;

        function actualizarPaginacionPoliciasMonitoreo() {
            const verMas = document.getElementById('btnVerMasPolicias');

            verMas.disabled = consultaListadoPoliciasMonitoreo !== null;
            verMas.hidden =
                paginaPoliciasMonitoreo >= ultimaPaginaPoliciasMonitoreo;
        }

        async function cargarListadoPoliciasMonitoreo(
            pagina = paginaPoliciasMonitoreo,
            silenciosa = false
        ) {
            if (!tokenMonitoreo) return;

            consultaListadoPoliciasMonitoreo?.abort();

            const consulta = new AbortController();
            consultaListadoPoliciasMonitoreo = consulta;

            const tabla = document.getElementById('tablaPoliciasMonitoreo');
            const mensaje = document.getElementById('mensajeListadoPolicias');
            const buscar = document.getElementById('btnBuscarPoliciasMonitoreo');
            const verMas = document.getElementById('btnVerMasPolicias');

            const cantidadSolicitada = pagina * 20;
            const porPagina = Math.min(cantidadSolicitada, 100);
            const filtros = {
                ...filtrosPoliciasMonitoreo
            };

            tabla.setAttribute('aria-busy', 'true');
            buscar.disabled = true;
            verMas.disabled = true;

            if (!silenciosa) {
                mensaje.textContent = 'Cargando policías…';
            }

            try {
                const policias = new Map();
                let paginaConsulta = 1;
                let ultimaPaginaConsulta = 1;
                let total = 0;

                do {
                    const parametros = new URLSearchParams({
                        page: String(paginaConsulta),
                        por_pagina: String(porPagina),
                        buscar: filtros.buscar,
                        estado: filtros.estado
                    });

                    const respuesta = await fetch(
                        API + '/monitoreo/policias/registrados?' + parametros, {
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

                    if (!respuesta.ok) {
                        mensaje.textContent = respuesta.status === 403 ?
                            'Tu cuenta no tiene permiso para consultar policías.' :
                            respuesta.status === 429 ?
                            'Has realizado varias consultas. Espera un momento.' :
                            'No se pudo actualizar el listado. Intenta nuevamente.';
                        return;
                    }

                    const datos = await respuesta.json();

                    if (consulta.signal.aborted) return;

                    if (
                        !Array.isArray(datos.policias) ||
                        !datos.paginacion ||
                        !Number.isInteger(Number(datos.paginacion.total)) ||
                        !Number.isInteger(Number(datos.paginacion.ultima))
                    ) {
                        throw new Error('Respuesta de listado inválida.');
                    }

                    total = Number(datos.paginacion.total);
                    ultimaPaginaConsulta = Number(datos.paginacion.ultima);

                    for (const policia of datos.policias) {
                        policias.set(policia.id_operador, policia);
                    }

                    paginaConsulta++;
                } while (
                    policias.size < cantidadSolicitada &&
                    paginaConsulta <= ultimaPaginaConsulta
                );

                if (
                    consulta.signal.aborted ||
                    consultaListadoPoliciasMonitoreo !== consulta
                ) return;

                const visibles = Array.from(policias.values())
                    .sort((a, b) => b.id_operador - a.id_operador)
                    .slice(0, cantidadSolicitada);

                const filas = document.createDocumentFragment();

                for (const policia of visibles) {
                    const fila = document.createElement('tr');

                    const valores = [
                        policia.nombre,
                        policia.numero_empleado,
                        policia.numero_patrulla,
                        policia.activo ? 'Activa' : 'Inactiva'
                    ];

                    for (const valor of valores) {
                        const celda = document.createElement('td');
                        celda.textContent = valor ?? 'No registrado';
                        fila.appendChild(celda);
                    }

                    const acciones = document.createElement('td');
                    const verFicha = document.createElement('button');

                    verFicha.type = 'button';
                    verFicha.className =
                        'policias-boton policias-boton-secundario';
                    verFicha.textContent = 'Ver ficha';

                    verFicha.addEventListener('click', () => {
                        abrirFichaPoliciaMonitoreo(policia.id_operador);
                    });

                    acciones.appendChild(verFicha);

                    const verServicios = document.createElement('button');
                    verServicios.type = 'button';
                    verServicios.className = 'policias-boton';
                    verServicios.textContent = 'Ver servicios';

                    verServicios.addEventListener('click', () => {
                        abrirServiciosPolicia(
                            policia.id_operador,
                            policia.nombre
                        );
                    });

                    acciones.appendChild(verServicios);
                    fila.appendChild(acciones);
                    filas.appendChild(fila);
                }

                if (visibles.length === 0) {
                    const fila = document.createElement('tr');
                    const celda = document.createElement('td');

                    celda.colSpan = 5;
                    celda.textContent =
                        'No se encontraron policías con esos filtros.';

                    fila.appendChild(celda);
                    filas.appendChild(fila);
                }

                tabla.replaceChildren(filas);

                ultimaPaginaPoliciasMonitoreo =
                    Math.max(1, Math.ceil(total / 20));

                paginaPoliciasMonitoreo =
                    Math.min(pagina, ultimaPaginaPoliciasMonitoreo);

                document.getElementById('totalListadoPolicias').textContent =
                    `Mostrando ${visibles.length} de ${total} policías`;

                mensaje.textContent = '';
            } catch (error) {
                if (
                    !consulta.signal.aborted &&
                    consultaListadoPoliciasMonitoreo === consulta
                ) {
                    mensaje.textContent =
                        'No se pudo cargar el listado. Revisa la conexión e intenta nuevamente.';
                }
            } finally {
                if (consultaListadoPoliciasMonitoreo === consulta) {
                    consultaListadoPoliciasMonitoreo = null;
                    tabla.setAttribute('aria-busy', 'false');
                    buscar.disabled = false;
                    actualizarPaginacionPoliciasMonitoreo();
                }
            }
        }

        function buscarPoliciasMonitoreo(evento) {
            evento.preventDefault();

            clearTimeout(temporizadorBusquedaPoliciasMonitoreo);
            temporizadorBusquedaPoliciasMonitoreo = null;

            filtrosPoliciasMonitoreo = {
                buscar: document
                    .getElementById('buscarPoliciaMonitoreo')
                    .value.trim(),
                estado: document.getElementById('filtroCuentaPolicia').value
            };

            cargarListadoPoliciasMonitoreo(1);
        }

        function verMasPoliciasMonitoreo() {
            if (
                consultaListadoPoliciasMonitoreo ||
                temporizadorBusquedaPoliciasMonitoreo ||
                paginaPoliciasMonitoreo >= ultimaPaginaPoliciasMonitoreo
            ) return;

            const buscar = document.getElementById(
                'buscarPoliciaMonitoreo'
            ).value.trim();

            const estado = document.getElementById(
                'filtroCuentaPolicia'
            ).value;

            if (
                buscar !== filtrosPoliciasMonitoreo.buscar ||
                estado !== filtrosPoliciasMonitoreo.estado
            ) {
                document.getElementById('formBuscarPolicias').requestSubmit();
                return;
            }

            cargarListadoPoliciasMonitoreo(paginaPoliciasMonitoreo + 1);
        }

        let policiaEnFicha = null;
        let consultaEdicionPolicia = null;

        function mostrarEdicionPolicia() {
            if (!policiaEnFicha) return;

            document.getElementById('editarEmpleadoPolicia').value =
                policiaEnFicha.numero_empleado ?? '';

            document.getElementById('editarPlacaPolicia').value =
                policiaEnFicha.numero_placa ?? '';

            document.getElementById('editarUnidadPolicia').value =
                policiaEnFicha.numero_patrulla ?? '';

            document.getElementById('editarAdscripcionPolicia').value =
                policiaEnFicha.adscripcion ?? '';

            document.getElementById('formEditarDatosPolicia').hidden = false;
            document.getElementById('btnEditarDatosPolicia').hidden = true;
        }

        function cancelarEdicionPolicia() {
            document.getElementById('formEditarDatosPolicia').hidden = true;
            document.getElementById('btnEditarDatosPolicia').hidden = !policiaEnFicha;
        }

        async function guardarDatosLaboralesPolicia(evento) {
            evento.preventDefault();

            if (!policiaEnFicha || consultaEdicionPolicia) return;

            const campos = {
                numero_empleado: document.getElementById(
                    'editarEmpleadoPolicia'
                ).value.trim(),
                numero_placa: document.getElementById(
                    'editarPlacaPolicia'
                ).value.trim(),
                numero_patrulla: document.getElementById(
                    'editarUnidadPolicia'
                ).value.trim(),
                adscripcion: document.getElementById(
                    'editarAdscripcionPolicia'
                ).value.trim()
            };

            const mensaje = document.getElementById('mensajeFichaPolicia');
            const guardar = document.getElementById(
                'btnGuardarDatosPolicia'
            );
            const ficha = document.getElementById(
                'fichaPoliciaMonitoreo'
            );
            const id = policiaEnFicha.id_operador;

            if (Object.values(campos).some(valor => !valor)) {
                mensaje.textContent = 'Completa los cuatro datos laborales.';
                return;
            }

            const consulta = new AbortController();
            consultaEdicionPolicia = consulta;
            guardar.disabled = true;
            mensaje.textContent = 'Guardando cambios…';

            try {
                const respuesta = await fetch(
                    API + '/monitoreo/policias/registrados/' +
                    encodeURIComponent(id), {
                        method: 'PATCH',
                        signal: consulta.signal,
                        headers: {
                            'Authorization': 'Bearer ' + tokenMonitoreo,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(campos),
                        cache: 'no-store'
                    }
                );

                if (consulta.signal.aborted) return;

                if (respuesta.status === 401) {
                    cerrarSesionMonitoreo();
                    return;
                }

                const datos = await respuesta.json();

                if (
                    consulta.signal.aborted ||
                    consultaEdicionPolicia !== consulta ||
                    !ficha.open ||
                    Number(policiaEnFicha?.id_operador) !== Number(id)
                ) return;

                if (!respuesta.ok) {
                    mensaje.textContent =
                        datos.errors?.numero_empleado?.[0] ??
                        datos.errors?.numero_placa?.[0] ??
                        datos.errors?.numero_patrulla?.[0] ??
                        datos.errors?.adscripcion?.[0] ??
                        datos.message ??
                        'No se pudieron guardar los cambios.';
                    return;
                }

                Object.assign(policiaEnFicha, datos.policia);

                document.getElementById(
                    'fichaPoliciaEmpleado'
                ).textContent = datos.policia.numero_empleado;

                document.getElementById(
                    'fichaPoliciaPlaca'
                ).textContent = datos.policia.numero_placa;

                document.getElementById(
                    'fichaPoliciaUnidad'
                ).textContent = datos.policia.numero_patrulla;

                document.getElementById(
                    'fichaPoliciaAdscripcion'
                ).textContent = datos.policia.adscripcion;

                cancelarEdicionPolicia();
                mensaje.textContent = datos.message;

                cargarListadoPoliciasMonitoreo(
                    paginaPoliciasMonitoreo,
                    true
                );
            } catch (error) {
                if (!consulta.signal.aborted) {
                    mensaje.textContent =
                        'No fue posible guardar los cambios. Intenta nuevamente.';
                }
            } finally {
                if (consultaEdicionPolicia === consulta) {
                    consultaEdicionPolicia = null;
                    guardar.disabled = false;
                }
            }
        }

        document.getElementById('fichaPoliciaMonitoreo')
            .addEventListener('close', () => {
                consultaEdicionPolicia?.abort();
                consultaEdicionPolicia = null;
                policiaEnFicha = null;
                document.getElementById(
                    'formEditarDatosPolicia'
                ).hidden = true;
                document.getElementById(
                    'btnEditarDatosPolicia'
                ).hidden = true;
            });

        let guardandoEstadoPolicia = false;

        function actualizarControlEstadoPolicia() {
            const control = document.getElementById(
                'controlEstadoPolicia'
            );
            const boton = document.getElementById(
                'btnCambiarEstadoPolicia'
            );

            control.hidden = !policiaEnFicha;

            if (!policiaEnFicha) return;

            boton.textContent = policiaEnFicha.activo ?
                'Desactivar cuenta' :
                'Activar cuenta';

            boton.disabled = guardandoEstadoPolicia;
        }

        function pedirCambioEstadoPolicia() {
            if (!policiaEnFicha || guardandoEstadoPolicia) return;

            document.getElementById(
                    'textoConfirmacionEstadoPolicia'
                ).textContent = policiaEnFicha.activo ?
                '¿Confirmas que quieres desactivar esta cuenta policial?' :
                '¿Confirmas que quieres activar esta cuenta policial?';

            document.getElementById(
                'confirmacionEstadoPolicia'
            ).hidden = false;
        }

        function cancelarCambioEstadoPolicia() {
            document.getElementById(
                'confirmacionEstadoPolicia'
            ).hidden = true;
        }

        async function confirmarCambioEstadoPolicia() {
            if (!policiaEnFicha || guardandoEstadoPolicia) return;

            const id = policiaEnFicha.id_operador;
            const activar = !policiaEnFicha.activo;
            const mensaje = document.getElementById(
                'mensajeFichaPolicia'
            );
            const confirmar = document.getElementById(
                'btnConfirmarEstadoPolicia'
            );

            guardandoEstadoPolicia = true;
            confirmar.disabled = true;
            actualizarControlEstadoPolicia();
            mensaje.textContent = 'Guardando estado de la cuenta…';

            try {
                const respuesta = await fetch(
                    API + '/monitoreo/policias/registrados/' +
                    encodeURIComponent(id) + '/estado', {
                        method: 'PATCH',
                        headers: {
                            'Authorization': 'Bearer ' + tokenMonitoreo,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            activo: activar
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
                    mensaje.textContent =
                        datos.message ??
                        'No se pudo cambiar el estado de la cuenta.';
                    return;
                }

                if (
                    !document.getElementById(
                        'fichaPoliciaMonitoreo'
                    ).open ||
                    Number(policiaEnFicha?.id_operador) !== Number(id)
                ) return;

                policiaEnFicha.activo = datos.activo;
                policiaEnFicha.disponible = datos.disponible;

                document.getElementById(
                        'fichaPoliciaCuenta'
                    ).textContent = datos.activo ?
                    'Activa' :
                    'Inactiva';

                document.getElementById(
                        'fichaPoliciaDisponibilidad'
                    ).textContent = datos.activo && datos.disponible ?
                    'Disponible' :
                    'No disponible';

                cancelarCambioEstadoPolicia();
                mensaje.textContent = datos.message;

                cargarListadoPoliciasMonitoreo(
                    paginaPoliciasMonitoreo,
                    true
                );
            } catch (error) {
                mensaje.textContent =
                    'No fue posible guardar el cambio. Intenta nuevamente.';
            } finally {
                guardandoEstadoPolicia = false;
                confirmar.disabled = false;
                actualizarControlEstadoPolicia();
            }
        }

        let idServiciosPolicia = null;
        let paginaServiciosPolicia = 0;
        let ultimaPaginaServiciosPolicia = 1;
        let consultaServiciosPolicia = null;

        function abrirServiciosPolicia(id, nombre) {
            consultaServiciosPolicia?.abort();

            idServiciosPolicia = id;
            paginaServiciosPolicia = 0;
            ultimaPaginaServiciosPolicia = 1;
            consultaServiciosPolicia = null;

            document.getElementById('tituloServiciosPolicia').textContent =
                'Servicios de ' + nombre;
            document.getElementById('mensajeServiciosPolicia').textContent = '';
            document.getElementById('listaServiciosPolicia').replaceChildren();
            document.getElementById('btnVerMasServiciosPolicia').hidden = true;

            const dialogo = document.getElementById('dialogoServiciosPolicia');

            if (!dialogo.open) dialogo.showModal();

            cargarServiciosPolicia();
        }

        function cerrarServiciosPolicia() {
            const dialogo = document.getElementById('dialogoServiciosPolicia');

            if (dialogo.open) dialogo.close();
        }

        async function cargarServiciosPolicia() {
            const dialogo = document.getElementById('dialogoServiciosPolicia');

            if (
                !dialogo.open ||
                idServiciosPolicia === null ||
                consultaServiciosPolicia ||
                paginaServiciosPolicia >= ultimaPaginaServiciosPolicia
            ) return;

            const id = idServiciosPolicia;
            const siguientePagina = paginaServiciosPolicia + 1;
            const consulta = new AbortController();
            consultaServiciosPolicia = consulta;

            const mensaje = document.getElementById('mensajeServiciosPolicia');
            const lista = document.getElementById('listaServiciosPolicia');
            const boton = document.getElementById('btnVerMasServiciosPolicia');

            mensaje.textContent = 'Cargando servicios…';
            boton.disabled = true;

            try {
                const respuesta = await fetch(
                    API + '/monitoreo/policias/registrados/' +
                    encodeURIComponent(id) +
                    '/servicios?page=' + siguientePagina, {
                        signal: consulta.signal,
                        headers: {
                            'Authorization': 'Bearer ' + tokenMonitoreo,
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    }
                );

                if (consulta.signal.aborted || !dialogo.open) return;

                if (respuesta.status === 401) {
                    cerrarSesionMonitoreo();
                    return;
                }

                if (!respuesta.ok) {
                    mensaje.textContent = respuesta.status === 403 ?
                        'Tu cuenta no tiene permiso para consultar estos servicios.' :
                        respuesta.status === 429 ?
                        'Has realizado varias consultas. Espera un momento.' :
                        'No se pudieron cargar los servicios. Intenta nuevamente.';
                    return;
                }

                const datos = await respuesta.json();

                if (
                    consulta.signal.aborted ||
                    !dialogo.open ||
                    idServiciosPolicia !== id
                ) return;

                if (!Array.isArray(datos.servicios) || !datos.paginacion) {
                    throw new Error('Respuesta de servicios inválida.');
                }

                const elementos = document.createDocumentFragment();

                for (const servicio of datos.servicios) {
                    const elemento = document.createElement('li');

                    elemento.textContent =
                        'Folio: ' + (servicio.folio ?? 'No registrado') +
                        ' · Tipo: ' + (servicio.tipo ?? 'No registrado') +
                        ' · Estado: ' + (servicio.estado ?? 'No registrado') +
                        ' · Aceptada: ' +
                        (servicio.fecha_aceptacion ?? 'No registrada');

                    elementos.appendChild(elemento);
                }

                lista.appendChild(elementos);

                paginaServiciosPolicia = Number(datos.paginacion.actual);
                ultimaPaginaServiciosPolicia =
                    Number(datos.paginacion.ultima);

                if (paginaServiciosPolicia === 1 && datos.servicios.length === 0) {
                    mensaje.textContent =
                        'Este policía todavía no ha aceptado alertas.';
                } else {
                    mensaje.textContent = '';
                }

                boton.hidden =
                    paginaServiciosPolicia >= ultimaPaginaServiciosPolicia;
            } catch (error) {
                if (!consulta.signal.aborted && dialogo.open) {
                    mensaje.textContent =
                        'No se pudieron cargar los servicios. Revisa la conexión e intenta nuevamente.';
                }
            } finally {
                if (consultaServiciosPolicia === consulta) {
                    consultaServiciosPolicia = null;
                    boton.disabled = false;
                }
            }
        }

        document.getElementById('dialogoServiciosPolicia')
            .addEventListener('close', () => {
                consultaServiciosPolicia?.abort();
                consultaServiciosPolicia = null;
                idServiciosPolicia = null;
            });

        async function abrirFichaPoliciaMonitoreo(id) {
            consultaFichaPoliciaMonitoreo?.abort();

            const consulta = new AbortController();
            consultaFichaPoliciaMonitoreo = consulta;

            const ficha = document.getElementById('fichaPoliciaMonitoreo');
            const contenido = document.getElementById('datosFichaPolicia');
            const mensaje = document.getElementById('mensajeFichaPolicia');
            consultaEdicionPolicia?.abort();
            consultaEdicionPolicia = null;
            policiaEnFicha = null;
            document.getElementById('formEditarDatosPolicia').hidden = true;
            document.getElementById('btnEditarDatosPolicia').hidden = true;

            policiaEnFicha = null;
            cancelarCambioEstadoPolicia();
            actualizarControlEstadoPolicia();

            contenido.hidden = true;
            mensaje.textContent = 'Cargando ficha…';

            if (!ficha.open) ficha.showModal();

            try {
                const respuesta = await fetch(
                    API + '/monitoreo/policias/registrados/' + id, {
                        signal: consulta.signal,
                        headers: {
                            'Authorization': 'Bearer ' + tokenMonitoreo,
                            'Accept': 'application/json'
                        },
                        cache: 'no-store'
                    }
                );

                if (consulta.signal.aborted || !ficha.open) return;

                if (respuesta.status === 401) {
                    cerrarSesionMonitoreo();
                    return;
                }

                if (!respuesta.ok) {
                    mensaje.textContent = respuesta.status === 404 ?
                        'No se encontró la cuenta policial.' :
                        respuesta.status === 403 ?
                        'Tu cuenta no tiene permiso para consultar esta ficha.' :
                        respuesta.status === 429 ?
                        'Has realizado varias consultas. Espera un momento.' :
                        'No se pudo cargar la ficha. Intenta nuevamente.';
                    return;
                }

                const datos = await respuesta.json();

                if (consulta.signal.aborted || !ficha.open) return;

                const policia = datos.policia;

                if (!policia || Number(policia.id_operador) !== Number(id)) {
                    throw new Error('Respuesta de ficha inválida.');
                }

                const campos = {
                    fichaPoliciaNombre: policia.nombre,
                    fichaPoliciaCurp: policia.curp,
                    fichaPoliciaTelefono: policia.telefono,
                    fichaPoliciaCuip: policia.cuip,
                    fichaPoliciaEmpleado: policia.numero_empleado,
                    fichaPoliciaPlaca: policia.numero_placa,
                    fichaPoliciaUnidad: policia.numero_patrulla,
                    fichaPoliciaAdscripcion: policia.adscripcion,
                    fichaPoliciaCuenta: policia.activo ? 'Activa' : 'Inactiva',
                    fichaPoliciaDisponibilidad: policia.activo && policia.disponible ?
                        'Disponible' : 'No disponible'
                };

                for (const [campo, valor] of Object.entries(campos)) {
                    document.getElementById(campo).textContent =
                        valor ?? 'No registrado';
                }

                contenido.hidden = false;
                mensaje.textContent = '';
                policiaEnFicha = policia;
                document.getElementById('btnEditarDatosPolicia').hidden = false;
                actualizarControlEstadoPolicia();
            } catch (error) {
                if (!consulta.signal.aborted && ficha.open) {
                    mensaje.textContent =
                        'No se pudo cargar la ficha. Revisa la conexión e intenta nuevamente.';
                }
            } finally {
                if (consultaFichaPoliciaMonitoreo === consulta) {
                    consultaFichaPoliciaMonitoreo = null;
                }
            }
        }

        function cerrarFichaPoliciaMonitoreo() {
            consultaFichaPoliciaMonitoreo?.abort();
            consultaFichaPoliciaMonitoreo = null;

            cancelarCambioEstadoPolicia();

            const ficha = document.getElementById(
                'fichaPoliciaMonitoreo'
            );

            if (ficha.open) ficha.close();
        }

        function detenerConsultasPoliciasMonitoreo() {
            clearTimeout(temporizadorBusquedaPoliciasMonitoreo);
            temporizadorBusquedaPoliciasMonitoreo = null;

            consultaListadoPoliciasMonitoreo?.abort();
            consultaListadoPoliciasMonitoreo = null;

            cerrarFichaPoliciaMonitoreo();

            document.getElementById('tablaPoliciasMonitoreo')
                .setAttribute('aria-busy', 'false');

            document.getElementById('btnBuscarPoliciasMonitoreo')
                .disabled = false;

            actualizarPaginacionPoliciasMonitoreo();
        }

        document.getElementById('fichaPoliciaMonitoreo')
            .addEventListener('close', () => {
                if (!document.getElementById('fichaPoliciaMonitoreo').open) {
                    consultaFichaPoliciaMonitoreo?.abort();
                    consultaFichaPoliciaMonitoreo = null;
                }
            });

        document.getElementById('buscarPoliciaMonitoreo')
            .addEventListener('input', () => {
                clearTimeout(temporizadorBusquedaPoliciasMonitoreo);
                consultaListadoPoliciasMonitoreo?.abort();

                temporizadorBusquedaPoliciasMonitoreo = setTimeout(() => {
                    temporizadorBusquedaPoliciasMonitoreo = null;
                    document.getElementById('formBuscarPolicias').requestSubmit();
                }, 500);
            });

        document.getElementById('filtroCuentaPolicia')
            .addEventListener('change', () => {
                document.getElementById('formBuscarPolicias').requestSubmit();
            });

        function actualizarPoliciasVisibles() {
            if (
                document.hidden ||
                !tokenMonitoreo ||
                document.getElementById('panelPoliciasMonitoreo').hidden ||
                document.getElementById('fichaPoliciaMonitoreo').open ||
                consultaListadoPoliciasMonitoreo ||
                temporizadorBusquedaPoliciasMonitoreo
            ) return;

            const buscar = document
                .getElementById('buscarPoliciaMonitoreo')
                .value.trim();

            const estado = document.getElementById('filtroCuentaPolicia').value;
            const tabla = document.getElementById('tablaPoliciasMonitoreo');

            if (
                buscar === filtrosPoliciasMonitoreo.buscar &&
                estado === filtrosPoliciasMonitoreo.estado &&
                !tabla.contains(document.activeElement)
            ) {
                cargarListadoPoliciasMonitoreo(
                    paginaPoliciasMonitoreo,
                    true
                );
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setInterval(actualizarPoliciasVisibles, 15000);

            document.addEventListener(
                'visibilitychange',
                actualizarPoliciasVisibles
            );
        });

        window.addEventListener('pagehide', detenerConsultasPoliciasMonitoreo);
    </script>
</section>