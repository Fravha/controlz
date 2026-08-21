# Guia de Git y GitHub para el desarrollo del proyecto Control Z

Este documento resume el flujo de trabajo recomendado para el sistema `Control Z Academy` usando Git y GitHub. La idea es que todo el equipo trabaje con el mismo criterio para evitar conflictos, mantener trazabilidad y facilitar las entregas.

## Objetivo

Usar Git y GitHub de forma ordenada para:

- versionar el codigo fuente
- trabajar en equipo sin sobrescribir cambios
- revisar avances antes de integrarlos
- mantener un historial claro del proyecto
- reducir errores durante el desarrollo

## Conceptos base

### Git

Git es el sistema de control de versiones que guarda el historial de cambios del proyecto en cada maquina de trabajo.

### GitHub

GitHub es la plataforma donde se aloja el repositorio remoto, se comparten ramas, se revisa codigo y se centraliza la colaboracion del equipo.

### Repositorio

Es la carpeta del proyecto con su historial de versiones.

### Commit

Es un registro de cambios con un mensaje que explica que se hizo.

### Branch o rama

Es una linea de trabajo separada. Permite desarrollar una funcionalidad sin afectar la rama principal.

### Pull Request

Es la solicitud para revisar e integrar cambios de una rama hacia otra.

## Estructura de ramas recomendada

Para `Control Z Academy` se recomienda una estructura simple y clara:

- `main`: contiene versiones estables y aprobadas
- `develop`: concentra el trabajo integrado del equipo
- `feature/nombre-corto`: nuevas funcionalidades
- `fix/nombre-corto`: correcciones de errores
- `hotfix/nombre-corto`: correcciones urgentes sobre produccion

Ejemplos:

- `feature/login-estudiantes`
- `feature/modulo-cursos`
- `fix/error-panel-admin`
- `hotfix/correccion-autenticacion`

## Configuracion inicial de Git

Instalar Git y luego configurar identidad local:

```bash
git config --global user.name "Tu Nombre"
git config --global user.email "tu-correo@ejemplo.com"
```

Verificar configuracion:

```bash
git config --list
```

## Primer acceso al repositorio

Clonar el proyecto:

```bash
git clone https://github.com/ORG_O_USUARIO/control-z-academy.git
cd control-z-academy
```

Ver ramas disponibles:

```bash
git branch -a
```

Cambiar a la rama de integracion:

```bash
git checkout develop
git pull origin develop
```

## Flujo de trabajo diario

### 1. Actualizar tu base antes de empezar

```bash
git checkout develop
git pull origin develop
```

### 2. Crear una rama nueva para tu tarea

```bash
git checkout -b feature/nombre-de-tu-tarea
```

Ejemplo:

```bash
git checkout -b feature/registro-estudiantes
```

### 3. Desarrollar y revisar cambios

Consultar estado:

```bash
git status
```

Ver cambios:

```bash
git diff
```

### 4. Agregar archivos al area de preparacion

Agregar todos los cambios:

```bash
git add .
```

O agregar archivos especificos:

```bash
git add src/components/Login.jsx
git add src/services/auth.js
```

### 5. Crear commits claros

```bash
git commit -m "feat: agrega formulario de registro de estudiantes"
```

## Convencion sugerida para mensajes de commit

Usar mensajes cortos, claros y consistentes:

- `feat:` nueva funcionalidad
- `fix:` correccion de error
- `docs:` cambios en documentacion
- `refactor:` mejora interna sin cambiar comportamiento esperado
- `style:` formato o estilos sin impacto funcional
- `test:` pruebas nuevas o actualizadas
- `chore:` tareas de soporte o mantenimiento

Ejemplos:

```bash
git commit -m "feat: agrega vista de cursos"
git commit -m "fix: corrige validacion del login"
git commit -m "docs: actualiza guia de instalacion"
```

## Subir cambios a GitHub

La primera vez que subes una rama:

```bash
git push -u origin feature/nombre-de-tu-tarea
```

Despues:

```bash
git push
```

## Uso de Pull Requests

Cuando la funcionalidad este lista:

1. subir la rama a GitHub
2. abrir un Pull Request hacia `develop`
3. describir que se hizo, que se probo y cualquier riesgo conocido
4. esperar revision antes de fusionar

Un buen Pull Request debe incluir:

- objetivo del cambio
- archivos o modulos impactados
- evidencia de pruebas si aplica
- capturas si hay cambios visuales
- observaciones para revisores

## Como mantener tu rama actualizada

Si `develop` tuvo cambios mientras trabajabas:

```bash
git checkout develop
git pull origin develop
git checkout feature/nombre-de-tu-tarea
git merge develop
```

Si aparecen conflictos, resolverlos manualmente, validar el resultado y luego hacer commit.

## Resolucion basica de conflictos

Git marca los archivos en conflicto. El flujo recomendado es:

1. abrir el archivo afectado
2. identificar los bloques marcados por Git
3. decidir que contenido conservar
4. eliminar las marcas de conflicto
5. guardar el archivo
6. agregarlo de nuevo
7. confirmar la resolucion con un commit

Comandos utiles:

```bash
git status
git add .
git commit -m "fix: resuelve conflictos con develop"
```

## Buenas practicas para el equipo

- hacer `pull` antes de empezar una tarea
- trabajar cada tarea en una rama separada
- no desarrollar directamente sobre `main`
- usar mensajes de commit descriptivos
- subir avances con frecuencia
- revisar `git status` antes de cada commit
- evitar commits con cambios no relacionados
- abrir Pull Requests pequenos y faciles de revisar
- validar funcionalidad antes de fusionar cambios
- documentar cambios importantes

## Lo que se debe evitar

- usar `main` como rama de trabajo diario
- hacer commits con mensajes como `cambios`, `update` o `arreglos`
- mezclar varias tareas distintas en un mismo commit
- subir codigo roto o sin probar
- resolver conflictos sin revisar el resultado final
- borrar ramas ajenas sin coordinacion

## Comandos utiles de referencia

```bash
git status
git branch
git branch -a
git checkout nombre-rama
git checkout -b nueva-rama
git pull origin develop
git add .
git commit -m "mensaje"
git push
git log --oneline --graph --decorate --all
```

## Flujo resumido para Control Z Academy

```text
1. git checkout develop
2. git pull origin develop
3. git checkout -b feature/nueva-tarea
4. desarrollar cambios
5. git add .
6. git commit -m "feat: descripcion del cambio"
7. git push -u origin feature/nueva-tarea
8. abrir Pull Request hacia develop
9. revisar, corregir y fusionar
```

## Recomendacion para organizacion del proyecto

Si el equipo trabaja por modulos, conviene reflejarlo en nombres de ramas y commits.

Ejemplos:

- `feature/modulo-matriculas`
- `feature/dashboard-docente`
- `feature/reportes-academicos`
- `fix/validacion-inscripciones`

Esto facilita identificar rapidamente el objetivo tecnico y funcional de cada cambio.

## Checklist antes de fusionar una rama

- el codigo compila o ejecuta correctamente
- no hay archivos temporales o innecesarios
- los cambios corresponden a una sola tarea
- el commit tiene un mensaje claro
- la rama fue actualizada con `develop`
- el Pull Request tiene descripcion suficiente
- se validaron los puntos criticos del cambio

## Conclusion

Git y GitHub no solo sirven para guardar codigo. En `Control Z Academy` deben usarse como una parte central del proceso de desarrollo para mantener orden, colaboracion y trazabilidad.

Si todo el equipo sigue un mismo flujo de ramas, commits y revisiones, el proyecto avanza con menos errores y con mayor control sobre cada entrega.
