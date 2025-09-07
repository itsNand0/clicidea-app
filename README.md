# clicidea-app

* Siempre dar acceso al storage:link para que no haya inconvenientes no solo con la carga de adjuntos, sino en general.

* Para que el service worker funcione correctamente en Desarrollo se debe agregar como segura la wwb en chrome://flags/ opcion unsefuly...

* En caso que no funcione las notificaciones en prueba ejecutar php artisan queue:work --stop-when-empty ya que podrian quedar en cola