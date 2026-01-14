# Proyecto Clínica - Ejercicio Laravel Avanzado

Este proyecto es una extensión del sistema de historias clínicas, implementando características avanzadas de Laravel como integración de interfaces Legacy con Vite, APIs RESTful, Colas con Redis, Scheduler y Procedimientos Almacenados.

## Requerimientos del Entorno

Es necesario tener instalado xampp para la ejecucion de este proyecto:

* **PHP:** >= 8.2
* **Composer:** Última versión estable.
* **Node.js & NPM:** Usado para la compilacion de assets.
* **MySQL:** >= 8.0
* **Redis Server:** Usado para el manejo de colas y jobs.
* **Extensiones PHP Obligatorias:** `pdo_mysql`, `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `tokenizer`, `xml`.

## Guía de Instalación Automática

El proyecto cuenta con un script de automatización (`init.sh`) que configura el entorno, instala dependencias de PHP y ejecuta las migraciones.

### 1. Ejecutar el Script de Inicialización
Abre una terminal en la raíz del proyecto y ejecuta el comando:

bash init.sh
Este script instalará los paquetes de Composer, creará el archivo .env, configurará la conexión a MySQL, generará la APP_KEY y ejecutará migrate:fresh --seed.

### 2. Instalación de Frontend y Compilación

npm install

► Ejecución del Proyecto
Para que el sistema funcione completamente, mantén corriendo estas terminales:

Compilación: php artisan serve por la configuracion de los assets y npm run build no es necesario mantener una terminal con npm run dev

Módulos y Funcionalidades
### 1. Módulo Legacy (Pacientes)
Integración de la interfaz antigua del sistema HCEO/HCUT utilizando Vite y distribuyendo en carpetas el contenido js y css

URL: http://127.0.0.1:8000/legacy/pacientes

### 2. API RESTful
API pública para consulta de disponibilidad y citas sin autenticación.

→ http://127.0.0.1:8000/api/v1/disponibilidad

parametros usados:
medico_id : (id de medico)
sede_id : principal
fecha : (unicamente acepta la actual o la pasada)

ejemplo de la direccion con params o parametros 
http://127.0.0.1:8000/api/v1/disponibilidad?medico_id=4&sede_id=principal&fecha=2026-01-03

→ http://127.0.0.1:8000/api/v1/citas

headers usados:
Content-Type:application/json
Accept:application/json

body:raw ejemplo para crear la cita
{
    "paciente_id": 1,
    "medico_id": 1,
    "sede_id": 1,
    "fecha": "2026-01-06",
    "hora_inicio": "12:00:00",
    "hora_fin": "12:30:00",
    "motivo": "Consulta General",
    "estado": "pendiente"
}

→ http://127.0.0.1:8000/api/v1/citas

Para listar todas las citas

→ http://127.0.0.1:8000/api/v1/citas/10/cancelar

headers usados:
Content-Type:application/json
Accept:application/json

body:raw usado
{
    "motivo_cancelacion": "El paciente no puede asistir"
}

### 3. Sistema de Recordatorios (Scheduler & Queues)
Envío automatizado de correos para citas del día siguiente.

Scheduler: Configurado en routes/console.php para ejecutarse a las 08:00 PM.

Colas: Usa Redis para procesar los envíos en segundo plano mediante Jobs.

Prueba manual: php artisan queue:work   php artisan schedule:work en terminales distintas 

con redis insight se pueden consultar los datos

cabe recalcar que debera tener citas creadas para el dia siguiente y tener encendido el redis server para ejecutar los envios.
Para visualizar los envios se pueden revisar desde redis insight

### 4. Bitácora de Auditoría
Middleware que registra automáticamente en la tabla bitacora_auditoria cualquier operación POST, PUT o DELETE, guardando el usuario, el módulo y los datos modificados en formato JSON o en procesos en segundo plano se crea mediante comando preestablecidos.