# 📋 API Payloads & Examples

## 🎯 Payloads Padrão

### Autenticação

#### Login
```json
{
  "email": "leonardo@empresa.com",
  "password": "password123"
}
```

#### Registro
```json
{
  "name": "Leonardo Ribeiro",
  "email": "leonardo@empresa.com",
  "password": "password123",
  "password_confirmation": "password123",
  "department": "Sales",
  "position": "Sales Manager",
  "phone": "(11) 99999-9999"
}
```

### Solicitação de Viagem

#### Criar Solicitação (Padrão Obrigatório)
```json
{
  "requestor_name": "Leonardo",
  "departure_date": "16-07-2025",
  "return_date": "20-07-2025",
  "justification": "Viagem a trabalho",
  "destination": "Salvador - BA",
  "purpose": "Venda de Software e Consultoria"
}
```

#### Criar Solicitação (Completa)
```json
{
  "requestor_name": "Leonardo Ribeiro",
  "departure_date": "16-07-2025",
  "return_date": "20-07-2025",
  "justification": "Viagem a trabalho para apresentação de proposta comercial",
  "destination": "Salvador - BA",
  "purpose": "Venda de Software e Consultoria",
  "estimated_cost": 2500.00,
  "notes": "Reunião com cliente potencial de grande porte"
}
```

### Aprovação/Rejeição

#### Aprovar Solicitação
```json
{
  "comment": "Aprovado pela administração. Boa viagem!"
}
```

#### Rejeitar Solicitação
```json
{
  "comment": "Orçamento insuficiente para este período. Tente reagendar para o próximo trimestre."
}
```

### Gerenciamento de Usuários

#### Criar Usuário
```json
{
  "name": "Maria Santos",
  "email": "maria@empresa.com",
  "password": "password123",
  "role": "employee",
  "department": "IT",
  "position": "Developer",
  "phone": "(11) 98888-8888",
  "is_active": true
}
```

#### Atualizar Usuário
```json
{
  "name": "Maria Santos Silva",
  "role": "manager",
  "department": "IT",
  "position": "Tech Lead",
  "phone": "(11) 97777-7777"
}
```

## 🔄 Respostas Padrão

### Sucesso
```json
{
  "success": true,
  "message": "Operação realizada com sucesso",
  "data": {
    // dados do recurso
  }
}
```

### Erro
```json
{
  "success": false,
  "message": "Mensagem de erro",
  "errors": {
    "campo": ["Erro específico"]
  }
}
```

## 📊 Filtros e Parâmetros

### Solicitações de Viagem
```
GET /api/admin/travel-requests?
  status=requested&
  user_id=1&
  destination=São Paulo&
  start_date=2025-01-01&
  end_date=2025-12-31&
  search=leonardo&
  page=1&
  per_page=20
```

### Usuários
```
GET /api/admin/users?
  role=employee&
  department=IT&
  is_active=true&
  search=maria&
  page=1&
  per_page=15
```

## 🎨 Formatos de Data

- **Data de Partida/Retorno**: `DD-MM-YYYY` (ex: "16-07-2025")
- **Timestamps**: ISO 8601 (ex: "2025-07-16T10:30:00Z")
- **Filtros de Data**: `YYYY-MM-DD` (ex: "2025-07-16")

## 🔐 Headers Obrigatórios

### Autenticação
```
Authorization: Bearer {token}
```

### Content-Type
```
Content-Type: application/json
```

### Accept
```
Accept: application/json
```

## 🎯 Status de Solicitações

- **requested**: Solicitação criada, aguardando aprovação
- **approved**: Solicitação aprovada
- **rejected**: Solicitação rejeitada
- **cancelled**: Solicitação cancelada

## 👥 Roles de Usuário

- **employee**: Funcionário padrão
- **manager**: Gerente (pode aprovar solicitações)
- **admin**: Administrador (acesso total)

## 🏢 Departamentos

- **IT**: Tecnologia da Informação
- **Sales**: Vendas
- **Marketing**: Marketing
- **Finance**: Financeiro
- **HR**: Recursos Humanos
- **Operations**: Operações
