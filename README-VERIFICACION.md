# CTRL Z — Verificación de login por código (2do factor)

Este paquete ya está adaptado a tu proyecto (MVC: `controllers/`, `models/`,
`config/`), no es genérico. Lo que cambia respecto a tu código original:

## Qué se agregó / modificó

| Archivo | Cambio |
|---|---|
| `controllers/AuthController.php` | `loginPersona()` ya no abre sesión directo: ahora genera el código, lo guarda y lo envía por correo. Se agregaron `verificarCodigo()` y `reenviarCodigo()`, con sus `case` en el switch. |
| `models/Persona.php` | Se agregaron 5 métodos nuevos al final de la clase para guardar/leer el código de verificación. |
| `config/smtp.php` | **NUEVO.** Credenciales del correo que envía los códigos. |
| `libs/PHPMailer/` | **NUEVO.** Librería PHPMailer (3 archivos), no requiere Composer. |
| `index.html` | Se agregó el modal `#verificacionModal`, con las mismas clases que ya usas (`modal-overlay`, `modal-login`, etc.), justo después del modal de login. |
| `js/modal-login.js` | El submit del login ahora revisa si la respuesta pide verificación (`resultado.data.requiere_verificacion`); si es así, abre el modal de código en vez de redirigir. Se agregó el manejo del formulario de verificación y el botón "reenviar código". |
| `_temp/tablas_verificacion.sql` | **NUEVO.** Crea `codigos_verificacion` y también `loginerror`, que tu `AuthController.php` ya usaba pero no estaba en tu dump `updsctrolz.sql`. |

## Cómo funciona el flujo ahora

1. El usuario llena correo + contraseña en el modal de Login (igual que antes).
2. `AuthController.php?action=login` valida contra `persona`/`usuario` **igual
   que antes** (mismos 3 intentos, mismo bloqueo de cuenta). Si la contraseña
   es correcta:
   - Genera un código de 4 dígitos y lo guarda en `codigos_verificacion`
     (vence en 10 minutos).
   - Lo envía por correo con PHPMailer.
   - Guarda los datos del usuario como "pendientes" en `$_SESSION`
     (**todavía no autenticado**).
   - Responde `{ requiere_verificacion: true, correo: "..." }`.
3. El frontend cierra el modal de login y abre el modal de código.
4. El usuario ingresa el código de 4 dígitos → se manda a
   `AuthController.php?action=verificar-codigo`.
5. Si el código es correcto (existe, no expiró, no se agotaron los 5
   intentos), **recién ahí** se abre la sesión real (`persona_id`,
   `persona_nombres`, etc., igual que tu código original) y se responde con
   el `redirect` según `tipoper`, igual que antes.

## 1. Instalar en tu WAMP

Copia estas carpetas/archivos dentro de tu proyecto en
`C:\wamp64\www\CTROL-Z\` (sobrescribiendo los que ya tienes):

```
CTROL-Z/
├── config/
│   ├── database.php     (sin cambios)
│   └── smtp.php          NUEVO — edítalo con tus credenciales
├── controllers/
│   └── AuthController.php   REEMPLAZAR
├── models/
│   └── Persona.php          REEMPLAZAR
├── libs/
│   └── PHPMailer/            NUEVO — copiar carpeta completa
├── index.html                REEMPLAZAR
├── js/
│   └── modal-login.js        REEMPLAZAR
└── _temp/
    └── tablas_verificacion.sql   NUEVO
```

(El resto de tu proyecto —`admin/`, `docente/`, `estudiante/`,
`controllers/CursosController.php`, `controllers/UsuariosController.php`,
`controllers/PersonaController.php`, `css/`, `img/`— no se tocó, va igual.)

## 2. Base de datos

1. Abre phpMyAdmin (`http://localhost/phpmyadmin`), entra a `updsctrolz`.
2. Importa/ejecuta `_temp/tablas_verificacion.sql`. Esto crea:
   - `codigos_verificacion` (nueva, para el 2do factor).
   - `loginerror` (te faltaba en el dump original, tu código ya la usaba).

## 3. Configurar el correo (`config/smtp.php`)

Si usas Gmail para enviar los códigos:

1. Activa verificación en 2 pasos en esa cuenta de Gmail.
2. Genera una "Contraseña de aplicación" en
   https://myaccount.google.com/apppasswords (son 16 caracteres).
3. En `config/smtp.php`, reemplaza:
   ```php
   'usuario'  => 'tu_correo@gmail.com',
   'password' => 'xxxx xxxx xxxx xxxx',  // la contraseña de aplicación, no tu password normal
   ```

Si tu universidad/empresa da otro SMTP, cambia `host`, `puerto` y
`encriptado` según esos datos.

## 4. Probar

1. WAMP en verde (Apache + MySQL corriendo).
2. Ve a `http://localhost/CTROL-Z/index.html`.
3. Click en "Login", ingresa un correo/contraseña que ya exista en tu tabla
   `usuario` (con contraseña hasheada, como ya la guardas con
   `password_hash`).
4. Debería salir el modal pidiendo el código de 4 dígitos, y llegarte el
   correo (revisa spam la primera vez).
5. Ingresa el código → te redirige a `admin/index.php`,
   `docente/index.php` o `estudiante/index.php` según el `tipoper`, igual
   que antes.

## Notas

- El código expira en 10 minutos y no se puede reutilizar.
- Límite de 5 intentos incorrectos por código antes de tener que pedir uno
  nuevo (usa el link "¿No te llegó? Reenviar código").
- Tu regla de 3 intentos fallidos de **contraseña** (que desactiva la
  cuenta) sigue funcionando exactamente igual que antes, sin tocarse.
- No subas `config/smtp.php` con tus credenciales reales a un repositorio
  público (agrégalo a `.gitignore`).
