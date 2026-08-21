# Contrato de API — CTRL Z (Frontend Angular → Backend PHP real)



Base URL configurada en el frontend: `environment.phpApiUrl` (ver
`src/environments/environment.ts`). Cada endpoint se arma como
`${phpApiUrl}/controllers/<Controller>.php?action=<accion>`.

**Autenticación real:** el backend usa **sesión de PHP (cookie)**, no JWT.
Por eso todas las peticiones al backend van con `withCredentials: true`
(lo agrega automáticamente `credentials.interceptor.ts`), y el backend
necesita CORS con `Access-Control-Allow-Credentials: true` — ver
`config/cors.php` en el backend, ya incluido en `AuthController.php`,
`PersonaController.php` y `UsuariosController.php`.

## Ya implementado en el backend (funciona hoy)

| Método | Endpoint | Descripción | Body | Respuesta |
|---|---|---|---|---|
| POST | `AuthController.php?action=login` | Login | form-urlencoded: `correo, password` | `{ ok, mensaje, data: { redirect, usuario } }` |
| GET | `AuthController.php?action=me` | Sesión actual (para restaurar al recargar) | — | `{ ok, data: { usuario } }` o 401 |
| GET | `AuthController.php?action=logout&json=1` | Cierra sesión (variante JSON para SPA) | — | `{ ok: true }` |
| POST | `PersonaController.php?action=registrar` | Registro | form-urlencoded: `nombres, apellidos, telefono, correo, password, ci, extension, f_nac, sexo, estcivil, tipoper` | `{ ok, mensaje, data: { id } }` |
| GET | `UsuariosController.php?action=listar` | Lista personas (solo admin) | — | `{ ok, data: [...] }` |
| POST | `UsuariosController.php?action=actualizarTipo` | Cambia rol de una persona (solo admin) | JSON: `{ id, tipoper }` | `{ ok, actualizado }` |

`tipoper`: `1` Administrador, `2` Docente, `3` Estudiante (el frontend lo
traduce con `tipoperARol()` en `usuario.model.ts`).

Probado end-to-end (login, listar/cambiar roles, restaurar sesión al
recargar, logout, registro + login inmediato) con MariaDB local — funciona.

## Todavía NO existe en el backend (el frontend ya está listo para consumirlo)

Estos servicios Angular (`CursoService`, `SuscripcionService`, `PagoService`,
`ReporteService`) ya apuntan al patrón de controller esperado. Mientras el
controller PHP no exista, la UI cae a datos de ejemplo local y muestra un
aviso "Backend pendiente" (ver `admin-dashboard`, `instructor-dashboard`,
`estudiante-dashboard`).

| Controller a crear | Acciones esperadas |
|---|---|
| `CursoController.php` | `listar` (query: nivel, categoria, precio_max, q), `detalle` (query: id), `crear`, `actualizar`, `eliminar`, `mios` |
| `SuscripcionController.php` | `planes`, `contratar` (body: planId), `activa`, `historial`, `cancelarRenovacion` (body: suscripcionId) |
| `PagoController.php` | `iniciar` (body: planId → responde checkoutUrl + transaccionId), `confirmar` (body: transaccionId), `mios` |
| `ReporteController.php` | `ingresosMensuales`, `cursosPopulares`, `estudiantesActividad` |
| `CalificacionController.php` | (usado por el panel Docente) crear/listar calificaciones por curso |

Todos deben incluir `require_once __DIR__ . '/../config/cors.php';` al
principio (igual que los controllers existentes) para que Angular pueda
consumirlos.

## Tablas sugeridas (anexo de la consigna, adaptadas al esquema actual)

Hoy la BD solo tiene `persona` + `usuario` (ver `_temp/updsctrolz.sql`).
Faltan, como mínimo: `courses`/`cursos`, `subscription_plans`,
`user_subscriptions`, `payments`, `course_enrollments` — este es el
entregable obligatorio de Sprint 0 (diagrama ER + 3FN + script SQL).

## Nota de seguridad sobre el repositorio

La carpeta `.git` que venía en el .rar del proyecto pertenece a otro
sistema (con un historial de 2020 y un archivo de credenciales de Google
comprometido). **No la reutilicen** — inicialicen un repo nuevo (`git init`)
solo con los archivos de `CTROL-Z`/`ctrolz-backend` antes de subir a
GitHub/GitLab.
