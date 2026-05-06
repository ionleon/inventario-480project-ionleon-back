#!/bin/bash

# Colores para la terminal (opcional, para legibilidad)
CYAN='\033[0;36m'
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${CYAN}Iniciando despliegue de Proyecto Inventory 480...${NC}"

# 1. Configurar variables de entorno (Paso 2)
if [ ! -f .env.local ]; then
    echo "Paso 2: Creando .env.local..."
    cp .env .env.local

# Definición de variables
    DB_URL="postgresql://user_admin:skibidiman123@database:5432/project_inventory_480_db?serverVersion=18&charset=utf8"
    JWT_PASS="a35eab0d39a99076c5f8747bc553c0df86693e150a22e465987f288d610bdef3"
    SECRET="cf313f261c7cd660b5b066cb67962665"
    REDIS_URL="redis://redis:6379"

    # Inyectar variables usando sed (reemplaza o añade si no existen)
    # Usamos '|' como separador en sed porque la DB_URL contiene '/'
    sed -i "s|^#\?DATABASE_URL=.*|DATABASE_URL=\"$DB_URL\"|" .env.local
    sed -i "s|^#\?JWT_PASSPHRASE=.*|JWT_PASSPHRASE=$JWT_PASS|" .env.local
    sed -i "s|^#\?APP_SECRET=.*|APP_SECRET=$SECRET|" .env.local
    sed -i "s|^#\?REDIS_URL=.*|REDIS_URL=$REDIS_URL|" .env.local
else
    echo "Paso 2: El archivo .env.local ya existe, se omiten los cambios."
fi

# 2. Levantar los contenedores (Paso 3)
echo "Paso 3: Construyendo y levantando servicios (Postgres, PHP, Nginx)..."
docker-compose up -d --build

# Espera de seguridad para asegurar que la base de datos acepta conexiones
echo "Esperando a que la base de datos este lista..."
sleep 5

# 3. Instalar dependencias de PHP (Paso 4)
echo "Paso 4: Instalando dependencias mediante Composer..."
docker-compose exec php composer install --no-interaction

# 4. Generar las claves JWT (Paso 5)
echo "Paso 5: Generando par de claves para LexikJWT..."
docker-compose exec php bin/console lexik:jwt:generate-keypair --skip-if-exists

# 5. Configurar la Base de Datos (Paso 6)
echo "Paso 6: Creando base de datos y ejecutando migraciones..."
docker-compose exec php bin/console doctrine:database:create --if-not-exists --no-interaction
docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

# 6. Cargar datos por defecto (Paso 7)
echo "Paso 7: Cargando fixtures en la base de datos..."
docker-compose exec php bin/console doctrine:fixtures:load --no-interaction

echo -e "${GREEN}Despliegue completado con exito.${NC}"
echo "-------------------------------------------------------"
echo "URL Base: http://localhost:8000"
echo "Prefijo de rutas: /480project"
echo "Endpoint de Login: POST http://localhost:8000/480project/login"
echo "-------------------------------------------------------"

echo "-------------------------------------------------------"
echo "Prueba de autenticacion (Copia y pega):"
echo ""
echo "curl -X POST http://localhost:8000/480project/login \\"
echo "     -H \"Content-Type: application/json\" \\"
echo "     -d '{\"email\":\"admin@example.com\", \"password\":\"password1234\"}'"
echo "-------------------------------------------------------"
