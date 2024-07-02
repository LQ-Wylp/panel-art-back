# PanelArt API Documentation

Cette documentation décrit les différentes routes de l'API `PanelArt`. Vous trouverez ci-dessous les détails pour chaque route, y compris les méthodes HTTP, les URL, et les exemples de corps de requête.

## Clients

### 1. Créer un client

- **URL**: `/api/clients/register`
- **Méthode**: `POST`
- **Corps de la requête**:
  ```json
  {
      "email": "raf@example.com",
      "password": "passwordRaf",
      "firstname": "Raf",
      "lastname": "Stor",
      "adresse": "13 Rue de Lille",
      "complement": "Appartement 3",
      "town": "Lille",
      "postalCode": "59000",
      "phone": "+33122566349"
  }
- **Description**: Crée un nouveau client avec les informations fournies.

### 2. Mettre à jour un client
- **URL**: /api/clients/{id}
- **Méthode**: PUT
- **Corps de la requête**:
  ```json
  {
    "firstname": "Raf",
    "lastname": "Storsr"
  }
- **Description**: Met à jour les informations du client spécifié par l'identifiant.

### 3. Supprimer un client
- **URL**: /api/clients/{id}
- **Méthode**: DELETE
- **Description**: Supprime le client spécifié par l'identifiant.

### 4. Obtenir un client par email
- **URL**: /api/clients/{email}
- **Méthode**: GET
- **Description**: Récupère les informations du client spécifié par l'email.

### 5. Obtenir tous les clients
- **URL**: /api/clients
- **Méthode**: GET
- **Description**: Récupère les informations de tous les clients.

### 6. Login du client
- **URL**: /api/clients/login/{email}/{password}
- **Méthode**: GET
- **Description**: Authentifie un client avec l'email et le mot de passe fournis.


## Peintures
### 1. Créer une peinture
- **URL**: /api/peintures
- **Méthode**: POST
- **Corps de la requête**:
  ```json
  {
      "title": "Titre de la peinture",
      "height": 120,
      "width": 80,
      "description": "Description de la peinture",
      "quantity": 1,
      "createdAt": "2024-07-01T12:00:00Z",
      "method": "Oil on canvas"
  }
- **Description**: Crée une nouvelle peinture avec les informations fournies.

### 2. Mettre à jour une peinture
- **URL**: /api/peintures/{id}
- **Méthode**: PUT
- **Corps de la requête**:
  ```json
  {
      "height": 1,
      "width": 80,
      "description": "Description de la peinture3"
  }
- **Description**: Met à jour les informations de la peinture spécifiée par l'identifiant.

### 3. Supprimer une peinture
- **URL**: /api/peintures/{id}
- **Méthode**: DELETE
- **Description**: Supprime la peinture spécifiée par l'identifiant.

### 4. Obtenir une peinture par ID
- **URL**: /api/peintures/{id}
- **Méthode**: GET
- **Description**: Récupère les informations de la peinture spécifiée par l'identifiant.

### 5. Obtenir toutes les peintures
- **URL**: /api/peintures
- **Méthode**: GET
- **Description**: Récupère les informations de toutes les peintures.

## Ventes
### 1. Créer une vente
- **URL**: /api/ventes
- **Méthode**: POST
- **Corps de la requête**:
  ```json
  {
      "idClient": 2,
      "idPeinture": 2,
      "amount": 100,
      "status": "Disponible"
  }
- **Description**: Crée une nouvelle vente avec les informations fournies.

### 2. Mettre à jour une vente
- **URL**: /api/ventes/{id}
- **Méthode**: PUT
- **Corps de la requête**:
  ```json
  {
      "amount": 42
  }
- **Description**: Met à jour les informations de la vente spécifiée par l'identifiant.

### 3. Supprimer une vente
- **URL**: /api/ventes/{id}
- **Méthode**: DELETE
- **Description**: Supprime la vente spécifiée par l'identifiant.

### 4. Obtenir une vente par ID
- **URL**: /api/ventes/{id}
- **Méthode**: GET
- **Description**: Récupère les informations de la vente spécifiée par l'identifiant.

### 5. Obtenir toutes les ventes
- **URL**: /api/ventes
- **Méthode**: GET
- **Description**: Récupère les informations de toutes les ventes.
