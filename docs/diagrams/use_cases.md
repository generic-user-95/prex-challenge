# Diagrama de Casos de Uso

```mermaid
flowchart TD
    actorAnon["Usuario / Cliente No Autenticado"]
    actorAuth["Usuario Autenticado"]
    serviceGiphy["Servicio Externo GIPHY API"]
    systemDB[("Base de Datos MySQL / MariaDB")]

    subgraph API["Challenge PHP Hexagonal API"]
        ucLogin(["UC1: Iniciar Sesión (Login OAuth2.0)"])
        ucSearchGifs(["UC2: Buscar GIFs por término"])
        ucGetGif(["UC3: Obtener GIF por ID"])
        ucFavGif(["UC4: Guardar GIF Favorito"])
        ucAudit(["UC5: Registrar Auditoría de Interacciones"])
    end

    actorAnon -->|POST /api/auth/login| ucLogin
    ucLogin -->|Valida credenciales y genera token de 30m| systemDB

    actorAuth -->|GET /api/gifs/search (Bearer Token)| ucSearchGifs
    actorAuth -->|GET /api/gifs/:id (Bearer Token)| ucGetGif
    actorAuth -->|POST /api/gifs/favorites (Bearer Token)| ucFavGif

    ucSearchGifs -->|Consulta endpoints de búsqueda| serviceGiphy
    ucGetGif -->|Consulta endpoint por ID| serviceGiphy
    ucFavGif -->|Persiste relación favorito| systemDB

    ucLogin -.->|<<include>> Registra petición, respuesta e IP| ucAudit
    ucSearchGifs -.->|<<include>> Registra petición, respuesta e IP| ucAudit
    ucGetGif -.->|<<include>> Registra petición, respuesta e IP| ucAudit
    ucFavGif -.->|<<include>> Registra petición, respuesta e IP| ucAudit

    ucAudit -->|Persiste log de auditoría| systemDB
```
