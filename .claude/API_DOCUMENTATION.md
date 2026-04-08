# 📡 API RESTful Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
Usar Bearer Token via Sanctum:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## 🔐 Authentication Endpoints

### Login
**POST** `/login`

Request:
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

Response (201):
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  },
  "token": "1|abcdefg..."
}
```

### Logout
**POST** `/logout` (Requer autenticação)

Response (200):
```json
{
  "message": "Deslogado com sucesso"
}
```

### Get Current User
**GET** `/me` (Requer autenticação)

Response (200):
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "user@example.com"
}
```

---

## 🏛️ Igreja Endpoints

### List Igrejas
**GET** `/v1/igrejas` (Requer autenticação)

Query Parameters:
- `q`: Buscar por nome/código/cidade
- `page`: Número da página (default: 1)
- `per_page`: Registros por página (default: 20)

Example:
```
GET /v1/igrejas?q=são&page=1&per_page=20
```

Response (200):
```json
{
  "data": [
    {
      "id": 1,
      "codigo_controle": "IGR001",
      "nome_fantasia": "Igreja Primeira",
      "razao_social": "Igreja Primeira Ltda",
      "cidade": "São Paulo",
      "estado": "SP",
      "created_at": "2026-04-08T10:30:00Z"
    }
  ],
  "pagination": {
    "total": 50,
    "per_page": 20,
    "current_page": 1,
    "last_page": 3
  }
}
```

### Get Igreja Details
**GET** `/v1/igrejas/{id}` (Requer autenticação)

Response (200):
```json
{
  "id": 1,
  "codigo_controle": "IGR001",
  "nome_fantasia": "Igreja Primeira",
  "razao_social": "Igreja Primeira Ltda",
  "cidade": "São Paulo",
  "estado": "SP",
  "fotos": [...],
  "documentos": [...],
  "tarefas": [...]
}
```

### Create Igreja
**POST** `/v1/igrejas` (Requer autenticação)

Request:
```json
{
  "codigo_controle": "IGR002",
  "nome_fantasia": "Igreja Segunda",
  "razao_social": "Igreja Segunda Ltda",
  "cidade": "Rio de Janeiro",
  "estado": "RJ"
}
```

Response (201):
```json
{
  "id": 2,
  "codigo_controle": "IGR002",
  ...
}
```

### Update Igreja
**PUT** `/v1/igrejas/{id}` (Requer autenticação)

Request:
```json
{
  "nome_fantasia": "Igreja Primeira Atualizada"
}
```

Response (200):
```json
{
  "id": 1,
  "nome_fantasia": "Igreja Primeira Atualizada",
  ...
}
```

### Delete Igreja
**DELETE** `/v1/igrejas/{id}` (Requer autenticação)

Response (200):
```json
{
  "message": "Igreja deletada com sucesso"
}
```

---

## ⚠️ Error Responses

### Unauthorized (401)
```json
{
  "message": "Unauthorized"
}
```

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "codigo_controle": ["The codigo controle field is required."]
  }
}
```

### Not Found (404)
```json
{
  "message": "Not found"
}
```

---

## 🧪 Testing com cURL

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

### Get Igrejas
```bash
curl -X GET http://localhost:8000/api/v1/igrejas \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Igreja
```bash
curl -X POST http://localhost:8000/api/v1/igrejas \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "codigo_controle": "IGR003",
    "nome_fantasia": "Igreja Terceira",
    "razao_social": "Igreja Terceira Ltda",
    "cidade": "Salvador",
    "estado": "BA"
  }'
```

---

## 📊 Response Status Codes

- **200**: OK - Operação bem-sucedida
- **201**: Created - Recurso criado com sucesso
- **400**: Bad Request - Erro na requisição
- **401**: Unauthorized - Autenticação necessária
- **404**: Not Found - Recurso não encontrado
- **422**: Unprocessable Entity - Erro de validação
- **500**: Internal Server Error - Erro do servidor

---

## 🔄 Versioning

API usa versionamento via URL path:
- `/api/v1/igrejas` - Version 1 (atual)
- `/api/v2/igrejas` - Version 2 (planejado para futuro)

---

## 🚀 Futuras Expansões

- [ ] Documentos endpoint
- [ ] Tarefas endpoint
- [ ] Fotos endpoint
- [ ] Rate limiting
- [ ] API keys (alternativa a Sanctum)
- [ ] Webhooks
- [ ] GraphQL API

