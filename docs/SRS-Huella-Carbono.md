# ESPECIFICACIÓN DE REQUISITOS DE SOFTWARE (SRS)  
## Sistema Web - Huella de Carbono

---

# FICHA DEL DOCUMENTO

| Campo | Descripción |
|-------|-------------|
| **Nombre del documento** | Especificación de Requisitos de Software - Sistema Web Huella de Carbono |
| **Cliente** | Centro de Formación Agroindustrial La Angostura |
| **Proyecto** | Sistema Web - Huella de Carbono |
| **Software** | Laravel + MySQL |
| **Versión del documento** | 1.0 |
| **Fecha** | 2026 |
| **Módulo** | HUELLACARBONO |

---

# CONTENIDO

1. [Introducción](#1-introducción)  
   1.1 [Objetivo general](#11-objetivo-general)  
   1.2 [Propósito](#12-propósito)  
   1.3 [Alcance](#13-alcance)  
   1.4 [Personal involucrado](#14-personal-involucrado)  
   1.5 [Definiciones, acrónimos y abreviaturas](#15-definiciones-acrónimos-y-abreviaturas)  
   1.6 [Referencias](#16-referencias)  
   1.7 [Resumen](#17-resumen)  
2. [Descripción general](#2-descripción-general)  
   2.1 [Perspectiva del producto](#21-perspectiva-del-producto)  
   2.2 [Funcionalidad del producto](#22-funcionalidad-del-producto)  
   2.3 [Características de los usuarios](#23-características-de-los-usuarios)  
   2.4 [Restricciones](#24-restricciones)  
   2.5 [Suposiciones y dependencias](#25-suposiciones-y-dependencias)  
   2.6 [Evolución posible del sistema](#26-evolución-posible-del-sistema)  
3. [Requisitos funcionales específicos](#3-requisitos-funcionales-específicos)  
   3.1 [Requisitos funcionales del Administrador](#31-requisitos-funcionales-del-administrador)  
   3.2 [Requisitos funcionales del Líder](#32-requisitos-funcionales-del-líder)  
   3.3 [Requisitos funcionales del Visitante](#33-requisitos-funcionales-del-visitante)  
   3.4 [Requisitos no funcionales](#34-requisitos-no-funcionales)  

---

# 1. INTRODUCCIÓN

## 1.1 Objetivo general

Definir los requisitos de software del **Sistema Web Huella de Carbono** del Centro de Formación Agroindustrial La Angostura, de forma que sirva como referencia para el desarrollo, las pruebas y la validación del módulo de registro, seguimiento y reporte de la huella de carbono institucional.

## 1.2 Propósito

Este documento tiene como propósito:

- Establecer una especificación clara y verificable de las funcionalidades del sistema.
- Servir como contrato de requisitos entre el cliente (Centro de Formación La Angostura) y el equipo de desarrollo.
- Facilitar la trazabilidad entre requisitos, historias de usuario y pruebas.
- Permitir que administradores, líderes de unidad y visitantes comprendan el alcance y el uso del sistema.

## 1.3 Alcance

El sistema es un **módulo web** integrado en una aplicación Laravel existente, que permite:

- **Registro de consumos** por unidad productiva y factor de emisión, con cálculo automático de CO₂.
- **Gestión administrativa** de unidades productivas, factores de emisión, usuarios, roles, consumos y solicitudes de registro.
- **Panel para líderes** de cada unidad: registro diario, historial, alertas (días sin reporte) y solicitud de registro para fechas pasadas (sujeta a aprobación).
- **Contenido público**: información del proyecto, calculadora personal de huella de carbono y estadísticas públicas.
- **Reportes y exportación** a PDF y Excel por período.

Quedan fuera del alcance de este SRS: otros módulos de la aplicación (p. ej. SICA), la infraestructura de autenticación global y el diseño detallado de base de datos (se asume ya implementado).

## 1.4 Personal involucrado

| Rol | Descripción |
|-----|-------------|
| **Cliente / Usuario final** | Centro de Formación Agroindustrial La Angostura (administradores, líderes de unidad, aprendices, comunidad). |
| **Administrador del sistema** | Usuario con rol Admin del módulo Huella de Carbono; configura y supervisa el módulo (p. ej. instructor o personal del centro). |
| **Líder de unidad** | Aprendiz con cuenta en SICEFA asignado como líder de una unidad productiva; registra consumos y gestiona datos de su área. No son instructores. |
| **Equipo de desarrollo** | Responsable del diseño, implementación y mantenimiento del software. |
| **Instructor / Responsable técnico** | Acompaña el proceso formativo y la entrega del sistema. |

## 1.5 Definiciones, acrónimos y abreviaturas

| Término | Definición |
|---------|------------|
| **CO₂** | Dióxido de carbono; se expresa en kg CO₂ como unidad de huella de carbono. |
| **Factor de emisión** | Coeficiente que permite convertir una cantidad de consumo (p. ej. kWh, litros) en kg CO₂ equivalente. |
| **Huella de carbono** | Medida del impacto en emisiones de gases de efecto invernadero (en este sistema, en kg CO₂). |
| **Unidad productiva** | Área o centro de costos del Centro de Formación que reporta consumos (ej.: granja, planta, oficina). |
| **Líder de unidad** | Aprendiz con cuenta en SICEFA al que el Administrador asigna una unidad productiva; es el usuario autorizado para registrar consumos de esa unidad. No es instructor. |
| **Solicitud de registro** | Petición enviada por un líder (aprendiz) para registrar consumos en una fecha pasada; requiere aprobación del Administrador. |
| **Admin** | Rol de administrador del módulo Huella de Carbono (gestión completa); suele corresponder a instructor o personal del centro. |
| **SICEFA** | Sistema de información del Centro de Formación; los aprendices tienen cuenta en SICEFA y desde allí se gestionan usuarios que pueden ser asignados como Líder en el módulo Huella de Carbono. |
| **SICA** | Módulo o sistema de información del Centro (gestión de usuarios/roles de la aplicación). |
| **SRS** | Especificación de Requisitos de Software (Software Requirements Specification). |

## 1.6 Referencias

- Historias de usuario del módulo Huella de Carbono (docs/historias-de-usuario-huella-carbono.md).
- Matriz de stakeholders (docs/matriz-stakeholders-huella-carbono.md).
- Diagramas UML del proyecto: casos de uso, clases, secuencias, componentes, despliegue (docs/*.puml).
- Documentación del framework Laravel y del paquete nwidart/laravel-modules.
- Normativa o metodología de cálculo de huella de carbono que aplique el Centro de Formación (si existe).

## 1.7 Resumen

El Sistema Web Huella de Carbono permite al Centro de Formación La Angostura registrar, consolidar y reportar la huella de carbono por unidades productivas. Los **administradores** configuran unidades y factores, gestionan usuarios y consumos, y generan reportes; los **líderes** registran consumos diarios de su unidad y pueden solicitar registros en fechas pasadas; los **visitantes** consultan información y usan la calculadora personal. El sistema está desarrollado en Laravel + MySQL como módulo integrado y se despliega en un entorno tipo Laragon (Apache/Nginx + PHP + MySQL).

---

# 2. DESCRIPCIÓN GENERAL

## 2.1 Perspectiva del producto

El Sistema Web Huella de Carbono es un **módulo** de una aplicación web Laravel más amplia. No es un producto independiente: depende de la autenticación, usuarios y roles proporcionados por la aplicación principal (y en su caso por el módulo SICA). El módulo aporta:

- Rutas bajo el prefijo `/huellacarbono/` (públicas, admin y líder).
- Entidades propias (unidades productivas, factores de emisión, consumos diarios, solicitudes de registro, calculadora personal, notificaciones).
- Vistas Blade, controladores y exportación a PDF/Excel.

La interfaz es web; los usuarios acceden mediante navegador. No existe aplicación móvil nativa dentro del alcance de este SRS.

## 2.2 Funcionalidad del producto

- **Gestión de unidades productivas**: creación, edición, activación/desactivación y asignación de líder por unidad.
- **Gestión de factores de emisión**: creación, edición y activación/desactivación de factores con unidad de medida y coeficiente de CO₂ (opcionalmente con porcentaje de nitrógeno).
- **Registro de consumos diarios**: por unidad, factor, fecha, cantidad y opcionales (porcentaje N, observaciones); cálculo automático de CO₂.
- **Gestión de usuarios y roles**: asignación de roles Líder o Admin del módulo a usuarios de la aplicación.
- **Consultas y filtros**: listado de consumos con filtros por unidad y rango de fechas; orden por fecha e ID (más recientes primero).
- **Edición y eliminación de consumos**: por parte del Administrador; el Líder puede editar consumos de su propia unidad.
- **Reportes**: consulta por rango de fechas, totales y desglose por unidad y por factor; exportación a PDF y Excel.
- **Gráficas**: tendencias y distribución por período (semanal, mensual, trimestral, anual) y opcionalmente por unidad.
- **Solicitudes de registro**: el Líder puede solicitar el registro de consumos para fechas pasadas; el Administrador aprueba o rechaza y, al aprobar, se crean los registros de consumo correspondientes.
- **Alertas**: visualización de días recientes sin reporte por unidad (para el Líder).
- **Contenido público**: página de inicio, información del proyecto, listado de factores activos, calculadora personal de huella y estadísticas públicas agregadas.
- **Redirección por rol**: al acceder al módulo, el usuario es redirigido al panel de Líder, al de Admin o a la vista pública según su rol.

## 2.3 Características de los usuarios

| Tipo de usuario | Características |
|-----------------|-----------------|
| **Administrador** | Suele ser instructor o personal del centro; conoce el proceso de huella de carbono; necesita configurar unidades, factores y usuarios, auditar datos y generar reportes oficiales (PDF/Excel). |
| **Líder de unidad** | Es un **aprendiz** con cuenta en SICEFA asignado como líder de una unidad; conoce los consumos de su área, debe registrar datos con frecuencia (idealmente diaria) y necesita alertas y la posibilidad de regularizar fechas pasadas vía solicitud. No son instructores. |
| **Visitante / Aprendiz** | Puede ser aprendiz sin rol Líder u otro usuario; puede no estar autenticado o no tener rol en el módulo; consulta información y calculadora; no modifica datos operativos. |

## 2.4 Restricciones

- El módulo **no modifica** la estructura de tablas ni la lógica de otros módulos (p. ej. SICA); solo puede leer usuarios/roles según la integración definida.
- Los **roles** del módulo (Líder, Admin) se gestionan dentro del mismo módulo o vía la aplicación principal; no se definen nuevos sistemas de autenticación en este SRS.
- La **calculadora personal** utiliza factores y fórmulas propias del módulo; no sustituye la metodología oficial del centro si esta existe por separado.
- El despliegue asume un entorno **PHP** (p. ej. 8.x) compatible con Laravel y **MySQL** como base de datos.
- La interfaz está pensada para uso en **navegador de escritorio**; la usabilidad en móvil puede ser limitada si no se ha definido diseño responsive específico.

## 2.5 Suposiciones y dependencias

- Existe una **aplicación Laravel** en funcionamiento con autenticación (login, sesión, usuarios).
- Los **usuarios** (incluidos los aprendices con cuenta en SICEFA) y, en su caso, **roles** provienen de la aplicación o del módulo SICA; el módulo HUELLACARBONO asigna roles propios (Líder, Admin) sobre esos usuarios. Los **Líderes** son aprendices con cuenta en SICEFA, no instructores.
- La **base de datos** dispone de las tablas del módulo (hc_productive_units, hc_emission_factors, hc_daily_consumptions, etc.) y de las tablas de usuarios/roles necesarias.
- El **nombrado de rutas** y el prefijo `/huellacarbono/` se mantienen según la implementación actual.
- Se asume que el cliente tiene **navegadores** actualizados y conexión a la red donde se aloja la aplicación.

## 2.6 Evolución posible del sistema

- Inclusión de **más factores de emisión** o fórmulas de cálculo sin cambiar la estructura general.
- **Mapa de calor** o visualización geográfica si se incorporan coordenadas u otra información espacial.
- **Notificaciones** (correo o en aplicación) al aprobar/rechazar solicitudes o al detectar días sin reporte.
- **API REST** para integración con otros sistemas o para aplicaciones móviles futuras.
- **Indicadores de cumplimiento** o metas de reducción de huella por unidad o por período.
- **Auditoría** detallada de cambios en consumos (quién y cuándo modificó).

---

# 3. REQUISITOS FUNCIONALES ESPECÍFICOS

## 3.1 Requisitos funcionales del Administrador

| ID | Requisito | Descripción |
|----|-----------|-------------|
| RF-ADM-01 | Gestión de unidades productivas | El sistema debe permitir crear, editar, listar, activar y desactivar unidades productivas, y asignar o quitar líder por unidad. |
| RF-ADM-02 | Gestión de factores de emisión | El sistema debe permitir crear, editar, listar, activar y desactivar factores de emisión (nombre, código, unidad, factor CO₂, porcentaje de nitrógeno opcional). |
| RF-ADM-03 | Gestión de usuarios y roles | El sistema debe permitir asignar a usuarios (aprendices con cuenta en SICEFA u otros) los roles Líder o Admin del módulo Huella de Carbono (o retirar el acceso). Los Líderes son aprendices asignados por unidad, no instructores. |
| RF-ADM-04 | Consulta de consumos | El sistema debe permitir listar todos los registros de consumo con filtros por unidad productiva y rango de fechas, ordenados por fecha e ID descendente. |
| RF-ADM-05 | Edición de consumos | El sistema debe permitir al Administrador editar cantidad, porcentaje de nitrógeno y observaciones de cualquier registro de consumo. |
| RF-ADM-06 | Eliminación de consumos | El sistema debe permitir al Administrador eliminar registros de consumo; el registro debe dejar de contabilizarse en totales y reportes. |
| RF-ADM-07 | Reportes por período | El sistema debe permitir consultar reportes por rango de fechas con total de registros, total CO₂ y desglose por unidad y por factor. |
| RF-ADM-08 | Exportación PDF | El sistema debe permitir exportar el reporte del período seleccionado en formato PDF. |
| RF-ADM-09 | Exportación Excel | El sistema debe permitir exportar el reporte del período seleccionado en formato Excel. |
| RF-ADM-10 | Gráficas y análisis | El sistema debe ofrecer gráficas de tendencia y distribución por unidad y por factor, con selección de tipo de vista (semanal, mensual, trimestral, anual) y filtro opcional por unidad. |
| RF-ADM-11 | Solicitudes de registro | El sistema debe permitir listar las solicitudes de registro enviadas por los líderes, con estado (pendiente, aprobada, rechazada) y detalle de ítems. |
| RF-ADM-12 | Aprobar solicitud | El sistema debe permitir aprobar una solicitud pendiente; al aprobar, deben crearse los registros de consumo indicados en la solicitud y la solicitud debe pasar a estado aprobada. |
| RF-ADM-13 | Rechazar solicitud | El sistema debe permitir rechazar una solicitud pendiente indicando motivo; la solicitud debe pasar a estado rechazada y no deben crearse consumos. |
| RF-ADM-14 | Dashboard | El sistema debe mostrar al Administrador un panel con resumen de unidades, consumos recientes, totales por período y estado de solicitudes pendientes. |

## 3.2 Requisitos funcionales del Líder

*El Líder es un **aprendiz** con cuenta en SICEFA al que el Administrador asigna una unidad productiva; no es instructor.*

| ID | Requisito | Descripción |
|----|-----------|-------------|
| RF-LID-01 | Dashboard de unidad | El sistema debe mostrar al Líder (aprendiz asignado) un panel con el total de CO₂ de la semana actual y los últimos registros de su unidad asignada. |
| RF-LID-02 | Registro de consumo | El sistema debe permitir al Líder registrar consumos diarios para su unidad: fecha, factor de emisión, cantidad y opcionales (porcentaje N, observaciones); el sistema debe calcular y almacenar el CO₂ generado. |
| RF-LID-03 | Registro múltiple | El sistema debe permitir registrar varios factores en una misma fecha en una sola operación (múltiples líneas); no debe permitir duplicar factor y fecha para la misma unidad. |
| RF-LID-04 | Historial | El sistema debe permitir consultar el historial de consumos de la unidad del Líder, ordenado por fecha e ID descendente, con indicación de registros en “retraso” (aprobados por Admin) si aplica. |
| RF-LID-05 | Edición de consumo propio | El sistema debe permitir al Líder editar cantidad y observaciones de los consumos de su unidad dentro de las reglas definidas. |
| RF-LID-06 | Alertas | El sistema debe mostrar al Líder los días recientes (p. ej. últimos 7 días) en los que no hay registro de consumo para su unidad. |
| RF-LID-07 | Mis solicitudes | El sistema debe permitir al Líder ver el listado de sus solicitudes de registro con estado (pendiente, aprobada, rechazada) y detalle de ítems. |
| RF-LID-08 | Solicitar registro (fecha pasada) | El sistema debe permitir al Líder crear una solicitud de registro para una fecha pasada con uno o más ítems (factor, cantidad, porcentaje N); la solicitud debe quedar en estado pendiente hasta que el Administrador la apruebe o rechace. |
| RF-LID-09 | Estadísticas de unidad | El sistema debe mostrar al Líder totales y desgloses (por factor, por período) solo de los datos de su unidad. |
| RF-LID-10 | Gráficas de unidad | El sistema debe mostrar al Líder gráficas de tendencia y distribución solo con datos de su unidad. |
| RF-LID-11 | Sin unidad asignada | Si el usuario tiene rol Líder pero no tiene unidad asignada, el sistema debe redirigir con un mensaje indicando que no tiene unidad asignada. |

## 3.3 Requisitos funcionales del Visitante

| ID | Requisito | Descripción |
|----|-----------|-------------|
| RF-VIS-01 | Página de inicio | El sistema debe mostrar una página de inicio pública con enlaces a Información, Calculadora personal, Estadísticas públicas y Desarrolladores (o equivalentes). |
| RF-VIS-02 | Información del proyecto | El sistema debe ofrecer una sección de información sobre el proyecto de huella de carbono y, si aplica, listado de factores de emisión activos. |
| RF-VIS-03 | Calculadora personal | El sistema debe permitir al visitante ingresar datos de consumo (según los campos definidos) y calcular una estimación de huella de carbono personal; debe mostrar el resultado en kg CO₂ y opcionalmente guardar el cálculo. |
| RF-VIS-04 | Estadísticas públicas | El sistema debe mostrar estadísticas agregadas del centro (totales y/o gráficas) sin requerir autenticación ni rol en el módulo. |
| RF-VIS-05 | Redirección por rol | Si el usuario está autenticado con rol Líder, el sistema debe redirigirlo al panel del Líder al acceder al módulo; si tiene rol Admin (y no Líder), al panel Admin; si no tiene ninguno de estos roles, debe ver la vista pública. |

## 3.4 Requisitos no funcionales

| ID | Requisito | Descripción |
|----|-----------|-------------|
| RNF-01 | Seguridad | El acceso a las secciones de Administrador y Líder debe estar restringido según el rol asignado; las acciones sensibles (editar, eliminar, aprobar/rechazar) deben validar permisos en el servidor. |
| RNF-02 | Usabilidad | La interfaz debe ser clara y coherente; los formularios deben validar datos y mostrar mensajes de error comprensibles. |
| RNF-03 | Integridad de datos | No debe permitirse duplicar registro de consumo para la misma unidad, factor y fecha; el cálculo de CO₂ debe ser consistente con el factor de emisión configurado. |
| RNF-04 | Rendimiento | Las consultas de listados y reportes deben estar limitadas o paginadas para no degradar la respuesta con grandes volúmenes de datos. |
| RNF-05 | Compatibilidad | El sistema debe funcionar en navegadores modernos (Chrome, Firefox, Edge, Safari) con JavaScript habilitado para la experiencia completa. |
| RNF-06 | Mantenibilidad | El código del módulo debe seguir la estructura de Laravel y nwidart/laravel-modules para facilitar mantenimiento y evolución. |
| RNF-07 | Trazabilidad | Los registros de consumo deben permitir identificar la unidad, el factor, la fecha, la cantidad, el CO₂ generado y, si aplica, el usuario que registró o la solicitud que originó el registro (retraso). |

---

*Documento SRS – Sistema Web Huella de Carbono – Centro de Formación Agroindustrial La Angostura.*
