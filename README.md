# CTRL Z Academy

Aplicación web académica desarrollada para el laboratorio de Proyecto Formativo de la UPDS. Permite registrar usuarios y acceder a paneles según el rol asignado: administrador, docente o estudiante.

El proyecto está construido con PHP sin framework, MySQL/PDO, JavaScript nativo y CSS. El segundo factor de autenticación se envía por correo mediante PHPMailer, incluido en el repositorio.

## Funcionalidades

- Registro de personas y creación de usuarios con contraseña hasheada.
- Inicio de sesión con control de intentos: después de tres contraseñas incorrectas, la cuenta se desactiva.
- Verificación en dos pasos por correo: código de cuatro dígitos, válido durante 10 minutos y con hasta cinco intentos.
- Redirección y protección de paneles por rol:
  - `1`: administrador;
  - `2`: docente;
  - `3`: estudiante.
- Panel administrativo para consultar, editar, cambiar roles y activar/desactivar usuarios.
- Paneles de docente y estudiante con contenido demostrativo de cursos.

## Requisitos

- Apache (XAMPP, WAMP o equivalente).
- PHP 7.0 o superior con extensiones `pdo_mysql` y `openssl` habilitadas.
- MySQL 5.6 o superior.
- Una cuenta SMTP para el envío de códigos (por ejemplo, Gmail con contraseña de aplicación).

## Instalación local

1. Copia el proyecto dentro del directorio público de Apache. En XAMPP, por ejemplo:

   ```text
   C:\xampp\htdocs\controlz
   ```

2. Inicia Apache y MySQL.

3. Crea una base de datos llamada `updsctrolz` e importa el archivo [`_temp/updsctrolz.sql`](_temp/updsctrolz.sql) desde phpMyAdmin o desde la terminal:

   ```bash
   mysql -u root -p updsctrolz < _temp/updsctrolz.sql
   ```

4. Revisa la conexión en [`config/database.php`](config/database.php) y ajusta host, base de datos, usuario y contraseña si tu instalación local no usa los valores predeterminados.

5. Configura el servidor de correo en `config/smtp.php`. Para Gmail se debe usar una contraseña de aplicación, no la contraseña normal de la cuenta.

6. Abre [http://localhost/controlz/index.html](http://localhost/controlz/index.html).

## Flujo de acceso

1. Desde la página principal, registra una cuenta o ingresa con una existente.
2. Al validar la contraseña, el sistema genera y envía un código de cuatro dígitos al correo registrado.
3. Introduce el código recibido. Si es válido, se crea la sesión y se abre el panel correspondiente al rol.
4. El enlace de cierre de sesión elimina la sesión y vuelve a la página principal.

## Estructura

```text
controlz/
├── admin/                 # Panel y gestión de usuarios/cursos
├── config/                # Configuración de MySQL y SMTP
├── controllers/           # Endpoints PHP para registro, acceso y administración
├── css/                   # Estilos de la interfaz
├── docente/               # Panel de docente
├── estudiante/            # Panel de estudiante
├── img/                   # Recursos gráficos
├── js/                    # Lógica de modales, registro, login y 2FA
├── libs/PHPMailer/        # Librería de correo incluida localmente
├── models/Persona.php     # Acceso a datos de personas, usuarios y códigos
├── _temp/updsctrolz.sql   # Esquema y datos iniciales de MySQL
└── index.html             # Página pública de inicio
```

## Rutas principales

| Ruta | Descripción |
| --- | --- |
| `index.html` | Página pública con formularios de registro e ingreso. |
| `controllers/PersonaController.php?action=registrar` | Registra una persona (POST). |
| `controllers/AuthController.php?action=login` | Valida credenciales e inicia el flujo de 2FA (POST). |
| `controllers/AuthController.php?action=verificar-codigo` | Verifica el código y crea la sesión (POST). |
| `controllers/AuthController.php?action=reenviar-codigo` | Envía otro código de verificación (POST). |
| `admin/index.php` | Panel exclusivo para administradores. |
| `docente/index.php` | Panel exclusivo para docentes. |
| `estudiante/index.php` | Panel exclusivo para estudiantes. |

## Base de datos

El volcado incluido crea las tablas `persona`, `usuario`, `roles`, `permisos`, `detalle_r_p`, `loginerror`, `codigos_verificacion` y `areas`.

`persona.tipoper` determina el acceso: `1` administrador, `2` docente y `3` estudiante. La tabla `usuario` contiene el correo, la contraseña hasheada y el estado de la cuenta.

## Estado actual y consideraciones

- Los paneles de docente y estudiante muestran datos de ejemplo; todavía no consultan cursos reales de la base de datos.
- El módulo de cursos del panel de administración es incompleto respecto al volcado incluido: `CursosController.php` espera las tablas `cursos` y `personas`, además de una conexión estática `Database::getConnection()`. El proyecto actual usa la tabla `persona` y `Database::connect()`. Debe unificarse esa implementación antes de usar este módulo en producción.
- No hay una suite de pruebas automatizadas ni un gestor de dependencias; PHPMailer se mantiene dentro de `libs/`.

## Seguridad

- No publiques `config/smtp.php` ni volcados SQL con cuentas, códigos o datos de usuarios reales. Añade esos archivos a `.gitignore` antes de subir el proyecto a un repositorio.
- Si se expuso una contraseña SMTP o una contraseña de aplicación, revócala y genera una nueva antes de desplegar el sistema.
- Antes de producción, usa variables de entorno o un archivo de configuración no versionado para las credenciales y habilita HTTPS.

## Documentación complementaria

El archivo [`README-VERIFICACION.md`](README-VERIFICACION.md) describe en detalle la configuración y las reglas del segundo factor de autenticación.
