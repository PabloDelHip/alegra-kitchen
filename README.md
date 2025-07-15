
# ALEGRA PRUEBA — Sistema de Gestión de Jornadas Gratuitas  

## 💥 Reto  

💥 **Jornada de almuerzo gratuita**  
Un reconocido restaurante ha decidido tener una jornada de donación de comida a los residentes de la región, con la única condición de que el plato que obtendrán los comensales será **aleatorio**.  

El administrador del restaurante requiere **con urgencia** un sistema que permita enviar pedidos a la cocina y gestionar la operación de forma eficiente.  

---

## 🏗️ Arquitectura General del Sistema  

La solución fue diseñada bajo el enfoque de **microservicios desacoplados**, donde cada uno cumple un rol específico del negocio y se comunica mediante eventos a través de **RabbitMQ**.  

### 🔹 Componentes principales:
- **API Gateway**  
  Punto de entrada único, encargado de validar JWT, controlar el tráfico mediante Rate Limiting y aplicar políticas de CORS.  
  Redirige las solicitudes al microservicio correspondiente o las publica en las colas según sea el flujo.  

- **RabbitMQ como Sistema de Mensajería**  
  Administra los eventos del negocio, permitiendo comunicación asíncrona y desacoplada.  
  Implementa:  
  - **Colas de trabajo** para la comunicación normal  
  - **Dead Letter Queues (DLQ)** para mensajes fallidos  
  - **Manejo de reintentos automáticos**  

- **WebSockets vía Laravel Reverb**  
  Encargado de la transmisión en tiempo real de eventos al frontend, asegurando que las órdenes, notificaciones e inventarios se actualicen de manera instantánea.  

---

## 🧩 Microservicios Implementados  

| Microservicio   | Función                                                              | Comunicación    | Base de Datos |
|-----------------|---------------------------------------------------------------------|-----------------|---------------|
| **API Gateway** | Punto de entrada, controla autenticación, CORS, Rate Limit, y envía eventos a RabbitMQ | HTTP / RabbitMQ | - |
| **Users Service** | Gestión de usuarios, autenticación, control de acceso y JWT        | HTTP        | MySQL |
| **Kitchen Service** | Gestión de órdenes, platillos y su estado (en preparación, listo, fallido) | HTTP/RabbitMQ | MySQL |
| **Warehouse Service** | Control de inventario, abastecimiento y compras                 | HTTP/RabbitMQ        | MySQL |
| **Notifier Service** | Envía notificaciones al frontend mediante WebSockets            | RabbitMQ        | - |
| **Agent Service** | Analiza inventario, compras y órdenes para generar recomendaciones automáticas | HTTP | - |

---

## 🔄 Flujo de Eventos y Comunicación  

1️⃣ El **usuario** inicia sesión a través del **API Gateway**, que valida las credenciales contra el **Users Service** usando JWT.  

2️⃣ Las **órdenes** generadas por los usuarios son enviadas a través del **API Gateway** y publicadas en la cola correspondiente de **RabbitMQ**, que luego consume el **Kitchen Service**.  

3️⃣ El **Kitchen Service** procesa las órdenes, solicita los ingredientes al **Warehouse Service** y responde con éxito o fallo, dependiendo de la disponibilidad.  

4️⃣ Cuanto un platillo es finalizado, el **Kitchen Service** lo registra, ya sea un platillo completo o incompleto  y la comunica mediante eventos a **Notifier Service**, que emite una notificación en tiempo real al frontend.  

5️⃣ El **Agent Service** no trabaja con eventos, sino que es consultado mediante **HTTP** a través del **API Gateway** cada vez que se requiere un análisis.  
Cuando recibe una solicitud, consulta directamente al **Kitchen Service** y al **Warehouse Service** para obtener la información actualizada y generar las recomendaciones que luego son mostradas al usuario. 

---

## 🛡️ Seguridad y Control  

El **API Gateway** garantiza:  
- Validación y renovación de **tokens JWT**  
- **Políticas CORS** dinámicas  
- Aplicación de **Rate Limiting** para evitar abusos  
- Middleware de autorización conectado al **Users Service**  

---

## 🖥️ Vista destacada — Acciones Rápidas  

La **pantalla de Acciones Rápidas** permite ejecutar las dos funciones estratégicas del sistema:  

- **Análisis del negocio con IA (Agent Service)**  
  Genera recomendaciones con base en las compras, inventario y pedidos previos.  

