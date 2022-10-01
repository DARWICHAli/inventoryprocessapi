# InventoryProcessAPI

API d'intercommunication entre les interfaces et le backend.


## Entrées / Sorties

### JSON

Les entrées se font **toujours** en _JSON_.

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

Les réponses sont au format JSON si succès.

### Pages

L'API est comprise de deux pages : 

* `auth.php` pour l'authentification.

* `query.php` pour les requêtes à l'API

### Erreurs

Une erreur sera signifiée par un code d'erreur HTTP 4XX.

### Connexion et sécurité

Afin de garantir la sécurité, les clients doivent s'authentifier via la page `auth.php` et récupérer un JWT.

!(./doc/seq_auth.png)[Diagramme de séquence auth.php]

### Requêtes

!(./doc/seq_query.png)[Diagramme de séquence query.php]

### Requêtes

.... à venir

## Accès BDD direct

.... à venir
