# ALERTA IXTA
## Requisitos del sistema y criterios de aceptación

**Proyecto:** Sistema Municipal de Alerta y Botón de Pánico  
**Etapa:** Prototipo / MVP en ambiente controlado  
**Documento base:** Proyecto Ejecutivo ALERTA IXTA, versión 1.0

---

## 1. Requisitos funcionales mínimos

| ID | Requisito |
|---|---|
| RF-01 | El sistema permitirá autenticación de usuarios de prueba. |
| RF-02 | El usuario podrá activar una alerta mediante pulsación sostenida o confirmación equivalente. |
| RF-03 | El sistema solicitará permiso de geolocalización antes de capturar coordenadas. |
| RF-04 | Cada alerta generará un folio único. |
| RF-05 | El sistema registrará fecha, hora, tipo, ubicación, usuario y estado. |
| RF-06 | El dashboard mostrará nuevas alertas sin necesidad de consultar manualmente cada registro. |
| RF-07 | Un operador podrá aceptar una alerta y quedar asociado a ella. |
| RF-08 | El operador podrá registrar acciones y observaciones de seguimiento. |
| RF-09 | El sistema mantendrá historial de cambios relevantes. |
| RF-10 | Una alerta podrá cerrarse indicando resultado y observaciones. |
| RF-11 | Los supervisores podrán consultar indicadores del ambiente de prueba. |
| RF-12 | Los administradores podrán gestionar catálogos y cuentas autorizadas. |

---

## 2. Requisitos no funcionales

| ID | Requisito |
|---|---|
| RNF-01 | La interfaz será responsive y utilizable en teléfonos de gama media; el diseño deberá poder integrarse a la IxtAPPaluca. |
| RNF-02 | La comunicación se realizará únicamente mediante HTTPS en ambientes publicados. |
| RNF-03 | Las contraseñas se almacenarán mediante hash seguro y nunca en texto plano. |
| RNF-04 | Se utilizarán consultas SQL parametrizadas para reducir el riesgo de inyección. |
| RNF-05 | Las sesiones tendrán expiración y control por roles. |
| RNF-06 | El registro de errores no expondrá credenciales ni datos sensibles. |
| RNF-07 | El sistema deberá mantener tiempos de respuesta adecuados para demostración bajo la carga definida en el plan de pruebas. |
| RNF-08 | Se utilizará un diseño modular, código comentado cuando sea necesario y documentación de endpoints. |

---

## 3. Criterios de aceptación del MVP

| ID | Criterio de aceptación |
|---|---|
| CA-01 | Un usuario de prueba puede autenticarse y activar una alerta sin errores críticos. |
| CA-02 | La alerta genera un folio único y queda almacenada en MySQL. |
| CA-03 | La ubicación autorizada se representa correctamente en el mapa. |
| CA-04 | El dashboard identifica claramente las alertas nuevas y sus estados. |
| CA-05 | Un operador puede aceptar, registrar seguimiento y cerrar el evento. |
| CA-06 | La bitácora conserva las acciones relevantes. |
| CA-07 | Los roles impiden el acceso a funciones no autorizadas. |
| CA-08 | El sistema puede instalarse siguiendo la documentación entregada. |
| CA-09 | El repositorio no contiene contraseñas, tokens ni credenciales reales. |
| CA-10 | Existe evidencia documentada de las pruebas realizadas. |

---

## 4. Relación con el primer avance

En el primer avance se ha trabajado principalmente en la preparación del entorno, el modelo de datos, los wireframes, la implementación visual inicial de las pantallas del ciudadano, el diagrama de casos de uso y el control de versiones con Git/GitHub.

Las pantallas actuales son un **prototipo visual**. La autenticación real, el guardado de alertas, la geolocalización, el dashboard de operadores y los demás requisitos funcionales se implementarán en las siguientes fases del proyecto.

---

## 5. Nota de alcance

ALERTA IXTA se desarrolla inicialmente como un prototipo académico y tecnológico en ambiente controlado. No sustituye al 911 ni a los canales oficiales de emergencia y no debe conectarse automáticamente con servicios de emergencia reales sin autorización institucional y las evaluaciones correspondientes.
