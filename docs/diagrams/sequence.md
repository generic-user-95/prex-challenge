# Diagramas de Secuencia (Todos los Casos de Uso)

## 1. UC1: Login (OAuth2.0 con expiración de 30 minutos)
```mermaid
sequenceDiagram
    autonumber
    actor Client as Cliente API
    participant Middleware as LogInteractionMiddleware
    participant Controller as AuthController
    participant UseCase as LoginUseCase
    participant UserRepo as UserRepository (Port/Adapter)
    participant TokenGen as PassportTokenGenerator (Port/Adapter)
    participant DB as Base de Datos

    Client->>Middleware: POST /api/auth/login {email, password}
    activate Middleware
    Middleware->>Controller: handle(request)
    activate Controller
    Controller->>UseCase: execute(LoginCommand)
    activate UseCase
    UseCase->>UserRepo: findByEmail(Email)
    UserRepo->>DB: SELECT * FROM users WHERE email = ?
    DB-->>UserRepo: Record (User)
    UserRepo-->>UseCase: Domain User
    UseCase->>UseCase: verifyPassword(password)
    UseCase->>TokenGen: generate(User, 30 mins)
    TokenGen->>DB: INSERT INTO oauth_access_tokens (expires_at = now + 30m)
    DB-->>TokenGen: Access Token Created
    TokenGen-->>UseCase: AuthTokenDTO (expires_in: 1800)
    UseCase-->>Controller: AuthTokenDTO
    deactivate UseCase
    Controller-->>Middleware: JSON Response 200 {access_token, expires_in: 1800}
    deactivate Controller
    Middleware->>DB: INSERT INTO interaction_logs (user_id, service, request_body, response_code, response_body, ip)
    Middleware-->>Client: 200 OK
    deactivate Middleware
```

## 2. UC2: Buscar GIFs por Término
```mermaid
sequenceDiagram
    autonumber
    actor Client as Cliente API
    participant Middleware as LogInteractionMiddleware
    participant AuthGuard as Passport Auth:api Guard
    participant Controller as GifController
    participant UseCase as SearchGifsUseCase
    participant GiphyAdapter as GiphyHttpAdapter (Port/Adapter)
    participant GiphyAPI as GIPHY External API
    participant DB as Base de Datos

    Client->>Middleware: GET /api/gifs/search?query=cats&limit=10&offset=0 (Bearer Token)
    activate Middleware
    Middleware->>AuthGuard: authenticate()
    AuthGuard->>DB: Validate Token & Expiration
    DB-->>AuthGuard: Valid Authenticated User
    Middleware->>Controller: search(SearchGifsRequest)
    activate Controller
    Controller->>UseCase: execute(SearchGifsQuery)
    activate UseCase
    UseCase->>GiphyAdapter: search(SearchTerm, Pagination)
    activate GiphyAdapter
    GiphyAdapter->>GiphyAPI: GET /v1/gifs/search?api_key=KEY&q=cats&limit=10&offset=0
    GiphyAPI-->>GiphyAdapter: 200 OK (Giphy JSON payload)
    GiphyAdapter-->>UseCase: Array of Gif Domain Entities + Pagination Data
    deactivate GiphyAdapter
    UseCase-->>Controller: PaginatedGifsDTO
    deactivate UseCase
    Controller-->>Middleware: JSON Response 200 {data: [...], pagination: {...}}
    deactivate Controller
    Middleware->>DB: INSERT INTO interaction_logs (user_id, service, request_body, status: 200, response_body, ip)
    Middleware-->>Client: 200 OK
    deactivate Middleware
```

## 3. UC3: Buscar GIF por ID
```mermaid
sequenceDiagram
    autonumber
    actor Client as Cliente API
    participant Middleware as LogInteractionMiddleware
    participant AuthGuard as Passport Auth:api Guard
    participant Controller as GifController
    participant UseCase as GetGifByIdUseCase
    participant GiphyAdapter as GiphyHttpAdapter (Port/Adapter)
    participant GiphyAPI as GIPHY External API
    participant DB as Base de Datos

    Client->>Middleware: GET /api/gifs/{id} (Bearer Token)
    activate Middleware
    Middleware->>AuthGuard: authenticate()
    AuthGuard-->>Middleware: Authenticated
    Middleware->>Controller: show(id)
    activate Controller
    Controller->>UseCase: execute(GetGifByIdQuery)
    activate UseCase
    UseCase->>GiphyAdapter: findById(GifId)
    activate GiphyAdapter
    GiphyAdapter->>GiphyAPI: GET /v1/gifs/{id}?api_key=KEY
    alt GIF encontrado
        GiphyAPI-->>GiphyAdapter: 200 OK (Gif JSON)
        GiphyAdapter-->>UseCase: Gif Domain Entity
        UseCase-->>Controller: GifDTO
        Controller-->>Middleware: 200 OK {data: {...}}
    else GIF no encontrado
        GiphyAPI-->>GiphyAdapter: 404 Not Found
        GiphyAdapter-->>UseCase: null
        UseCase-->>Controller: throws GifNotFoundException
        Controller-->>Middleware: 404 Not Found {error: 'Not Found'}
    end
    deactivate GiphyAdapter
    deactivate UseCase
    deactivate Controller
    Middleware->>DB: INSERT INTO interaction_logs (user_id, service, status_code, ip, etc.)
    Middleware-->>Client: Response (200 OK / 404 Not Found)
    deactivate Middleware
```

## 4. UC4: Guardar GIF Favorito
```mermaid
sequenceDiagram
    autonumber
    actor Client as Cliente API
    participant Middleware as LogInteractionMiddleware
    participant AuthGuard as Passport Auth:api Guard
    participant Controller as FavoriteGifController
    participant UseCase as SaveFavoriteGifUseCase
    participant UserRepo as UserRepository (Port/Adapter)
    participant FavRepo as FavoriteGifRepository (Port/Adapter)
    participant DB as Base de Datos

    Client->>Middleware: POST /api/gifs/favorites {gif_id, alias, user_id} (Bearer Token)
    activate Middleware
    Middleware->>AuthGuard: authenticate()
    AuthGuard-->>Middleware: Authenticated
    Middleware->>Controller: store(SaveFavoriteGifRequest)
    activate Controller
    Controller->>UseCase: execute(SaveFavoriteGifCommand)
    activate UseCase
    UseCase->>UserRepo: findById(UserId)
    UserRepo->>DB: SELECT * FROM users WHERE id = ?
    DB-->>UserRepo: Record (User)
    UserRepo-->>UseCase: Domain User
    UseCase->>FavRepo: save(FavoriteGif)
    FavRepo->>DB: INSERT INTO favorite_gifs (user_id, gif_id, alias)
    DB-->>FavRepo: Saved Record
    FavRepo-->>UseCase: Saved FavoriteGif Domain Entity
    UseCase-->>Controller: FavoriteGifDTO
    deactivate UseCase
    Controller-->>Middleware: 201 Created {message, data: {...}}
    deactivate Controller
    Middleware->>DB: INSERT INTO interaction_logs (user_id, service, request_body, status: 201, response_body, ip)
    Middleware-->>Client: 201 Created
    deactivate Middleware
```
