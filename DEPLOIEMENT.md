# Déploiement sur Render — Explication complète

Document rédigé après le déploiement réussi de l'application sur Render
(commit `c842bea`, application en ligne sur https://depot-reservation.onrender.com).

---

## 1. Le problème de départ

Lors du déploiement, le build réussissait mais le déploiement échouait avec :

```
No open ports detected, continuing to scan...
Docs on specifying a port: https://render.com/docs/web-services#port-binding
```

**Ce que ça signifie** : Render démarre le conteneur, ne trouve aucun port qui écoute,
ne peut donc pas router le trafic vers l'application.

## 2. Les causes (diagnostic)

Trois problèmes s'enchaînaient :

1. **Mauvais port** — le serveur PHP était lancé en dur sur le port `80`,
   alors que Render fournit un port dynamique via la variable d'environnement `$PORT`
   (généralement `10000`).
2. **`entrypoint.sh` bloquait indéfiniment** — le script d'initialisation attendait
   une base de données en boucle infinie. Comme aucune base n'était joignable sur Render,
   `php -S` n'était **jamais lancé** → aucun port ouvert → Render abandonnait.
3. **Code écrit uniquement pour MySQL** — Render propose du PostgreSQL natif.
   Les migrations, le DSN et le script d'attente de la base étaient incompatibles.

## 3. Les corrections apportées

### 3.1 Écoute sur le port fourni par Render — `Dockerfile`

```dockerfile
# AVANT :
CMD ["php", "-S", "0.0.0.0:80", "-t", "public", "public/index.php"]

# APRÈS :
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-80} -t public public/index.php"]
```

L'application écoute donc sur `$PORT` (ou `80` en local via docker-compose).

### 3.2 Compatibilité PostgreSQL (tout en gardant MySQL en local)

| Fichier | Changement |
|---|---|
| `Dockerfile` | Ajout de `libpq-dev` et des extensions `pgsql` / `pdo_pgsql` |
| `config/database.php` | Charset/collation adaptés selon le pilote (`utf8` pour pg, `utf8mb4` pour mysql) |
| `database/migrer.php` | `SET FOREIGN_KEY_CHECKS` appliqué uniquement sur MySQL |
| `database/migrations/*.php` | `$table->enum()` remplacé par `$table->string()` (Postgres ne gère pas `enum` nativement) |
| `docker/attend_db.php` | DSN construit selon le pilote, avec `dbname=` obligatoire pour PostgreSQL |
| `.env.example` | Commentaires indiquant les valeurs Render |

Le choix entre les deux bases se fait avec la variable `DB_DRIVER` :
- **En local** (`docker-compose.yml`) → `DB_DRIVER=mysql`
- **Sur Render** (`render.yaml`) → `DB_DRIVER=pgsql`

### 3.3 Démarrage fiable — `docker/entrypoint.sh`

**Avant** (bloquant, silencieux) :
```sh
until php attend_db.php ...; do echo "Attente de MySQL..."; sleep 2; done
```

**Après** :
- Erreur immédiate si `DB_HOST` est absent.
- Attente bornée (30 × 2 s = 60 s max) sur la base.
- Si la base est injoignable : message d'erreur clair et `exit 1`
  (au lieu de boucler à l'infini sans aucun log exploitable).
- Migrations + seed appliqués uniquement lorsque la base est prête.

### 3.4 Fichier de configuration Render — `render.yaml`

Blueprint permettant de recréer l'ensemble automatiquement :

- un **Web Service** (runtime docker) `reservation-salles`
- une base **PostgreSQL** `reservation-db`
- les variables `DB_*` reliées automatiquement entre les deux.

## 4. Vérifications effectuées avant la mise en production

Toute la chaîne a été testée en local avec Docker (simulation de Render) :

1. Build de l'image : `docker build`
2. Lancement avec `PORT` forcé → le serveur écoute sur `$PORT` → `HTTP 200`
3. Postgres de test lancé à côté :
   - migrations appliquées (`salles`, `reservations`) ✓
   - seed : 5 salles insérées ✓
   - `/salles` et `/reservations` → `HTTP 200` ✓

## 5. Déploiement réel sur Render (via l'API)

Avec une clé API Render (`Account Settings → API Keys`) :

1. Récupération des services existants (GET `/services`).
2. Utilisation de la base PostgreSQL existante `reservation_db`
   (infos récupérées via GET `/postgres/{id}` et `/postgres/{id}/connection-info`).
3. Ajout des variables d'environnement sur chaque service web
   (PUT `/services/{id}/env-vars/{key}`) :

   ```
   APP_ENV=production
   APP_DEBUG=false
   DB_DRIVER=pgsql
   DB_HOST=dpg-...-a.oregon-postgres.render.com
   DB_PORT=5432
   DB_DATABASE=reservation_db_y20k
   DB_USERNAME=reservation_db_y20k_user
   DB_PASSWORD=...
   ```

4. Redéploiement déclenché (POST `/services/{id}/deploys`)
   → statut passé de `build_in_progress` à `live`.

## 6. Résultat final

- ✔ Application en ligne : **https://depot-reservation.onrender.com**
- ✔ `/` → HTTP 200
- ✔ `/salles` → HTTP 200 (salles lues depuis PostgreSQL)
- ✔ Démarrage automatisé : base → migrations → seed → serveur PHP sur `$PORT`

## 7. Pour redéployer plus tard

Le plus simple : pousser un commit sur `main` — Render redéploie automatiquement
(`autoDeploy` activé).

```bash
git add -A
git commit -m "ma modif"
git push origin main
```

Sinon, depuis Render (dashboard) : bouton **Manual Deploy → Deploy latest commit**.