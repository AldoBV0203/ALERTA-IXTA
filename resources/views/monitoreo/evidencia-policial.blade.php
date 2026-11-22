<style>
    #panelEvidenciaMonitoreo {
        padding: 20px;
        border-top: 1px solid #E5DDE0;
    }

    #panelEvidenciaMonitoreo h4 {
        margin: 0 0 14px;
        color: #252A30;
        font-size: 16px;
    }

    #panelEvidenciaMonitoreo p {
        margin: 8px 0;
        font-size: 13px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    #mensajeEvidenciaMonitoreo {
        color: #62676C;
    }

    #mensajeEvidenciaMonitoreo:empty {
        display: none;
    }

    #btnVerEvidenciaMonitoreo {
        margin-top: 12px;
        padding: 10px 16px;
        border: 0;
        border-radius: 8px;
        background: #691C32;
        color: #FFFFFF;
        font: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    #btnVerEvidenciaMonitoreo:hover:not(:disabled) {
        background: #9F2241;
    }

    #btnVerEvidenciaMonitoreo:disabled {
        opacity: 0.65;
        cursor: wait;
    }

    #visorEvidenciaMonitoreo {
        margin: 16px 0 0;
    }

    #imagenEvidenciaMonitoreo {
        display: block;
        width: 100%;
        max-height: 420px;
        object-fit: contain;
        border: 1px solid #E5DDE0;
        border-radius: 8px;
        background: #FAF8F9;
    }

    #panelEvidenciaMonitoreo[hidden],
    #panelEvidenciaMonitoreo [hidden] {
        display: none !important;
    }
</style>

<section
    id="panelEvidenciaMonitoreo"
    aria-labelledby="tituloEvidenciaMonitoreo"
    hidden>

    <h4 id="tituloEvidenciaMonitoreo">Evidencia policial</h4>

    <div id="datosEvidenciaMonitoreo" hidden>
        <p>
            <strong>Policía:</strong>
            <span id="policiaEvidenciaMonitoreo"></span>
        </p>

        <p>
            <strong>Fecha:</strong>
            <span id="fechaEvidenciaMonitoreo"></span>
        </p>
    </div>

    <p id="mensajeEvidenciaMonitoreo" role="status"></p>

    <button
        id="btnVerEvidenciaMonitoreo"
        type="button"
        aria-controls="visorEvidenciaMonitoreo"
        aria-expanded="false"
        hidden>
        Ver evidencia
    </button>

    <figure id="visorEvidenciaMonitoreo" hidden>
        <img
            id="imagenEvidenciaMonitoreo"
            alt="Fotografía de evidencia de la alerta seleccionada">
    </figure>
</section>