- **Envío de pedidos a la cocina (Kitchen Service)**  
  Lanza las órdenes al flujo operativo, que son gestionadas y mostradas en tiempo real.  

| Vista | Descripción |
|-------|-------------|
| ![Acciones rápidas](https://i.postimg.cc/QtfxzNmy/Captura-desde-2025-07-14-21-41-48.png) | Acceso a las funciones de análisis automatizado y gestión de pedidos |

---
# 🛠️ Stack Tecnológico y Arquitectura Interna  

## ⚙️ Tecnologías Utilizadas  

| Tecnología        | Versión                 | Uso                                                    |
|-------------------|--------------------------|--------------------------------------------------------|
| **PHP**          | 8.2                      | Backend y microservicios                              |
| **NODE JS**          | 22.14                      | Agente IA                              |
| **Laravel**      | 12                       | Framework para todos los servicios                    |
| **MySQL**        | 8.2                      | Base de datos relacional (montada en Docker)          |
| **RabbitMQ**     | 3.x                      | Sistema de mensajería (montado en Docker)             |
| **Vue.js**       | 3                        | Frontend                                              |
| **Bootstrap**    | 5   | Framework CSS para diseño responsive                  |
| **Laravel Reverb** | -                      | Comunicación en tiempo real (WebSockets)              |
| **Docker Compose** | -                      | Orquestación de contenedores para desarrollo y producción |

---

## 🏗️ Estructura Interna y Patrones  

Todos los microservicios comparten la misma estructura para mantener la coherencia en el desarrollo:  

- **Controllers** — Manejo de las peticiones HTTP o eventos  
- **Services** — Contienen la lógica de negocio principal  
- **Repositories** — Abstracción de acceso a la base de datos  
- **Event Handlers** — Para la suscripción y procesamiento de eventos de RabbitMQ  

### 🔹 Patrones y Principios Aplicados  
- **Repository Pattern**  
- **Event-Driven Design**  
- **Service-Oriented Design (SOD)**  
- **Resiliencia mediante DLQ y reintentos**  
- **Responsabilidad Única (SRP)** en controladores y servicios  

---

## 🗄️ Bases de Datos y Migraciones  

Los siguientes microservicios manejan su propia base de datos **MySQL** para garantizar el principio de independencia y evitar acoplamientos:  

| Microservicio     | Base de Datos |
|-------------------|----------------|
| **Users Service** | MySQL         |
| **Kitchen Service** | MySQL       |
| **Warehouse Service** | MySQL     |

### 🔹 Migraciones y Seeds  
- Se implementaron migraciones con Laravel para cada microservicio  
- **Kitchen Service**: Seeds para cargar platillos y sus ingredientes  
- **Warehouse Service**: Seeds para registrar los ingredientes disponibles y el inventario inicial  

---

## 🖥️ Frontend y Librerías  

El frontend fue construido con **Vue 3**, diseñado para interactuar directamente con el **API Gateway**, garantizando seguridad y consistencia.  

Se utilizaron:  
- **Bootstrap** para un diseño adaptable y moderno  
- **Axios** para la comunicación con los endpoints  
- **WebSockets ** para recibir actualizaciones en tiempo real desde los microservicios  

---

## 🚀 Despliegue y Orquestación  

La solución fue desplegada utilizando **Docker Compose**, tanto en entorno de desarrollo como en producción.  

- Todos los servicios corren en contenedores Docker  
- **MySQL** y **RabbitMQ** también fueron levantados como servicios internos  
- El entorno de producción fue configurado y desplegado en una instancia **AWS EC2**  

🔗 **Acceso al sistema**:  
[http://3.19.250.16/](http://3.19.250.16/)  

---

## 🗝️ Usuario Demo  

```json
{
  "email": "pablo@example.com",
  "password": "12345678"
}
```

---

# 🖥️ Vistas Principales del Sistema y Funcionalidad  

El sistema cuenta con un **panel administrativo desarrollado en Vue 3**, el cual ofrece un control completo de las operaciones internas del restaurante y el monitoreo en tiempo real de las órdenes.  

Cada vista está conectada a uno o varios microservicios a través del **API Gateway**, comunicándose por HTTP o WebSockets, según corresponda.  

---

## 🔹 Vista 1 — Acciones Rápidas  

| Vista | Descripción |
|-------|-------------|
| ![Acciones rápidas](https://i.postimg.cc/QtfxzNmy/Captura-desde-2025-07-14-21-41-48.png) | Pantalla de inicio donde el usuario puede:<br>• **Ejecutar el análisis del negocio (IA)** — Solicita recomendaciones al **Agent Service** basadas en inventario, compras y pedidos anteriores.<br>• **Enviar a cocina** — Procesa pedidos al **Kitchen Service**, iniciando el flujo de preparación y gestión de órdenes. |

---

## 🔹 Vista 2 — Resumen General (Pedidos e Inventario)  

| Vista | Descripción |
|-------|-------------|
| ![Resumen](https://i.postimg.cc/x8qTVrFT/Captura-desde-2025-07-14-21-44-40.png) | Pantalla que muestra:<br>• **Últimos 20 pedidos realizados** (lado izquierdo)<br>• **Estado actual del inventario** (lado derecho)<br>• Posibilidad de **realizar compras directas** desde esta vista hacia el **Warehouse Service** |

---

## 🔹 Vista 3 — Órdenes en Tiempo Real (Kanban)  

| Vista | Descripción |
|-------|-------------|
| ![Kanban](https://i.postimg.cc/76HZXJ3z/Captura-desde-2025-07-14-21-46-00.png) | Tablero **Kanban** que refleja en tiempo real:<br>• Órdenes en estado **pendiente**, **en preparación**, **listas** o **fallidas**<br>• Si una orden falla, se muestra el ingrediente faltante que provocó el error<br>• Funcionalidad de **actualización manual** para refrescar el estado de las órdenes<br>Esta vista está directamente conectada al **Kitchen Service** y al **Notifier Service** mediante WebSockets. |

---

## 🔹 Vista 4 — Inventario de Bodega  

| Vista | Descripción |
|-------|-------------|
| ![Inventario Bodega](https://i.postimg.cc/J4vtmn8w/Captura-desde-2025-07-14-21-47-36.png) | Muestra el estado actual del inventario gestionado por el **Warehouse Service** y permite realizar compras para abastecer la bodega. |

---
## 🔹 Vista 5 — Historial de Compras  

| Vista | Descripción |
|-------|-------------|
| ![Historial de Compras](https://i.postimg.cc/cL3CJS1v/Captura-desde-2025-07-14-21-48-13.png) | Esta vista muestra:<br>• **Historial completo de compras por ingrediente**<br>• **Última fecha de compra** y **total comprado en el día**<br>• Filtro para consultar registros específicos<br>• Interacción directa con el **Warehouse Service** |

---

## 🔹 Vista 6 — Recetas de Cocina  

| Vista | Descripción |
|-------|-------------|
| ![Recetas de Cocina](https://i.postimg.cc/5tM0YRjn/Captura-desde-2025-07-14-21-49-29.png) | Visualización de todas las recetas registradas en el sistema, conectadas al **Kitchen Service**:<br>• Nombre del platillo<br>• Ingredientes requeridos<br>• Estado general de la receta |

---

## 🔹 Vista 7 — Historial de Órdenes  

| Vista | Descripción |
|-------|-------------|
| ![Historial de Órdenes](https://i.postimg.cc/VNBNjWC1/Captura-desde-2025-07-14-21-51-09.png) | Registro histórico de las órdenes procesadas:<br>• Estado final de cada orden (**listo**, **parcial**, **fallido**)<br>• Platillos incluidos en cada orden<br>• Motivo del estado cuando aplica (por ejemplo, si algún ingrediente faltó)<br>Conectada al **Kitchen Service** y al **Notifier Service**. |

---

## 🔹 Vista 8 — Análisis y Métricas  

| Vista | Descripción |
|-------|-------------|
| ![Análisis y Métricas](https://i.postimg.cc/xTcqWmWy/Captura-desde-2025-07-14-21-52-46.png) | Visualización de gráficas de análisis:<br>• **Ingredientes más usados** (izquierda)<br>• **Platillos más preparados** (derecha)<br> |

---

# 📝 Consideraciones Finales  

La solución presentada integra:  
- Arquitectura de microservicios con comunicación basada en eventos  
- Gestión desacoplada y resiliente de los procesos del restaurante  
- Flujo de trabajo en tiempo real con WebSockets y RabbitMQ  
- Interfaz moderna y amigable desarrollada en Vue 3  
- Despliegue automatizado en entornos Docker y AWS EC2  

La aplicación está preparada para escalar y adaptarse al crecimiento del negocio, asegurando una operación fluida durante jornadas de alta demanda.  

---

