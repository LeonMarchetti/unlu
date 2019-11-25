# Sprinkle UNLu para [UserFrosting 4](https://www.userfrosting.com)

[![UserFrosting Version](https://img.shields.io/badge/UserFrosting->=%204.2-brightgreen.svg)](https://github.com/userfrosting/UserFrosting)

Módulo (o sprinkle) de control de prestación de servicios del CIDETIC de la
Universidad Nacional de Luján.

## Setup
* `$ composer install`
* `$ php bakery bake`
    * Asignar base de datos
* Editar `sprinkles.json`
    * Agregar sprinkle `unlu` a la lista de sprinkles.
* `$ php bakery seed UnluPermissions`
    * Crear permisos de usuario del sprinkle.
* Agregar permisos `usuario_unlu` y `admin_unlu` al rol *Site Administrator* y
solo `usuario_unlu` al rol *User*.
* `$ chmod 757 app/cache app/logs app/sessions`
    * Permisos de directorios - El sitio se queja si no tiene permisos de
    escritura.
* Agregar datos de `usuario_unlu` para el usuario root.
* Crear servicios (directamente en la base de datos).
* `# chmod 777 app/logs/userfrosting.log`
    * Cambiar permisos para poder editarlo (si es necesario), permitiendo que
    lo pueda editar el sistema.

## Uso
En la barra lateral hay que acceder al item **UNLu**.

## Acciones
* **Solicitar Vinculación**
* **Solicitar Servicio**: O también denominado **petición**.
* **Baja Solicitud**: Borrar solicitud de servicio (petición).
* **Peticiones**: En la tabla que muestra las peticiones, se pueden acceder a
las acciones disponibles en el menú desplegable a la derecha de cada fila:
    * **Aprobar petición**: Aprobar una petición.
    * **Editar petición**: Editar la descripción o sus observaciones.

## Administradores
Se puede asignar un usuario como administrador al agregarle el rol
**Site Administrator**.