<script>
    let evidenciaMonitoreoActual = null;
    let consultaFotoEvidencia = null;
    let urlFotoEvidencia = null;

    function cerrarFotoEvidenciaMonitoreo() {
        consultaFotoEvidencia?.abort();
        consultaFotoEvidencia = null;

        const imagen = document.getElementById('imagenEvidenciaMonitoreo');
        imagen.removeAttribute('src');

        if (urlFotoEvidencia) {
            URL.revokeObjectURL(urlFotoEvidencia);
        }

        urlFotoEvidencia = null;

        document.getElementById('visorEvidenciaMonitoreo').hidden = true;

        const boton = document.getElementById('btnVerEvidenciaMonitoreo');

        boton.disabled = false;
        boton.textContent = 'Ver evidencia';
        boton.setAttribute('aria-expanded', 'false');
    }

    function actualizarEvidenciaMonitoreo(alerta) {
        const evidencia = alerta.evidencia;

        const clave = `${alerta.id_alerta}:${evidencia?.id ?? 'sin-foto'}:${evidencia?.fecha_hora ?? ''}`;

        if (evidenciaMonitoreoActual?.clave === clave) {
            return;
        }

        cerrarFotoEvidenciaMonitoreo();

        evidenciaMonitoreoActual = {
            clave,
            idAlerta: Number(alerta.id_alerta)
        };

        document.getElementById('panelEvidenciaMonitoreo').hidden = false;
        document.getElementById('datosEvidenciaMonitoreo').hidden = !evidencia;
        document.getElementById('btnVerEvidenciaMonitoreo').hidden = !evidencia;

        const mensaje = document.getElementById('mensajeEvidenciaMonitoreo');

        mensaje.textContent = evidencia
            ? ''
            : 'Esta alerta todavía no tiene evidencia registrada.';

        if (!evidencia) return;

        document.getElementById('policiaEvidenciaMonitoreo').textContent =
            evidencia.policia;

        const fecha = document.getElementById('fechaEvidenciaMonitoreo');

        const fechaRegistro = evidencia.fecha_hora
            ? new Date(evidencia.fecha_hora)
            : null;

        fecha.textContent = fechaRegistro &&
            !Number.isNaN(fechaRegistro.getTime())
                ? fechaRegistro.toLocaleString('es-MX')
                : 'Fecha no disponible';
    }

    async function alternarFotoEvidenciaMonitoreo() {
        const boton = document.getElementById('btnVerEvidenciaMonitoreo');
        const visor = document.getElementById('visorEvidenciaMonitoreo');
        const mensaje = document.getElementById('mensajeEvidenciaMonitoreo');
        const imagen = document.getElementById('imagenEvidenciaMonitoreo');

        if (!visor.hidden) {
            cerrarFotoEvidenciaMonitoreo();
            mensaje.textContent = '';
            return;
        }

        if (!evidenciaMonitoreoActual || boton.disabled) return;

        const { idAlerta, clave } = evidenciaMonitoreoActual;

        const consulta = new AbortController();
        consultaFotoEvidencia = consulta;

        boton.disabled = true;
        mensaje.textContent = 'Cargando fotografía…';

        function sigueSeleccionada() {
            return !consulta.signal.aborted &&
                consultaFotoEvidencia === consulta &&
                evidenciaMonitoreoActual?.clave === clave &&
                Number(idAlertaSeleccionada) === idAlerta;
        }

        try {
            const respuesta = await fetch(
                API + '/monitoreo/alertas/' + idAlerta + '/evidencia',
                {
                    signal: consulta.signal,
                    headers: {
                        'Authorization': 'Bearer ' + tokenMonitoreo,
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                }
            );

            if (!sigueSeleccionada()) return;

            if (respuesta.status === 401) {
                cerrarSesionMonitoreo();
                return;
            }

            if (!respuesta.ok) {
                mensaje.textContent = respuesta.status === 403
                    ? 'Tu cuenta no tiene permiso para consultar la evidencia.'
                    : 'No se pudo abrir la fotografía de evidencia.';
                return;
            }

            const archivo = await respuesta.blob();

            if (!sigueSeleccionada()) return;

            const nuevaUrl = URL.createObjectURL(archivo);

            try {
                imagen.src = nuevaUrl;

                await imagen.decode();

                if (!sigueSeleccionada()) return;

                urlFotoEvidencia = nuevaUrl;
                visor.hidden = false;
                mensaje.textContent = '';
                boton.textContent = 'Ocultar evidencia';
                boton.setAttribute('aria-expanded', 'true');
            } finally {
                if (urlFotoEvidencia !== nuevaUrl) {
                    URL.revokeObjectURL(nuevaUrl);
                }
            }
        } catch (error) {
            if (sigueSeleccionada()) {
                mensaje.textContent =
                    'No fue posible cargar la fotografía. Intenta nuevamente.';
            }
        } finally {
            if (consultaFotoEvidencia === consulta) {
                consultaFotoEvidencia = null;
                boton.disabled = false;
            }
        }
    }

    document.getElementById('btnVerEvidenciaMonitoreo')
        .addEventListener('click', alternarFotoEvidenciaMonitoreo);

    window.addEventListener('pagehide', cerrarFotoEvidenciaMonitoreo);
</script>