<div class="campo selector-foto" data-selector-foto data-campo-foto="{{ $idCampoFoto }}">

    <label for="{{ $idCampoFoto }}">
        {{ $etiquetaFoto }}
    </label>

    <div class="selector-foto__acciones">

        <button
            type="button"
            class="selector-foto__boton selector-foto__boton--principal"
            data-foto-abrir
            aria-controls="camara-{{ $idCampoFoto }}"
            aria-expanded="false">
            Tomar foto
        </button>

        <label
            for="{{ $idCampoFoto }}"
            class="selector-foto__boton selector-foto__boton--principal">

            Seleccionar foto

            <input
                id="{{ $idCampoFoto }}"
                name="{{ $nombreCampoFoto }}"
                type="file"
                accept="image/jpeg,image/png"
                required>
        </label>

    </div>

    <p class="selector-foto__archivo" data-foto-archivo role="status">
        Sin foto seleccionada.
    </p>

    <small>JPG o PNG de hasta 2 MB.</small>

    @error($nombreCampoFoto)
    <small class="error">{{ $message }}</small>
    @enderror

    <section
        id="camara-{{ $idCampoFoto }}"
        class="selector-foto__panel"
        aria-label="Captura de fotografía"
        hidden>

        <div class="selector-foto__cabecera">
            <h3>Tomar fotografía</h3>

            <button type="button" data-foto-cerrar>
                Cerrar
            </button>
        </div>

        <video
            data-foto-video
            autoplay
            muted
            playsinline>
        </video>

        <img
            data-foto-preview
            alt="Vista previa de la fotografía"
            hidden>

        <p class="selector-foto__mensaje" data-foto-mensaje role="status"></p>

        <div data-foto-controles-captura>
            <button
                type="button"
                class="selector-foto__boton selector-foto__boton--principal"
                data-foto-capturar
                disabled>
                Capturar foto
            </button>
        </div>

        <div
            class="selector-foto__acciones"
            data-foto-controles-revision
            hidden>

            <button
                type="button"
                class="selector-foto__boton selector-foto__boton--principal"
                data-foto-usar>
                Usar esta foto
            </button>

            <button
                type="button"
                class="selector-foto__boton selector-foto__boton--principal"
                data-foto-repetir>
                Tomar otra
            </button>
        </div>

    </section>
</div>

