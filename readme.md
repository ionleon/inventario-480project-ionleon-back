
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

### Ejecutar script deploy.sh

```Bash
./deploy.sh
```

#### El script configura el resto de pasos necesarios


### 2. Configurar variables de entorno




#### Crea una copia del archivo de ejemplo y configúralo (si es necesario):



```Bash
cp .env .env.local
```
#### Una vez creado el .env.local sustituye las variables por las siguientes

```
DATABASE_URL="postgresql://user_admin:skibidiman123@database:5432/project_inventory_480_db?serverVersion=18&charset=utf8"

JWT_PASSPHRASE=a35eab0d39a99076c5f8747bc553c0df86693e150a22e465987f288d610bdef3

APP_SECRET=cf313f261c7cd660b5b066cb67962665
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



```JSON
{
    "email": "usuario@ejemplo.com",
    "password": "tu_password"
}
```

```bash
-------------------------------------------------------
Prueba de autenticacion (Copia y pega):

curl -X POST http://localhost:8000/480project/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@example.com", "password":"password1234"}'
-------------------------------------------------------
```


##  Comandos útiles de Docker

- **Ver logs en tiempo real:** `docker-compose logs -f`

- **Reiniciar contenedores:** `docker-compose restart`

- **Detener contenedores:** `docker-compose down`

- **Entrar al terminal de PHP:** `docker-compose exec php bash`


##  Solución de problemas (CORS)

Si el frontend (React) recibe errores de CORS, asegúrate de que el archivo `.env.local` permite el origen de tu servidor de desarrollo: `CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'`
