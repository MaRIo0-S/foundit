# Journal des modifications

Ce fichier recense les évolutions du projet FoundIt (PFE).

---

## Administration

### 2026-06-10

#### Ajout
- Mise en place du back office sous le préfixe `/admin`
- Dashboard administrateur avec statistiques (objets, réclamations, graphiques CSS)
- Gestion des réclamations : liste, fiche de traitement, approbation, rejet, confirmation de restitution
- Workflow complet des statuts réclamation (`pending` → `approved` / `rejected`) et objet (`available` → `claimed` → `returned`)
- Middleware d'accès admin (`EnsureAdminAccess`)
- Services admin : `ClaimWorkflowService`, `DashboardService`, `NotificationService`
- Service `AuditLogService` et table `audit_logs`
- Enrichissement table `notifications` (type, title, related_type, related_id, read_at)
- Enrichissement table `claims` (reviewed_by, reviewed_at, rejection_reason)
- Colonne `users.is_suspended`
- Routes `routes/admin.php` enregistrées dans `bootstrap/app.php`
- Notification automatique au staff lors d'une nouvelle réclamation
- Correction métier : l'objet ne passe plus en `claimed` à la soumission, seulement à l'approbation admin

#### Fichiers concernés (backend)
- `routes/admin.php`
- `bootstrap/app.php`
- `app/Http/Middleware/EnsureAdminAccess.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Admin/ClaimController.php`
- `app/Http/Requests/Admin/RejectClaimRequest.php`
- `app/Services/AuditLogService.php`
- `app/Services/Admin/ClaimWorkflowService.php`
- `app/Services/Admin/DashboardService.php`
- `app/Services/Admin/NotificationService.php`
- `app/Services/ClaimsService.php`
- `app/Models/User.php`
- `app/Models/Claim.php`
- `app/Models/Notification.php`
- `app/Models/AuditLog.php`
- `database/migrations/2026_06_10_100000_add_admin_infrastructure.php`
- `database/migrations/2026_06_10_100001_fix_users_role_constraint.php`
- `database/seeders/UserSeeder.php`

#### Fichiers concernés (frontend)
- `resources/js/pages/Admin/Dashboard.vue`
- `resources/js/pages/Admin/Claims/Index.vue`
- `resources/js/pages/Admin/Claims/Review.vue`
- `resources/js/app.js`

#### Description
Première version du back office FoundIt. Permet à l'administrateur de consulter les statistiques et de traiter les réclamations utilisateurs selon le workflow métier prévu en base de données.

---

### 2026-06-11

#### Modification — Simplification des rôles
- Suppression des rôles `super_admin` et `moderator`
- Conservation de deux rôles uniquement : `user` et `admin`
- Suppression de l'enum `UserRole` ; vérification via `User::isAdmin()`
- Mise à jour des seeders : `admin@foundit.com` → admin, `staff@foundit.com` → user
- Migration des comptes existants `super_admin` / `moderator` vers `admin`

