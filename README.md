# dourousi
Dourousi — Plugin WordPress de gestion de cours audio

Dourousi est un plugin WordPress qui permet de publier, organiser et mettre en valeur des cours audio de façon simple et élégante.
Il s’adresse aux créateurs de contenu, enseignants, associations ou instituts qui souhaitent partager des cours audio classés par thèmes, savants, niveaux de difficulté, etc.

À quoi ça sert ?

Avec Dourousi, vous pouvez :

Créer des cours audio sous forme de fiches complètes.

Ajouter un ou plusieurs chapitres audio avec un lecteur moderne (Plyr).

Associer chaque cours à des taxonomies personnalisées :

Savant (qui a donné le cours)

Difficulté (débutant, intermédiaire, avancé)

Catégorie de cours (ex: Aqida, Fiqh, Hadith…).

Fournir des ressources complémentaires (livre, PDF, lien externe).

Afficher vos cours où vous voulez grâce aux shortcodes ou au bloc Gutenberg.

Donner accès à une page d’archives qui liste automatiquement tous les cours.

🚀 Comment ça marche ?
1. Créer un cours

Une fois le plugin activé :

Un nouveau menu Cours apparaît dans l’admin WordPress.

Chaque cours fonctionne comme un article, avec en plus :

Champs personnalisés (auteur du livre, nom du livre, PDF, chapitres audio…).

Association avec les taxonomies (savant, difficulté, catégorie).

2. Les taxonomies

Savant : permet de relier un cours à une personne spécifique.

Difficulté : permet de classer les cours par niveau (débutant → avancé).

Catégorie de cours : organise les cours par thématiques.

Cela permet ensuite de filtrer et regrouper les cours facilement.

3. Les shortcodes

Le plugin fournit le shortcode principal :

[dourousi_courses]


Il affiche une liste de cours, avec différents paramètres :

number → nombre de cours à afficher (par défaut 5).

savant → filtrer par slug du savant.

categorie → filtrer par catégorie de cours.

difficulte → filtrer par niveau de difficulté.

layout → choix de l’affichage (grid, list, carousel).

show_thumbnail → afficher ou non les images (true|false).

show_excerpt → afficher ou non le résumé.

Exemples :

[dourousi_courses number="6" layout="grid"]

[dourousi_courses number="3" savant="ibn-baz" difficulte="debutant" layout="carousel"]

4. Le bloc Gutenberg

Dans l’éditeur WordPress, un bloc Cours Dourousi est disponible.
Il permet de configurer directement le nombre de cours, le savant, etc.
Quand vous enregistrez, le bloc génère automatiquement le shortcode correspondant.

5. Page d’archive

Tous les cours sont accessibles à l’adresse :

https://votre-site.com/cours/


Cette page affiche automatiquement tous les cours publiés (comme une archive de blog).

🎨 Le design

Les cours s’affichent sous forme de cartes modernes (grid, liste ou carousel).

Chaque carte peut contenir : image, titre, savant, niveau de difficulté, bouton d’accès.

Un lecteur audio élégant permet d’écouter les chapitres directement depuis la page du cours.

🔎 Exemple concret

Imaginons que vous publiez :

Un cours intitulé Explication du Livre de la Prière.

Vous l’associez au savant Ibn Baz, niveau Débutant, catégorie Fiqh.

Vous ajoutez 5 chapitres audio + le PDF du livre.

Résultat :

La page /cours/explication-du-livre-de-la-priere/ affiche la fiche complète.

La page /cours/ liste automatiquement ce cours.

Avec [dourousi_courses number="1" savant="ibn-baz"], vous affichez ce cours dans n’importe quelle page/article.