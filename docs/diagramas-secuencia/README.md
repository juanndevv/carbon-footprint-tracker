# Diagramas de secuencia – Huella de Carbono

Cada archivo `.puml` es un **diagrama de secuencia** independiente. Puedes abrirlo en PlantUML (online, VS Code o CLI) para generar la imagen.

| Archivo | Descripción |
|---------|-------------|
| `01-lider-registra-consumo.puml` | Líder registra consumo diario (un factor, una fecha) |
| `02-admin-aprueba-solicitud.puml` | Admin aprueba solicitud de registro → se crean consumos |
| `03-lider-solicita-registro-fecha-pasada.puml` | Líder envía solicitud para fecha pasada (pendiente de aprobación) |
| `04-visitante-calculadora-personal.puml` | Visitante usa calculadora personal de huella |
| `05-admin-rechaza-solicitud.puml` | Admin rechaza solicitud de registro |
| `06-admin-exporta-reporte-pdf.puml` | Admin genera y exporta reporte en PDF |
| `07-admin-edita-consumo.puml` | Admin edita cantidad/observaciones de un consumo |
| `08-admin-asignar-lider.puml` | Admin asigna o cambia líder de una unidad |
| `09-admin-listar-consumos-filtros.puml` | Admin lista consumos con filtros (unidad, fechas) |

**Generar imágenes (CLI):**
```bash
java -jar plantuml.jar docs/diagramas-secuencia/*.puml
```
