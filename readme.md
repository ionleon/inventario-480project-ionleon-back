
# 480 Project - Backend (Symfony + Docker)


##  Requisitos previos

Antes de empezar, asegúrate de tener instalado:
* [Docker Desktop](https://www.docker.com/products/docker-desktop/)
* [Docker Compose](https://docs.docker.com/compose/install/)

##  Instalación y Despliegue

Sigue estos pasos para levantar el entorno local:

### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio>

````

### 2. Configurar variables de entorno

Crea una copia del archivo de ejemplo y configúralo (si es necesario):



```Bash
cp .env .env.local
```

_Nota: Por defecto, el archivo `.env` ya viene configurado para funcionar con los contenedores de Docker._

### 3. Levantar los contenedores

Ejecuta el siguiente comando para construir y levantar los servicios (Base de datos, PHP y Nginx):



```Bash
docker-compose up -d --build
```

### 4. Instalar dependencias de PHP



```Bash
docker-compose exec php composer install
```

### 5. Generar las claves JWT

Para que la autenticación funcione, es necesario generar el par de claves (pública/privada):



```Bash
docker-compose exec php bin/console lexik:jwt:generate-keypair
```

### 6. Configurar la Base de Datos

Crea la base de datos y ejecuta las migraciones:



```Bash
docker-compose exec php bin/console doctrine:database:create --if-not-exists
docker-compose exec php bin/console doctrine:migrations:migrate 
```

### 7. Cargar datos por defecto

Se ha utilizado la libreria **DataFixtures** para crear informacion por defecto en la base de datos, una vez realizada la migración ejecuta el siguiente comando:

```Bash
docker-compose exec php bin/console doctrine:fixtures:load 
```

---

##  Información de la API

- **URL Base:** `http://localhost:8000`

- **Prefijo de rutas:** `/480project`

- **Endpoint de Login:** `POST http://localhost:8000/480project/login`


### Ejemplo de petición de Login (POST)

JSON

```
{
    "email": "usuario@ejemplo.com",
    "password": "tu_password"
}
```

##  Comandos útiles de Docker

- **Ver logs en tiempo real:** `docker-compose logs -f`

- **Reiniciar contenedores:** `docker-compose restart`

- **Detener contenedores:** `docker-compose down`

- **Entrar al terminal de PHP:** `docker-compose exec php bash`


##  Solución de problemas (CORS)

Si el frontend (React) recibe errores de CORS, asegúrate de que el archivo `.env.local` permite el origen de tu servidor de desarrollo: `CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'`
