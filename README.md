# 🚀 Proyecto control de Peajes

Este proyecto está configurado para ejecutarse utilizando Docker, permitiendo un entorno de desarrollo consistente para todo el equipo.

---

## 🧰 Requisitos

Antes de comenzar, asegúrate de tener instalado:

* Git
* Docker (Docker Desktop o Docker Engine)
* Docker Compose

---

## 📥 Instalación del proyecto

### 1. Clonar el repositorio

git clone <URL_DEL_REPOSITORIO>
cd mi-proyecto

---

### 2. Levantar los contenedores

docker compose up -d --build


Esto iniciará los servicios necesarios:

* PHP + Apache
* Base de datos (MariaDB)

---

### 3. Instalar dependencias de Laravel

docker compose exec app composer install

---

### 4. Configurar variables de entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` si necesitas ajustar configuraciones.

---

### 5. Generar la clave de la aplicación

```bash
docker compose exec app php artisan key:generate
```

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

---

## 🔄 Flujo de trabajo diario

Cada vez que actualices el proyecto:

```bash
git pull
docker compose exec app composer install
docker compose exec app php artisan migrate
```

---

## 📁 Estructura relevante

```
mi-proyecto/
│
├── docker/
│   ├── php-apache/
│   │   ├── Dockerfile
│   │   ├── apache.conf
│   │   ├── php.ini
│   │   └── entrypoint.sh
│
├── docker-compose.yml
├── .env
└── ...
```

---

## 🛠️ Comandos útiles

### Ver contenedores en ejecución

```bash
docker compose ps
```

### Detener contenedores

```bash
docker compose down
```

### Reconstruir contenedores

```bash
docker compose up -d --build
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

## 👨‍💻 Contribución

1. Crear una rama desde `main`
2. Realizar cambios
3. Hacer commit y push
4. Crear Pull Request

---

## 📌 Recomendación

Se recomienda seguir siempre este flujo después de actualizar el proyecto:

```bash
git pull
docker compose exec app composer install
docker compose exec app php artisan migrate
```

---

## 🎯 Objetivo

Este entorno permite que cualquier desarrollador pueda levantar el proyecto rápidamente sin preocuparse por configuraciones locales de PHP, Apache o base de datos.

---

¡Listo! 🎉 Ahora puedes empezar a desarrollar 🚀