@once
<style>
    .selector-foto__acciones {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .selector-foto .selector-foto__boton {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 46px;
        box-sizing: border-box;
        margin: 0;
        padding: 12px 10px;
        overflow: hidden;
        border: 1px solid #691C32;
        border-radius: 8px;
        background: #FFFFFF;
        color: #691C32;
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
    }

    .selector-foto .selector-foto__boton--principal {
        background: #691C32;
        color: #FFFFFF;
    }

    .selector-foto .selector-foto__boton input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        opacity: 0;
        cursor: pointer;
    }

    .selector-foto__boton:focus-visible,
    .selector-foto__boton:focus-within {
        outline: 2px solid #9F2241;
        outline-offset: 3px;
    }

    .selector-foto__boton:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .selector-foto__archivo,
    .selector-foto__mensaje {
        margin: 10px 0;
        color: #62676C;
        font-size: 13px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .selector-foto__panel {
        margin-top: 16px;
        padding: 14px;
        border: 1px solid #E5DDE0;
        border-radius: 12px;
        background: #FAF8F9;
    }

    .selector-foto__cabecera {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .selector-foto__cabecera h3 {
        margin: 0;
        color: #252A30;
        font-size: 16px;
    }

    .selector-foto__cabecera button {
        padding: 4px;
        border: none;
        background: transparent;
        color: #691C32;
        font: inherit;
        font-size: 13px;
        cursor: pointer;
    }

    .selector-foto video,
    .selector-foto img {
        display: block;
        width: 100%;
        border-radius: 8px;
        background: #252A30;
    }

    .selector-foto [hidden] {
        display: none !important;
    }
</style>
@endonce
@once
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-selector-foto]').forEach((selector) => {
            const campo = selector.querySelector('input[type="file"]');
            const abrir = selector.querySelector('[data-foto-abrir]');
            const panel = abrir.getAttribute('aria-controls');
            const camara = document.getElementById(panel);
            const video = selector.querySelector('[data-foto-video]');
            const imagen = selector.querySelector('[data-foto-preview]');
            const mensaje = selector.querySelector('[data-foto-mensaje]');
            const archivo = selector.querySelector('[data-foto-archivo]');
            const capturar = selector.querySelector('[data-foto-capturar]');
            const controlesCaptura = selector.querySelector('[data-foto-controles-captura]');
            const controlesRevision = selector.querySelector('[data-foto-controles-revision]');

            const limite = 2 * 1024 * 1024;

            let flujo = null;
            let foto = null;
            let vista = null;
            let sesion = 0;

            function detenerCamara() {
                sesion++;

                flujo?.getTracks().forEach((pista) => pista.stop());

                flujo = null;
                video.srcObject = null;
                capturar.disabled = true;
            }

            function cerrarCamara() {
                detenerCamara();
                foto = null;

                if (vista) {
                    URL.revokeObjectURL(vista);
                }

                vista = null;
                imagen.removeAttribute('src');
                camara.hidden = true;
                abrir.setAttribute('aria-expanded', 'false');
            }

            async function abrirCamara() {
                cerrarCamara();

                const actual = sesion;

                camara.hidden = false;
                video.hidden = false;
                imagen.hidden = true;
                controlesCaptura.hidden = false;
                controlesRevision.hidden = true;

                abrir.setAttribute('aria-expanded', 'true');
                mensaje.textContent = 'Abriendo cámara…';

                if (!navigator.mediaDevices?.getUserMedia) {
                    mensaje.textContent =
                        'La cámara no está disponible. Puedes usar Seleccionar foto.';
                    return;
                }

                try {
                    const nuevoFlujo = await navigator.mediaDevices.getUserMedia({
                        audio: false,
                        video: {
                            facingMode: {
                                ideal: 'environment'
                            },
                            width: {
                                ideal: 1920
                            },
                            height: {
                                ideal: 1080
                            }
                        }
                    });

                    if (actual !== sesion) {
                        nuevoFlujo.getTracks().forEach((pista) => pista.stop());
                        return;
                    }

                    flujo = nuevoFlujo;
                    video.srcObject = flujo;

                    await video.play();

                    if (actual !== sesion) return;

                    capturar.disabled = false;
                    mensaje.textContent =
                        'Coloca el documento o la evidencia dentro de la imagen.';
                } catch (error) {
                    if (actual !== sesion) return;

                    detenerCamara();

                    mensaje.textContent = error.name === 'NotAllowedError' ?
                        'Permite el acceso a la cámara o utiliza Seleccionar foto.' :
                        'No se pudo abrir la cámara. Revisa que esté conectada y libre.';
                }
            }

            abrir.addEventListener('click', abrirCamara);

            selector.querySelector('[data-foto-repetir]')
                .addEventListener('click', abrirCamara);

            selector.querySelector('[data-foto-cerrar]')
                .addEventListener('click', () => {
                    cerrarCamara();
                    abrir.focus({
                        preventScroll: true
                    });
                });

            capturar.addEventListener('click', async () => {
                if (!video.videoWidth || !video.videoHeight || video.readyState < 2) {
                    return;
                }

                const actual = sesion;

                capturar.disabled = true;
                mensaje.textContent = 'Preparando fotografía…';

                try {
                    const lienzo = document.createElement('canvas');

                    const escala = Math.min(
                        1,
                        1600 / Math.max(video.videoWidth, video.videoHeight)
                    );

                    lienzo.width = Math.round(video.videoWidth * escala);
                    lienzo.height = Math.round(video.videoHeight * escala);

                    lienzo.getContext('2d').drawImage(
                        video,
                        0,
                        0,
                        lienzo.width,
                        lienzo.height
                    );

                    let contenido = null;

                    for (const calidad of [0.9, 0.8, 0.7]) {
                        contenido = await new Promise((resolver) => {
                            lienzo.toBlob(resolver, 'image/jpeg', calidad);
                        });

                        if (actual !== sesion) return;
                        if (contenido && contenido.size <= limite) break;
                    }

                    if (!contenido || contenido.size > limite) {
                        throw new Error('No se pudo preparar la fotografía.');
                    }

                    foto = new File(
                        [contenido],
                        `${campo.id}.jpg`, {
                            type: 'image/jpeg'
                        }
                    );

                    vista = URL.createObjectURL(foto);
                    imagen.src = vista;

                    detenerCamara();

                    video.hidden = true;
                    imagen.hidden = false;
                    controlesCaptura.hidden = true;
                    controlesRevision.hidden = false;

                    mensaje.textContent =
                        'Revisa que la fotografía se vea clara antes de usarla.';
                } catch (error) {
                    if (actual !== sesion) return;

                    capturar.disabled = false;
                    mensaje.textContent =
                        'No se pudo preparar la fotografía. Intenta capturarla otra vez.';
                }
            });

            selector.querySelector('[data-foto-usar]')
                .addEventListener('click', () => {
                    if (!foto) return;

                    try {
                        const transferencia = new DataTransfer();

                        transferencia.items.add(foto);
                        campo.files = transferencia.files;

                        campo.dispatchEvent(
                            new Event('change', {
                                bubbles: true
                            })
                        );

                        abrir.focus({
                            preventScroll: true
                        });
                    } catch (error) {
                        mensaje.textContent =
                            'No se pudo adjuntar la captura. Utiliza Seleccionar foto.';
                    }
                });

            campo.addEventListener('change', () => {
                cerrarCamara();

                const elegida = campo.files[0];

                archivo.textContent = 'Sin foto seleccionada.';

                if (!elegida) return;

                const formatoIncorrecto = elegida.type &&
                    !['image/jpeg', 'image/png'].includes(elegida.type);

                if (elegida.size > limite || formatoIncorrecto) {
                    campo.value = '';
                    archivo.textContent =
                        'Selecciona una foto JPG o PNG de hasta 2 MB.';
                    return;
                }

                archivo.textContent = `Foto lista para enviar: ${elegida.name}`;
            });

            campo.form?.addEventListener('submit', cerrarCamara, true);

            campo.form?.addEventListener('reset', () => {
                cerrarCamara();
                archivo.textContent = 'Sin foto seleccionada.';
            });

            window.addEventListener('pagehide', cerrarCamara);

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) cerrarCamara();
            });

            new MutationObserver(() => {
                const formularioOculto = selector.getClientRects().length === 0;

                if (!camara.hidden && (campo.disabled || formularioOculto)) {
                    cerrarCamara();
                }
            }).observe(document.body, {
                attributes: true,
                subtree: true,
                attributeFilter: ['class', 'style', 'hidden', 'disabled']
            });
        });
    });
</script>
@endonce