#### Fichiers concernés
- `app/Enums/UserRole.php` *(supprimé)*
- `app/Models/User.php`
- `app/Http/Middleware/EnsureAdminAccess.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `app/Http/Requests/Admin/RejectClaimRequest.php`
- `app/Services/Admin/NotificationService.php`
- `database/migrations/2026_06_11_100000_simplify_user_roles.php`
- `database/seeders/UserSeeder.php`

#### Description
Alignement sur un modèle simple à deux rôles, adapté au niveau PFE. L'administrateur possède tous les droits du back office sans hiérarchie de rôles intermédiaires.

---

#### Modification — Fusion navigation admin / utilisateur
- Suppression du layout séparé `AdminLayout` (sidebar sombre dédiée)
- Suppression des composants `AdminSidebar` et `AdminHeader`
- Réutilisation de `MainLayout` pour toutes les pages (publiques et admin)
- Ajout de `AdminNav` : sous-navigation horizontale affichée sous la NavBar sur les routes `/admin/*`
- Ajout de liens admin dans la NavBar principale (Objets, Admin, Réclamations + badge pending)
- Déplacement des composants réutilisables vers `components/ui/` : `StatusBadge`, `KpiCard`, `EmptyState`
- Suppression du dossier `components/admin/`

#### Fichiers concernés
- `resources/js/layouts/MainLayout.vue`
- `resources/js/layouts/AdminLayout.vue` *(supprimé)*
- `resources/js/components/ui/NavigationBars/NavBar.vue`
- `resources/js/components/ui/NavigationBars/AdminNav.vue` *(ajouté)*
- `resources/js/components/ui/StatusBadge.vue` *(ajouté)*
- `resources/js/components/ui/KpiCard.vue` *(ajouté)*
- `resources/js/components/ui/EmptyState.vue` *(ajouté)*
- `resources/js/components/admin/*` *(supprimés)*
- `resources/js/pages/Admin/Dashboard.vue`
- `resources/js/pages/Admin/Claims/Index.vue`
- `resources/js/pages/Admin/Claims/Review.vue`
- `resources/js/app.js`

#### Description
L'administration partage désormais la même barre de navigation et le même layout que l'application publique. L'administrateur navigue entre les sections sans changer d'interface visuelle.

---

#### Ajout — Documentation projet
- Création de ce fichier `PROJECT_CHANGES.md` à la racine du projet

#### Description
Journal de bord des modifications. À mettre à jour à chaque intervention sur le projet.

---

### 2026-06-11 (suite)

#### Modification — Sidebar verticale admin
- Remplacement de la barre horizontale `AdminNav` par une sidebar verticale gauche `AdminSideBar`
- Sidebar affichée sur les routes `/admin/*` sous la NavBar principale
- Menu complet admin : Tableau de bord, Objets, Réclamations, Utilisateurs, Catégories, Lieux, Notifications, Journal, Paramètres
- Liens actifs uniquement pour les modules déjà implémentés (dashboard, réclamations) ; autres entrées grisées en attendant
- NavBar du haut simplifiée : lien unique « Administration » pour accéder au back office

#### Fichiers concernés
- `resources/js/components/ui/NavigationBars/AdminSideBar.vue` *(ajouté)*
- `resources/js/components/ui/NavigationBars/AdminNav.vue` *(supprimé)*
- `resources/js/layouts/MainLayout.vue`
- `resources/js/components/ui/NavigationBars/NavBar.vue`

#### Description
Retour à une navigation admin verticale à gauche, tout en conservant la NavBar globale FoundIt en haut pour une transition fluide entre app publique et administration.

---

### 2026-06-11 — Corrections navigation et espacement

#### Modification
- Suppression des entrées de menu non fonctionnelles (placeholders grisés) dans la sidebar admin
- Sidebar limitée aux pages existantes : Tableau de bord, Réclamations
- État actif corrigé pour Réclamations (inclut la page de traitement `admin.claims.review`)
- Uniformisation de l'espacement sur toutes les pages admin via classes globales dans `app.scss`
- Classes partagées : `admin-page`, `admin-panel`, `admin-grid`, `admin-filters`, `admin-table-wrap`
- Refactor des pages Dashboard, Claims Index et Claims Review pour utiliser ces classes

#### Fichiers concernés
- `resources/css/app.scss`
- `resources/js/components/ui/NavigationBars/AdminSideBar.vue`
- `resources/js/components/ui/KpiCard.vue`
- `resources/js/pages/Admin/Dashboard.vue`
- `resources/js/pages/Admin/Claims/Index.vue`
- `resources/js/pages/Admin/Claims/Review.vue`

#### Description
Correction des liens non cliquables et amélioration du padding/marges pour un rendu plus aéré et professionnel sur l'ensemble du back office.

---

### 2026-06-11 — Alignement KPI et état actif sidebar

#### Modification
- Grille KPI : 6 colonnes sur grand écran, hauteur uniforme des cartes, responsive 3/2/1 colonnes
- État actif sidebar corrigé via `page.url` (Réclamations actif sur `/admin/claims/*`)

#### Fichiers concernés
- `resources/css/app.scss`
- `resources/js/components/ui/KpiCard.vue`
- `resources/js/components/ui/NavigationBars/AdminSideBar.vue`

#### Description
Stabilisation visuelle et navigation du menu latéral admin.

---

### 2026-06-11 — Restauration dashboard, rôles et données de démonstration

#### Modification
- **Dashboard restauré** : 6 KPI, graphiques (30 jours, catégories, lieux), alerte objets périmés, réclamations en attente, activité récente, dernières déclarations
- **Compte « Surveillant Général » supprimé** : ce n’était pas un troisième rôle, mais un nom de démonstration ambigu pour un compte `user`. Remplacé par des profils réalistes (étudiant, enseignant, personnel, bibliothécaire, agent de sécurité)
- **Permissions vérifiées** : seul `role = admin` accède au back office (`EnsureAdminAccess` + lien Administration conditionné à `is_admin`)
- **Affichage du rôle** : libellés « Utilisateur » / « Administrateur » dans le menu profil (plus de confusion avec le nom du compte)
- **Seeders enrichis** : 7 utilisateurs nommés, 16 objets, 9 réclamations, 8 entrées de journal d’activité, dates étalées sur 30 jours

#### Fichiers concernés
- `resources/js/pages/Admin/Dashboard.vue`
- `app/Http/Controllers/Admin/DashboardController.php`
- `resources/css/app.scss`
- `database/seeders/UserSeeder.php`
- `database/seeders/ItemSeeder.php`
- `database/seeders/ClaimSeeder.php`
- `database/seeders/AuditLogSeeder.php` *(ajouté)*
- `database/seeders/DatabaseSeeder.php`
- `resources/js/components/ui/NavigationBars/NavBar.vue`

#### Description
Correction d’une suppression abusive du dashboard, clarification du système de rôles et enrichissement des données de démo pour un rendu professionnel.

---

## Comptes de démonstration

Mot de passe commun pour tous les comptes : **`password`**

| Nom | Email | Rôle | Profil / contenu associé |
|-----|-------|------|--------------------------|
| Admin FoundIt | `admin@foundit.com` | Administrateur | Accès back office, traitement des réclamations |
| Sophie Martin | `sophie.martin@lycee-demo.fr` | Utilisateur | Étudiante — déclare objets (AirPods, calculatrice, clés…), réclamations en cours |
| Thomas Dupont | `thomas.dupont@lycee-demo.fr` | Utilisateur | Étudiant — déclarations (lunettes, écharpe), réclamations carte d’identité et portefeuille |
| Marie Leroy | `marie.leroy@lycee-demo.fr` | Utilisateur | Enseignante — déclare veste et manuel, réclamation rejetée (clés) |
| Jean-Pierre Moreau | `jp.moreau@lycee-demo.fr` | Utilisateur | Personnel administratif — déclare portefeuille et montre, réclamation badge en attente |
| Élodie Bernard | `elodie.bernard@lycee-demo.fr` | Utilisateur | Bibliothécaire — déclare carte d’identité et clés de casier |
| Marc Lefebvre | `marc.lefebvre@lycee-demo.fr` | Utilisateur | Agent de sécurité — déclare iPhone, Fitbit, bouteille isotherme |

**Relancer les données de démo :**
```bash
php artisan migrate:fresh --seed
```

---

### 2026-06-12 — Flux réclamations, notifications et menu admin

#### Modification
- Page « Mes réclamations » : affichage du statut réclamation (en attente / approuvée / rejetée) et du motif de refus admin
- Dashboard : bouton « Réclamer » adapté selon statut objet et réclamation utilisateur
- Notifications visibles dans le menu profil (desktop) et menu mobile
- Menu admin harmonisé : « Réclamations à traiter » sur desktop et mobile
- Menu mobile réorganisé en sections (Navigation / Administration / Mon compte)
- Correction : nouvelle réclamation possible après un refus

#### Fichiers concernés
- `resources/js/pages/User/Reclamations.vue`
- `resources/js/pages/User/ModifyReclamation.vue`
- `resources/js/pages/Dashboard.vue`
- `resources/js/components/ui/NavigationBars/NavBar.vue`
- `app/Services/ClaimsService.php`
- `app/Http/Middleware/HandleInertiaRequests.php`
- `app/Http/Controllers/Web/Routing/RoutingController.php`

---

### 2026-06-12 — API REST pour client desktop C#

#### Modification
- Installation de **Laravel Sanctum** (tokens Bearer)
- Création de `routes/api.php` protégé par `auth:sanctum` (+ `admin` pour routes admin)
- Contrôleurs API dédiés réutilisant Services et Form Requests existants
- Resources JSON : `UserResource`, `CategoryResource`, `LocationResource`, `ItemResource`, `ClaimResource`, `NotificationResource`
- Migration `personal_access_tokens`

#### Endpoints principaux (`/api/...`)

| Méthode | Route | Auth | Description |
|---------|-------|------|-------------|
| POST | `/auth/register` | — | Inscription (`RegisterRequest`) |
| POST | `/auth/login` | — | Connexion → token Bearer |
| POST | `/auth/logout` | sanctum | Révoque le token courant |
| GET | `/auth/me` | sanctum | Utilisateur connecté |
| GET | `/categories`, `/categories/{id}` | sanctum | Référentiel catégories |
| GET | `/locations`, `/locations/{id}` | sanctum | Référentiel lieux |
| GET/POST/PATCH/DELETE | `/items`, `/items/{id}` | sanctum | CRUD objets (`ItemRegisterRequest`) |
| GET/PATCH/DELETE | `/claims`, `/claims/{id}` | sanctum | Réclamations utilisateur |
| POST | `/items/{id}/claims` | sanctum | Soumettre réclamation (`ClaimRequest`) |
| GET/PATCH | `/notifications`, `/notifications/{id}/read` | sanctum | Notifications |
| GET | `/profile/declarations`, `/profile/reclamations` | sanctum | Historique utilisateur |
| PATCH | `/profile` | sanctum | Modifier profil (`UpdateUserRequest`) |
| GET/POST | `/admin/claims`, `/admin/claims/{id}/approve\|reject\|return` | sanctum + admin | Workflow admin |

**Authentification desktop :** `Authorization: Bearer {token}` après `POST /api/auth/login`.

#### Fichiers concernés
- `routes/api.php` *(ajouté)*
- `bootstrap/app.php`
- `app/Models/User.php`
- `app/Http/Controllers/Api/*` *(ajoutés)*
- `app/Http/Resources/*` *(ajoutés)*
- `app/Services/AuthService.php`, `app/Services/ItemsService.php`
- `app/Http/Middleware/EnsureAdminAccess.php`
- `config/sanctum.php`, migration Sanctum

---

### 2026-06-11 — Diagramme d'architecture et affichage mot de passe

#### Modification
- **Documentation architecture** : création de `docs/ARCHITECTURE.md` avec diagrammes Mermaid (acteurs, entités, flux métier, modules, vue technique)
- **Graphique dashboard** : refonte du graphique « Déclarations (30 jours) » — barres uniformes, axe Y, valeurs affichées, grille alignée
- **Composant `PasswordInput`** : icône œil pour afficher/masquer le mot de passe, réutilisé sur connexion, inscription et modification du profil

#### Fichiers concernés
- `docs/ARCHITECTURE.md` *(ajouté)*
- `resources/js/components/ui/PasswordInput.vue` *(ajouté)*
- `resources/js/pages/Auth/Login.vue`
- `resources/js/pages/Auth/Register.vue`
- `resources/js/pages/User/ModifyProfil.vue`
- `resources/js/pages/Admin/Dashboard.vue`

#### Description
Amélioration de la lisibilité pour le jury PFE et de l'expérience utilisateur sur les formulaires sensibles.

---

### 2026-06-10 — Notifications, restrictions admin et données démo

#### Modification
- **Cloche de notifications** dans la NavBar : panneau dédié, badge non-lus, marquage automatique comme lues à l'ouverture
- **Purge automatique** : notifications lues supprimées après 7 jours (`NotificationInboxService`)
- **Admin** : ne peut plus déclarer ni réclamer (middleware `user.only` web + API)
- **Objets > 30 jours** : lien « Voir dans le catalogue » depuis le dashboard admin + filtre `?stale=1&status=available` sur le catalogue
- **Graphique déclarations** : seeders enrichis avec plusieurs déclarations par jour pour valider le rendu dynamique
- **Données démo** : objet « Parapluie » (> 30 jours), badge déclaré par un utilisateur (plus l'admin)

#### Fichiers concernés
- `resources/js/components/ui/NavigationBars/NavBar.vue`
- `app/Services/NotificationInboxService.php`
- `app/Http/Controllers/Web/NotificationController.php`
- `app/Http/Middleware/EnsureRegularUser.php`
- `resources/js/pages/Dashboard.vue`, `Items/Category.vue`, `Items/Location.vue`
- `resources/js/pages/Admin/Dashboard.vue`
- `app/Services/ItemsService.php`, `app/Services/Admin/DashboardService.php`
- `database/seeders/ItemSeeder.php`
- `routes/web.php`, `routes/api.php`

---

### 2026-06-10 — Téléphones de contact et visibilité catalogue

#### Modification
- **Téléphones de contact** : obligatoires à la déclaration et à la réclamation, masqués du catalogue public, échangés entre déclarant et réclamant après approbation admin
- **Catalogue** : seuls les objets `available` sont visibles par défaut (objets approuvés/réclamés retirés de la liste publique)
- **Réclamations multiples** : plusieurs utilisateurs peuvent réclamer le même objet (une seule réclamation active par utilisateur)

#### Fichiers concernés
- `database/migrations/2026_06_10_200000_add_contact_phones_to_items_and_claims.php`
- `app/Models/Item.php`, `app/Models/Claim.php`
- `app/Http/Requests/ItemRegisterRequest.php`, `ClaimRequest.php`
- `app/Services/ItemsService.php`, `ClaimsService.php`, `UserService.php`, `ClaimWorkflowService.php`
- `resources/js/pages/Items/Declare.vue`, `User/ModifyDeclaration.vue`, `User/Reclamations.vue`, `User/Declarations.vue`
- `resources/js/pages/Dashboard.vue`, `Items/Category.vue`, `Items/Location.vue`
- `resources/js/pages/Admin/Claims/Review.vue`
- `database/seeders/ItemSeeder.php`, `ClaimSeeder.php`

---

## Modules non encore implémentés

Les fonctionnalités suivantes sont prévues mais absentes à ce jour :
- Gestion admin des objets trouvés
- Gestion admin des utilisateurs
- Gestion admin des catégories et lieux
- Journal d'activité (interface)
- Paramètres de l'application
