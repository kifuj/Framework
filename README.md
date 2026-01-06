# Framework MVC

## Tâches à réaliser

### ORM

- [ ] (1 pt) Définir un nom de table par défaut à partir du nom de l'entité dans la classe `Entity`.
  Par exemple, si l'entité est `Post`, le nom de la table sera `post`. Vous pouvez également essayer
  de gérer les cas où le nom de l'entité est composé de plusieurs mots, par exemple `PostComment`
  aura pour nom de table `post_comment`.
- [ ] (2 pts) Ajouter une méthode `getOne` dans la classe `Entity` qui prend en paramètre un identifiant (id)
  et qui retourne un objet correspondant à cet identifiant.
- [ ] (3 pts) Améliorer la méthode `getAll` dans la classe `Entity` pour qu'elle puisse prendre en paramètre :
    - une condition (clause WHERE)
    - un ordre de tri (ASC ou DESC)
    - une limite (nombre maximum d'objets à retourner)
      Exemple d'utilisation :
```php
$posts = $this->getDatabase()->getEntity(Post::class)->getAll(['published' => 1], ['created_at' => 'ASC'], 10);
```
Vous pouvez également améliorer la clause WHERE pour qu'elle puisse prendre en paramètre des opérateurs
de comparaison (>, <, >=, <=, =, !=, LIKE, etc.) et éventuellement utiliser de la programmation orientée objet
plutôt que des tableaux associatifs.
- [ ] (2 pts) Ajouter une méthode `delete` dans la classe `Entity` qui permet de supprimer un objet de la base de données.
  Exemple d'utilisation :
```php
$post = $this->getDatabase()->getEntity(Post::class)->getOne(1);
$post->delete();
```
- [ ] (2 pts) Ajouter une méthode `save` dans la classe `Entity` qui permet de sauvegarder un objet en base de données.
  Exemple d'utilisation :
```php
$post = new Post();
$post->setTitle('Mon premier article');
$post->setContent('Contenu de mon premier article');
$post->save();
```
La méthode `save` doit être capable de déterminer si l'objet doit être inséré ou mis à jour en base de données
et exécuter la requête SQL correspondante (INSERT ou UPDATE).

### Controller

- [ ] (1 pt) Personnaliser le message d'erreur 404 (page non trouvée) dans le Router.
- [ ] (1 pt) Personnaliser le message d'erreur lorsque la vue appelée par la méthode `render` n'existe pas.
- [ ] (2 pts) Ajouter une méthode `redirect` dans la classe `Controller` qui permet de rediriger l'utilisateur vers une autre
  page (utiliser la fonction `header` de PHP).
- [ ] (3 pts) Ajouter les méthodes `addFlash` et `getFlash` dans la classe `Controller` pour gérer les messages flash en session.
  Exemple d'utilisation :
```php
// Dans un contrôleur
$this->addFlash('success', 'Votre article a bien été publié !');

// Dans une vue
$messages = $this->getFlashes('success'); // Retourne un tableau de messages de type 'success'
$messages = $this->getFlashes(); // Retourne un tableau de tous les messages flash
```
L'objectif ici est de créer une couche d'abstraction pour gérer les messages flash en session, sans avoir à manipuler
directement la superglobale `$_SESSION`.
- [ ] (3 pts) Créer une classe `Response` qui permet de gérer la réponse HTTP (code de statut et contenu).
  Les méthodes des contrôleurs devront retourner un objet de type `Response` qui sera ensuite traitée
  par le Router.
  Exemple d'utilisation :
```php
public function index(): Response
{
    $response = new Response();
    $response->setContent('Hello world !');
    $response->setStatusCode(200);
    return $response; // La réponse sera ensuite affichée par le Router
}
```
Il faudra également mettre à jour la méthode `render` de la classe `Controller` pour qu'elle retourne
un objet de type `Response`.