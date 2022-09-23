# InventoryProcessAPI

API d'intercommunication entre les interfaces et le backend.


## Entrées / Sorties

Les entrées se feront **toujours** en _JSON_.

Format d'un message à l'API.

```json
{
    "code": 0, /* int: API code */
    "token": "xxxx", /* string: JWT token */
    "content": {
        /* request content */
        "property": value
    }
}
```

Les réponses sont elles aussi au format JSON.

### Erreurs

Si la requête est mal formée, le serveur répondra avec un code d'erreur HTTP 400. Si le token d'authentification n'est pas adapté à la requête, le serveur répondra HTTP 401.

## Connexion et sécurité

Afin de garantir la sécurité, les clients doivent s'authentifier via la page `auth.php` et récupérer un JWT.

Les 

### Requêtes

.... à venir

## Accès BDD direct

.... à venir
