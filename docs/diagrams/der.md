# Diagrama de Entidad Relación (DER)

```mermaid
erDiagram
    users ||--o{ favorite_gifs : "posee"
    users ||--o{ interaction_logs : "genera"
    users ||--o{ oauth_access_tokens : "autenticado_por"

    users {
        bigint id PK "Auto Increment"
        string name "Nombre del usuario"
        string email "Email único"
        string password "Password hasheado (Bcrypt)"
        timestamp email_verified_at "Nullable"
        string remember_token "Nullable"
        timestamp created_at
        timestamp updated_at
    }

    favorite_gifs {
        bigint id PK "Auto Increment"
        bigint user_id FK "users.id (CASCADE)"
        string gif_id "ID del recurso GIF en GIPHY"
        string alias "Nombre/Alias asignado por el usuario"
        timestamp created_at
        timestamp updated_at
    }

    interaction_logs {
        bigint id PK "Auto Increment"
        bigint user_id FK "users.id (NULL ON DELETE)"
        string service "Método y Ruta del servicio (ej. POST api/auth/login)"
        longtext request_body "Payload recibido (contraseñas enmascaradas)"
        int response_status_code "Código HTTP de respuesta (ej. 200, 201, 401)"
        longtext response_body "Cuerpo de la respuesta emitida"
        string ip_address "IP de origen del cliente (IPv4/IPv6)"
        timestamp created_at
        timestamp updated_at
    }

    oauth_access_tokens {
        string id PK "Token ID (UUID/Hash)"
        bigint user_id FK "users.id"
        uuid client_id FK "oauth_clients.id"
        string name "Token Name"
        text scopes "Scopes"
        boolean revoked "Estado de revocación"
        timestamp created_at
        timestamp updated_at
        timestamp expires_at "Expiración a los 30 minutos"
    }

    oauth_clients {
        uuid id PK "Client ID (UUID)"
        string name "Nombre del cliente"
        string secret "Client Secret"
        string provider "users"
        text redirect_uris "URIs de redirección"
        text grant_types "Tipos de grant"
        boolean revoked "Estado de revocación"
        timestamp created_at
        timestamp updated_at
    }
```
