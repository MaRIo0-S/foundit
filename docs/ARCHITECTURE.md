# FoundIt — Architecture fonctionnelle

Document de référence pour comprendre l'organisation du projet FoundIt (PFE).  
Il sépare volontairement la **vue métier** (ce que fait l'application) de la **vue technique** (comment elle est construite).

---

## 1. Acteurs et responsabilités

```mermaid
flowchart TB
    subgraph Acteurs["Acteurs"]
        U["Utilisateur<br/><small>Étudiant, enseignant, personnel…</small>"]
        A["Administrateur<br/><small>Vie scolaire, secrétariat…</small>"]
    end

    subgraph App["Application FoundIt"]
        P["Espace public<br/>Objets trouvés"]
        B["Back office admin<br/>Modération"]
    end

    U -->|"Consulte, déclare, réclame"| P
    A -->|"Consulte, déclare, réclame"| P
    A -->|"Traite les réclamations"| B

    style A fill:#0f2b4c,color:#fff
    style B fill:#0f2b4c,color:#fff
    style P fill:#f5f0eb,color:#0f2b4c
```

| Acteur | Rôle en base | Accès |
|--------|--------------|-------|
| Utilisateur | `user` | Application publique uniquement |
| Administrateur | `admin` | Application publique + back office `/admin` |

---

## 2. Entités métier et relations

```mermaid
erDiagram
    USER ||--o{ ITEM : "déclare"
    USER ||--o{ CLAIM : "soumet"
    USER ||--o{ NOTIFICATION : "reçoit"
    USER ||--o{ CLAIM : "examine (admin)"

    CATEGORY ||--o{ ITEM : "classifie"
    LOCATION ||--o{ ITEM : "localise"

    ITEM ||--o{ CLAIM : "fait l'objet de"

    USER {
        string name
        string email
        string role
    }

    ITEM {
        string name
        string status
        date found_date
    }

    CLAIM {
        string status
        text claim_notes
    }

    CATEGORY {
        string name
    }

    LOCATION {
        string name
    }

    NOTIFICATION {
        string type
        string title
        string message
    }
```

### Statuts principaux

**Objet trouvé (`items.status`)**

| Statut | Signification |
|--------|---------------|
| `available` | Visible, aucune réclamation approuvée |
| `claimed` | Réclamation approuvée, en attente de restitution |
| `returned` | Restitué au propriétaire |

**Réclamation (`claims.status`)**

| Statut | Signification |
|--------|---------------|
| `pending` | En attente de décision admin |
| `approved` | Acceptée — l'objet passe en `claimed` |
| `rejected` | Refusée — l'objet reste `available` |

---

## 3. Flux métier — cycle de vie d'un objet

```mermaid
flowchart LR
    A["Objet trouvé<br/>sur le terrain"] --> B["Déclaration<br/>par un utilisateur"]
    B --> C["Objet disponible<br/>dans le catalogue"]
    C --> D["Réclamation<br/>par un autre utilisateur"]
    D --> E{"Décision<br/>administrateur"}
    E -->|"Approuvée"| F["Objet réclamé"]
    E -->|"Rejetée"| C
    F --> G["Restitution<br/>confirmée"]
    G --> H["Objet restitué"]

    style E fill:#e8410a,color:#fff
    style C fill:#f5f0eb,color:#0f2b4c
    style H fill:#0f2b4c,color:#fff
```

---

## 4. Interactions entre modules

```mermaid
flowchart TB
    subgraph Utilisateur["Module Utilisateur"]
        U1["Authentification"]
        U2["Déclarer un objet"]
        U3["Consulter le catalogue"]
        U4["Soumettre une réclamation"]
        U5["Suivre ses déclarations / réclamations"]
    end

    subgraph Referentiel["Référentiel"]
        R1["Catégories"]
        R2["Lieux"]
    end

    subgraph Administration["Module Administration"]
        A1["Tableau de bord"]
        A2["Traiter les réclamations"]
        A3["Approuver / Rejeter / Restituer"]
    end

    subgraph Transversal["Services transverses"]
        N1["Notifications internes"]
        L1["Journal d'activité"]
    end

    U2 --> R1
    U2 --> R2
    U2 --> U3
    U4 --> A2
    A2 --> A3
    A3 --> N1
    A3 --> L1
    A3 --> U3

    style Administration fill:#0f2b4c,color:#fff
    style Referentiel fill:#f5f0eb,color:#0f2b4c
```

---

## 5. Vue technique simplifiée

```mermaid
flowchart TB
    subgraph Frontend["Frontend — Vue 3 + Inertia"]
        V1["Pages publiques"]
        V2["Pages admin"]
        V3["Composants UI partagés"]
    end

    subgraph Backend["Backend — Laravel 13"]
        C1["Contrôleurs"]
        S1["Services métier"]
        M1["Modèles Eloquent"]
    end

    DB[("PostgreSQL")]

    V1 <-->|"Inertia JSON"| C1
    V2 <-->|"Inertia JSON"| C1
    C1 --> S1
    S1 --> M1
    M1 --> DB

    style Frontend fill:#f5f0eb,color:#0f2b4c
    style Backend fill:#0f2b4c,color:#fff
    style DB fill:#e8410a,color:#fff
```

| Couche | Rôle |
|--------|------|
| **Pages Vue** | Interface utilisateur, formulaires, tableaux |
| **Contrôleurs** | Réception des requêtes, validation, réponse Inertia |
| **Services** | Logique métier (workflow réclamations, statistiques, notifications) |
| **Modèles** | Représentation des entités et relations en base |

---

## 6. Périmètre actuel du projet

**Implémenté**

- Authentification (connexion, inscription, profil)
- Catalogue d'objets trouvés (recherche, filtres)
- Déclaration d'objets (catégorie, lieu)
- Réclamations utilisateur
- Back office : tableau de bord, traitement des réclamations
- Notifications et journal d'activité (côté backend)

**Non implémenté à ce jour**

- Interfaces admin : gestion utilisateurs, catégories, lieux, centre de notifications, paramètres

---

*Document généré pour le projet FoundIt — à utiliser comme support de présentation PFE.*
