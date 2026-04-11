# 🚀 Proyecto control de Peajes

Este proyecto está configurado para ejecutarse utilizando Docker, permitiendo un entorno de desarrollo consistente para todo el equipo.

---

## 🧰 Requisitos

Antes de comenzar, asegúrate de tener instalado:

* Git
* Docker (Docker Desktop o Docker Engine)
* Docker Compose


******IMPORTANTE *******

"sudo usermod -aG docker $USER"  /// $USER es tu usuario, este comando nos servira para tener acceso a docker sin ser usaurio root, ejecutarlo si se usa linux.


"git config --global core.fileMode false" // ejecuta este comando para no tener problema con la configuracion de permisos que dara docker,  ejecuta este comando ya sea linux o windows.


"sudo chown -R usuario:grupo ."  //  si tienes probelmas con los permisos ejecuta este comando, solo si estas usando linux.

---

## 📥 Instalación del proyecto

### 1. Clonar el repositorio

git clone <URL_DEL_REPOSITORIO>
cd mi-proyecto

---

### 2. Configurar variables de entorno

//windows

hacer una copia del archivo .env.example y poner el nuevo nombre como ".env"

//linux
```bash
cp .env.example .env
```

Edita el archivo `.env` y cambia estas configuraciones

HASHIDS_SALT // esta variable es para la clave unica que se genera para los QR
DB_CONNECTION=mysql // mysql como base de datos
DB_HOST=mariadb  // seleccionamos el host que usaremos en nuestro caso de llama mariadb
DB_PORT=3306 // seleccionamos el puerto con el que usaremos la base de datos
DB_DATABASE // nombre de la base de datos
DB_USERNAME= // usuario de la base de datos
DB_PASSWORD=// contrasenia
DB_ROOT_PASSWORD= // esta es la contraenia root recurda no compartirlo con nadie

---

### 3. Levantar los contenedores

docker compose up -d --build


Esto iniciará los servicios necesarios:

* PHP + Apache
* Base de datos (MariaDB)

---

### 4. Generar la clave de la aplicación

```bash
docker compose exec app php artisan key:generate
```

---

### 5. Instalar dependencias de Laravel

docker compose exec app composer install

---


### 6. Ejecutar migraciones

```bash
docker compose exec app php artisan migrate
```

---

## 🌐 Acceso al sistema

Una vez completados los pasos anteriores, puedes acceder al proyecto en:

```
http://localhost:8080
```

¡Listo! 🎉 Ahora puedes empezar a trabajar en el sistema🚀

---

## 🔄 Flujo de trabajo diario

Cada vez que haya actualizaciones en el proyecto:

```bash
git pull origin  {nombre de rama}
docker compose exec app composer install
docker compose exec app php artisan migrate
```

---


## 🛠️ Comandos útiles

### Ver contenedores en ejecución

```bash
docker compose ps
```

### Detener contenedores

```bash
docker compose stop 
```

### Reconstruir contenedores

```bash
docker compose up -d --build -v // nota agregar el -v si se quiere eliminar tambien eliminar la base de datos
```

---

## ⚠️ Notas importantes

* La carpeta `vendor/` se genera con `composer install`
* Si hay cambios en dependencias (`composer.json` o `composer.lock`), es necesario volver a ejecutar:

```bash
docker compose exec app composer install
```

* La base de datos se mantiene persistente gracias a los volúmenes de Docker

---

## 🎯 Objetivo

Este entorno permite que cualquier desarrollador pueda levantar el proyecto rápidamente sin preocuparse por configuraciones locales de PHP, Apache o base de datos.



